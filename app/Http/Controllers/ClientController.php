<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * إرجاع قائمة جميع العملاء (API).
     */
    public function index()
    {
        $clients = Client::all();
        return response()->json($clients);
    }

    /**
     * تخزين عميل جديد.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'Prenom' => 'required|string|max:255',
            'mobel' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'telephone' => 'required|string|max:20',
        ]);

        $client = Client::create($validated);

        return response()->json([
            'message' => 'Client created successfully!',
            'client' => $client
        ], 201);
    }

    /**
     * عرض تفاصيل عميل واحد.
     */
    public function show(Client $client)
    {
        return response()->json($client);
    }

    /**
     * تعديل بيانات عميل.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'Prenom' => 'required|string|max:255',
            'mobel' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'telephone' => 'required|string|max:20',
        ]);

        $client->update($validated);

        return response()->json([
            'message' => 'Client updated successfully!',
            'client' => $client
        ]);
    }

  
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(['message' => 'Client deleted successfully!']);
    }
}
