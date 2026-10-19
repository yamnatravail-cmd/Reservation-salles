<?php

namespace App\Http\Controllers;

use App\Models\FactureReservation;
use App\Models\DemandeReservation;
use App\Models\Tva;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FactureReservationController extends Controller
{
    // جلب كل الفواتير مع بيانات الطلب والـ TVA والدفعات
    public function index()
    {
        $factures = FactureReservation::with('demande.client', 'demande.salle', 'tva', 'paiements')->get();
        return response()->json($factures);
    }

    // إنشاء فاتورة جديدة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'demande_id' => 'required|exists:demande_reservations,id',
            'date_emission' => 'required|date',
            'status' => 'required|string',
            'tva_id' => 'required|exists:tvas,id',
        ]);

        $demande = DemandeReservation::findOrFail($validated['demande_id']);
        $dateDebut = Carbon::parse($demande->date_debut);
        $dateFin = Carbon::parse($demande->date_fin);
        $nombreJours = $dateDebut->diffInDays($dateFin) + 1;

        $prixParJour = $demande->prix;
        $montantSansTva = $prixParJour * $nombreJours;

        $tva = Tva::findOrFail($validated['tva_id']);
        $tvaAmount = ($montantSansTva * $tva->pourcentage) / 100;
        $montantTotalAvecTva = $montantSansTva + $tvaAmount;

        $facture = FactureReservation::create([
            'demande_id' => $validated['demande_id'],
            'date_emission' => $validated['date_emission'],
            'status' => $validated['status'],
            'tva_id' => $validated['tva_id'],
            'montant_total' => $montantTotalAvecTva,
            'montant_paye' => 0,
            'reste_a_payer' => $montantTotalAvecTva,
        ]);

        
        $montantPaiement = $request->input('premier_paiement', 0);
        $paiement = null;

        if ($montantPaiement > 0) {
            $paiementController = new PaiementController();
            $paiementRequest = new Request([
                'montant' => $montantPaiement,
                'statut' => 'pending',
                'date_paiement' => now(),
                'facture_id' => $facture->id,
                'type' => 'reservation',
            ]);
            $paiementResponse = $paiementController->store($paiementRequest);
            $paiement = $paiementResponse->getData();

            $facture->montant_paye = $montantPaiement;
            $facture->reste_a_payer = $facture->montant_total - $montantPaiement;
            $facture->save();
        }

        return response()->json([
            'facture' => $facture,
            'paiement' => $paiement,
        ], 201);
    }

    
   public function show($id)
{
 $facture = FactureReservation::with('demande.client', 'demande.salle', 'tva', 'paiements')->find($id);

    if (!$facture) {
        return response()->json(['error' => 'الفاتورة غير موجودة'], 404);
    }

    return response()->json($facture);
}

    
public function update(Request $request, FactureReservation $factureReservation)
{
    $validated = $request->validate([
        'demande_id' => 'required|exists:demande_reservations,id',
        'date_emission' => 'required|date',
        'montant_total' => 'required|numeric',
        'status' => 'required|string',
        'tva_id' => 'required|exists:tvas,id',
    ]);

    $tva = Tva::findOrFail($validated['tva_id']);
    $tvaAmount = ($validated['montant_total'] * $tva->pourcentage) / 100;
    $montantTotalWithTva = $validated['montant_total'] + $tvaAmount;

    // تحويل التاريخ لتنسيق MySQL المقبول
    $dateEmissionFormatted = Carbon::parse($validated['date_emission'])->format('Y-m-d H:i:s');

    $factureReservation->update([
        'demande_id' => $validated['demande_id'],
        'date_emission' => $dateEmissionFormatted,
        'status' => $validated['status'],
        'tva_id' => $validated['tva_id'],
        'montant_total' => $montantTotalWithTva,
    ]);

    $factureReservation->reste_a_payer = $montantTotalWithTva - $factureReservation->montant_paye;
    $factureReservation->save();

    return response()->json($factureReservation);
}


    
    public function destroy(FactureReservation $factureReservation)
    {
        $factureReservation->delete();
        return response()->json(null, 204);
    }

    
public function PDF($id)
{
    $facture = FactureReservation::with('demande.salle', 'tva')->findOrFail($id);

    return \Barryvdh\DomPDF\Facade\Pdf::loadView('facture', compact('facture'))
              ->download('facture_' . $facture->id . '.pdf');
}

public function ajouterPaiement(Request $request, $id)
{
    $facture = FactureReservation::with('paiements')->findOrFail($id);

    $validated = $request->validate([
        'montant' => 'required|numeric|min:0.01',
        'statut' => 'required|string',
        'date_paiement' => 'required|date',
    ]);

    
    $paiement = $facture->paiements()->create([
        'montant' => $validated['montant'],
        'statut' => $validated['statut'],
        'date_paiement' => $validated['date_paiement'],
        'type' => 'reservation',
    ]);

    
    $facture->montant_paye += $validated['montant'];
    $facture->reste_a_payer = $facture->montant_total - $facture->montant_paye;
    $facture->save();

    return response()->json([
        'facture' => $facture,
        'paiement' => $paiement,
    ]);
}


}
