<?php

namespace App\Http\Controllers;

use App\Models\Tva;
use Illuminate\Http\Request;

class TvaController extends Controller
{
    public function index()
    {
        $tvas = Tva::all();
        return response()->json($tvas);
    }

    public function store(Request $request)
    {
        $tva = Tva::create($request->all());
        return response()->json($tva, 201);
    }

    public function show($id)
    {
        $tva = Tva::findOrFail($id);
        return response()->json($tva);
    }

    public function update(Request $request, $id)
    {
        $tva = Tva::findOrFail($id);
        $tva->update($request->all());
        return response()->json($tva);
    }

    public function destroy($id)
    {
        Tva::destroy($id);
        return response()->json(null, 204);
    }
}
