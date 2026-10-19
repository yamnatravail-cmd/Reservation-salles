<?php

namespace App\Http\Controllers;

use App\Models\Evenement;
use Illuminate\Http\Request;

class EvenementController extends Controller
{
    public function index()
    {
        $evenements = Evenement::all();
        return response()->json($evenements);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $evenement = Evenement::create($validated);
        return response()->json($evenement, 201);
    }

    public function show(Evenement $evenement)
    {
        return response()->json($evenement);
    }

    public function update(Request $request, Evenement $evenement)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $evenement->update($validated);
        return response()->json($evenement);
    }

    public function destroy(Evenement $evenement)
    {
        $evenement->delete();
        return response()->json(null, 204);
    }
}
