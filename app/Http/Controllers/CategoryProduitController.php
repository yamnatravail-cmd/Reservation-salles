<?php

namespace App\Http\Controllers;

use App\Models\CategoryProduit;
use Illuminate\Http\Request;

class CategoryProduitController extends Controller
{
    public function index()
    {
        $categories = CategoryProduit::all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'produit_id' => 'required|exists:produits,id',
        ]);

        $category = CategoryProduit::create($validated);
        return response()->json($category, 201);
    }

    public function show(CategoryProduit $categoryProduit)
    {
        return response()->json($categoryProduit);
    }

    public function update(Request $request, CategoryProduit $categoryProduit)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'produit_id' => 'required|exists:produits,id',
        ]);

        $categoryProduit->update($validated);
        return response()->json($categoryProduit);
    }

    public function destroy(CategoryProduit $categoryProduit)
    {
        $categoryProduit->delete();
        return response()->json(null, 204);
    }
}
