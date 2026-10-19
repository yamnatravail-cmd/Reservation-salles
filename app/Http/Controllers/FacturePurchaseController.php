<?php
namespace App\Http\Controllers;

use App\Models\FacturePurchase;
use App\Models\Purchase;
use App\Models\Tva;
use Illuminate\Http\Request;
use PDF;

class FacturePurchaseController extends Controller
{
    public function index()
    {
        $factures = FacturePurchase::with('purchase', 'tva', 'paiements')->get();
        return response()->json($factures);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => 'nullable|exists:purchases,id',
            'date_emission' => 'nullable|date',
            'statut' => 'nullable|string',
            'tva_id' => 'nullable|exists:tvas,id',
            'montant_total' => 'nullable|numeric',
            'premier_paiement' => 'nullable|numeric|min:0',
        ]);

        $montantSansTva = $validated['montant_total'] ?? 0;
        $montantTotalAvecTva = $montantSansTva;

        if (!empty($validated['tva_id'])) {
            $tva = Tva::findOrFail($validated['tva_id']);
            $tvaAmount = ($montantSansTva * $tva->pourcentage) / 100;
            $montantTotalAvecTva += $tvaAmount;
        }

        $facture = FacturePurchase::create([
            'purchase_id' => $validated['purchase_id'] ?? null,
            'date_emission' => $validated['date_emission'] ?? null,
            'statut' => $validated['statut'] ?? null,
            'tva_id' => $validated['tva_id'] ?? null,
            'montant_total' => $montantTotalAvecTva,
            'amount_paid' => 0,
            'amount_remaining' => $montantTotalAvecTva,
        ]);

        $montantPaiement = $validated['premier_paiement'] ?? 0;
        $paiement = null;

        if ($montantPaiement > 0) {
            $paiementController = new PaiementController();
            $paiementRequest = new Request([
                'montant' => $montantPaiement,
                'statut' => 'pending',
                'date_paiement' => now()->toDateString(),
                'facture_id' => $facture->id,
                'type' => 'purchase',
            ]);
            $paiementResponse = $paiementController->store($paiementRequest);
            $paiement = $paiementResponse->getData();

            $facture->amount_paid = $montantPaiement;
            $facture->amount_remaining = $facture->montant_total - $montantPaiement;
            $facture->save();
        }

        return response()->json([
            'facture' => $facture,
            'paiement' => $paiement,
        ], 201);
    }

    public function show($id)
    {
        $facture = FacturePurchase::with('purchase', 'tva', 'paiements')->find($id);

        if (!$facture) {
            return response()->json(['error' => 'le facture pas existe'], 404);
        }

        return response()->json($facture);
    }

    public function update(Request $request, FacturePurchase $facturePurchase)
    {
        $validated = $request->validate([
            'purchase_id' => 'nullable|exists:purchases,id',
            'date_emission' => 'nullable|date',
            'montant_total' => 'nullable|numeric',
            'statut' => 'nullable|string',
            'tva_id' => 'nullable|exists:tvas,id',
        ]);

        $montantSansTva = $validated['montant_total'] ?? 0;
        $montantTotalAvecTva = $montantSansTva;

        if (!empty($validated['tva_id'])) {
            $tva = Tva::findOrFail($validated['tva_id']);
            $tvaAmount = ($montantSansTva * $tva->pourcentage) / 100;
            $montantTotalAvecTva += $tvaAmount;
        }

        $facturePurchase->update([
            'purchase_id' => $validated['purchase_id'] ?? null,
            'date_emission' => $validated['date_emission'] ?? null,
            'statut' => $validated['statut'] ?? null,
            'tva_id' => $validated['tva_id'] ?? null,
            'montant_total' => $montantTotalAvecTva,
        ]);

        $facturePurchase->amount_remaining = $montantTotalAvecTva - $facturePurchase->amount_paid;
        $facturePurchase->save();

        return response()->json($facturePurchase);
    }

    public function destroy(FacturePurchase $facturePurchase)
    {
        $facturePurchase->delete();
        return response()->json(null, 204);
    }


    public function ajouterPaiement(Request $request, $id)
    {
        $facture = FacturePurchase::with('paiements')->findOrFail($id);

        $validated = $request->validate([
            'montant' => 'required|numeric|min:0.01',
            'statut' => 'required|string',
            'date_paiement' => 'required|date',
            'type' => 'required|string',
        ]);

        $paiement = $facture->paiements()->create([
            'montant' => $validated['montant'],
            'statut' => $validated['statut'],
            'date_paiement' => $validated['date_paiement'],
            'type' => $validated['type'],
        ]);

        $facture->amount_paid += $validated['montant'];
        $facture->amount_remaining = $facture->montant_total - $facture->amount_paid;
        $facture->save();

        return response()->json([
            'facture' => $facture,
            'paiement' => $paiement,
        ]);
    }

    public function downloadPDF($id)
    {
        $facture = FacturePurchase::with([
            'purchase.fournisseur',
            'purchase.produits' => function ($query) {
                $query->withPivot('quantite', 'prix_unitaire');
            },
            'tva'
        ])->findOrFail($id);

        $pdf = PDF::loadView('factures.purchase_pdf', compact('facture'));

        return $pdf->download("facture_achat_{$id}.pdf");
    }
}
