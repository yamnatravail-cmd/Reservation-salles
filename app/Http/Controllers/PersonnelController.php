<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index()
    {
        $personnels = Personnel::all();
        return response()->json($personnels);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
        ]);

        $personnel = Personnel::create($validated);
        return response()->json($personnel, 201);
    }

    public function show(Personnel $personnel)
    {
        return response()->json($personnel);
    }

    public function update(Request $request, Personnel $personnel)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
        ]);

        $personnel->update($validated);
        return response()->json($personnel);
    }

    public function destroy(Personnel $personnel)
    {
        $personnel->delete();
        return response()->json(null, 204);
    }
}
