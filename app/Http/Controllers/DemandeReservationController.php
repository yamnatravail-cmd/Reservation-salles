<?php

namespace App\Http\Controllers;

use App\Models\DemandeReservation;
use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Evenement;
use App\Models\Client;
use App\Models\Salle;
use App\Models\Service;
use App\Models\Tva;  


class DemandeReservationController extends Controller
{
    public function index()
    {
        $demandes = DemandeReservation::all();
        return response()->json($demandes);
    }

 public function store(Request $request)
{
    $validated = $request->validate([
        'client_id' => 'required|exists:clients,id',
        'evenement_id' => 'required|exists:evenements,id',
        'salle_id' => 'required|exists:salles,id',
        'statut' => 'required|string',
        'date_debut' => 'required|date',
        'date_fin' => 'required|date|after_or_equal:date_debut',
        'services_choisis' => 'nullable|array',
        'services_choisis.*' => 'integer|exists:services,id',
        'prix' => 'required|numeric|min:0',
    ]);

   
    $exists = DemandeReservation::where('salle_id', $validated['salle_id'])
        ->where(function ($query) use ($validated) {
            $query->whereBetween('date_debut', [$validated['date_debut'], $validated['date_fin']])
                ->orWhereBetween('date_fin', [$validated['date_debut'], $validated['date_fin']])
                ->orWhere(function ($query2) use ($validated) {
                    $query2->where('date_debut', '<=', $validated['date_debut'])
                        ->where('date_fin', '>=', $validated['date_fin']);
                });
        })->exists();

    if ($exists) {
        return response()->json(['error' => 'Cette salle est déjà réservée durant cette période.'], 422);
    }

    $demande = DemandeReservation::create([
        'client_id' => $validated['client_id'],
        'evenement_id' => $validated['evenement_id'],
        'salle_id' => $validated['salle_id'],
        'statut' => $validated['statut'],
        'date_debut' => $validated['date_debut'],
        'date_fin' => $validated['date_fin'],
        'services_choisis' => $validated['services_choisis'] ?? [],
        'prix' => $validated['prix'],
    ]);

    return response()->json($demande, 201);
}


    public function show(DemandeReservation $demande)
    {
        return response()->json($demande);
    }

  public function update(Request $request, DemandeReservation $demande)
{
    $validated = $request->validate([
        'client_id' => 'required|exists:clients,id',
        'evenement_id' => 'required|exists:evenements,id',
        'salle_id' => 'required|exists:salles,id',
        'statut' => 'required|string',
        'date_debut' => 'required|date',
        'date_fin' => 'required|date|after_or_equal:date_debut',
        'services_choisis' => 'nullable|array',
        'services_choisis.*' => 'integer|exists:services,id',
        'prix' => 'required|numeric|min:0',
    ]);

    $exists = DemandeReservation::where('salle_id', $validated['salle_id'])
        ->where('id', '!=', $demande->id)
        ->where(function ($query) use ($validated) {
            $query->whereBetween('date_debut', [$validated['date_debut'], $validated['date_fin']])
                ->orWhereBetween('date_fin', [$validated['date_debut'], $validated['date_fin']])
                ->orWhere(function ($query2) use ($validated) {
                    $query2->where('date_debut', '<=', $validated['date_debut'])
                        ->where('date_fin', '>=', $validated['date_fin']);
                });
        })->exists();

    if ($exists) {
        return response()->json(['error' => 'Cette salle est déjà réservée durant cette période.'], 422);
    }

    $demande->update([
        'client_id' => $validated['client_id'],
        'evenement_id' => $validated['evenement_id'],
        'salle_id' => $validated['salle_id'],
        'statut' => $validated['statut'],
        'date_debut' => $validated['date_debut'],
        'date_fin' => $validated['date_fin'],
        'services_choisis' => $validated['services_choisis'] ?? [],
        'prix' => $validated['prix'],
    ]);

    return response()->json($demande);
}


    public function destroy(DemandeReservation $demande)
    {
        $demande->delete();
        return response()->json(null, 204);
    }
    public function createR()
    {
         $produits = Produit::all();
          $salles = Salle::all();
          $evenements = Evenement::all();
          $clients = Client::all();
           $tvas = Tva::all();
          $services = Service::with(['produit', 'salle'])->get();
        return response()->json([$produits,$salles,$evenements, $clients, $services, $tvas]);

}
public function allWithRelations()
{
    $demandes = DemandeReservation::with(['client', 'salle', 'evenement'])->get();
    $clients = Client::select('id', 'nom', 'Prenom')->get();
    $salles = Salle::select('id', 'nom')->get();

    return response()->json([
        'demandes' => $demandes,
        'clients' => $clients,
        'salles' => $salles,
    ]);
}


public function infos()
{
    try {
        $demandes = DemandeReservation::with('client')
            ->orderByDesc('date_debut')
            ->get();

        $salles = Salle::all();
        $evenements = Evenement::all();
        $clients = Client::all(); 
        $services = Service::all();
        $produits = Produit::all();

        return response()->json([
            'demandes' => $demandes,
            'salles' => $salles,
            'evenements' => $evenements,
            'clients' => $clients, 
            'services' => $services,
            'produits' => $produits,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
public function confirmer($id)
{
    $demande = DemandeReservation::findOrFail($id);
    $demande->statut = 'confirmée';
    $demande->save();

    return response()->json(['message' => 'Réservation confirmée avec succès']);
}

public function getReservationData($reservationId)
{
    $reservation = DemandeReservation::findOrFail($reservationId);

    $client = Client::find($reservation->client_id);
    $salle = Salle::find($reservation->salle_id);
    $evenement = Evenement::find($reservation->evenement_id);

    $clientsList = Client::all();
    $sallesList = Salle::all();
    $evenementsList = Evenement::all();
    $servicesList = Service::all();
    $produitsList = Produit::all();

    return response()->json([
        'reservation' => $reservation,
        'client' => $client,
        'salle' => $salle,
        'evenement' => $evenement,
        'clientsList' => $clientsList,
        'sallesList' => $sallesList,
        'evenementsList' => $evenementsList,
        'servicesList' => $servicesList,
        'produitsList' => $produitsList,]);
}





public function getClientReservations($id)
{
    $client = Client::find($id);
    if (!$client) {
        return response()->json(['message' => 'Client introuvable'], 404);
    }

    $reservations = DemandeReservation::where('client_id', $id)->get();
    $salles = Salle::all();
    $evenements = Evenement::all();
    $services = Service::all(); 

    
    $reservations->transform(function ($reservation) use ($services) {
        $serviceNames = [];

        if (is_array($reservation->services_choisis)) {
            $serviceNames = $services
                ->whereIn('id', $reservation->services_choisis)
                ->pluck('nom')
                ->toArray();
        }

        $reservation->services_choisis_noms = $serviceNames; 
        return $reservation;
    });

    return response()->json([
        'client' => $client,
        'reservations' => $reservations,
        'salles' => $salles,
        'evenements' => $evenements
    ]);
}






}
