<?php namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseLine;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    
    public function index()
    {
        $purchases = Purchase::with('lignes', 'fournisseur')->get();
        return response()->json($purchases);
    }

    
    public function show($id)
    {
        $purchase = Purchase::with('lignes', 'fournisseur')->find($id);
        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }
        return response()->json($purchase);
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate([
    'fournisseur_id' => 'required|exists:fournisseurs,id',
    'date_achat' => 'required|date',
    'purchase_lines' => 'required|array',
    'purchase_lines.*.produit_id' => 'required|exists:produits,id',
    'purchase_lines.*.quantite' => 'required|integer',
    'purchase_lines.*.prix_unitaire' => 'required|numeric|min:0',
]);


        $purchase = Purchase::create([
            'fournisseur_id' => $validated['fournisseur_id'],
            'date_achat' => $validated['date_achat'],
        ]);

      
            foreach ($validated['purchase_lines'] as $line) {
    PurchaseLine::create([
        'purchase_id' => $purchase->id,
        'produit_id' => $line['produit_id'],
        'quantite' => $line['quantite'],
        'prix_unitaire' => $line['prix_unitaire'], 
    ]);
}

        

        return response()->json($purchase, 201);
    }

    // Modifier un achat avec lignes
   public function update(Request $request, $id)
{
    $validated = $request->validate([
        'fournisseur_id' => 'required|exists:fournisseurs,id',
        'date_achat' => 'required|date',
        'purchase_lines' => 'required|array',
        'purchase_lines.*.produit_id' => 'required|exists:produits,id',
        'purchase_lines.*.quantite' => 'required|integer',
        'purchase_lines.*.prix_unitaire' => 'required|numeric|min:0',
    ]);

    $purchase = Purchase::find($id);
    if (!$purchase) {
        return response()->json(['message' => 'Purchase not found'], 404);
    }

    $purchase->update([
        'fournisseur_id' => $validated['fournisseur_id'],
        'date_achat' => $validated['date_achat'],
    ]);

    // Supprimer anciennes lignes
    $purchase->lignes()->delete();

    // Ajouter nouvelles lignes
    foreach ($validated['purchase_lines'] as $line) {
        PurchaseLine::create([
            'purchase_id' => $purchase->id,
            'produit_id' => $line['produit_id'],
            'quantite' => $line['quantite'],
            'prix_unitaire' => $line['prix_unitaire'],
        ]);
    }

    return response()->json($purchase);
}


    // Supprimer un achat
    public function destroy($id)
    {
        $purchase = Purchase::find($id);
        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }

        $purchase->lignes()->delete();
        $purchase->delete();

        return response()->json(['message' => 'Purchase deleted successfully'], 204);
    }
}
