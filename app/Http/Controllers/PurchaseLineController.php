<?php

namespace App\Http\Controllers;

use App\Models\PurchaseLine;
use Illuminate\Http\Request;

class PurchaseLineController extends Controller
{
    public function index()
    {
        $purchaseLines = PurchaseLine::all();
        return response()->json($purchaseLines);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer',
        ]);

        $purchaseLine = PurchaseLine::create($validated);
        return response()->json($purchaseLine, 201);
    }

    public function show(PurchaseLine $purchaseLine)
    {
        return response()->json($purchaseLine);
    }

    public function update(Request $request, PurchaseLine $purchaseLine)
    {
        $validated = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer',
        ]);

        $purchaseLine->update($validated);
        return response()->json($purchaseLine);
    }

    public function destroy(PurchaseLine $purchaseLine)
    {
        $purchaseLine->delete();
        return response()->json(null, 204);
    }
}
