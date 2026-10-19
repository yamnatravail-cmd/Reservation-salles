<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::all();
        return response()->json($produits);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric',
            'type' => 'required|string|max:255',
        ]);

        $produit = Produit::create($validated);
        return response()->json($produit, 201);
    }

    public function show(Produit $produit)
    {
        return response()->json($produit);
    }

    public function update(Request $request, Produit $produit)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric',
            'type' => 'required|string|max:255',
        ]);

        $produit->update($validated);
        return response()->json($produit);
    }

    public function destroy(Produit $produit)
    {
        $produit->delete();
        return response()->json(null, 204);
    }
}
