<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use Illuminate\Http\Request;
use App\Models\FactureReservation;
use App\Models\FacturePurchase;

class PaiementController extends Controller
{
    public function index()
    {
        $paiements = Paiement::with('facturable')->get();
        return response()->json($paiements);
    }

    public function store(Request $request)
    {
        $rules = [
            'montant' => 'required|numeric|min:1',
            'statut' => 'required|string',
            'date_paiement' => 'required|date',
            'facture_id' => 'nullable|integer',
            'type' => 'required|in:reservation,purchase',
        ];

        if ($request->input('type') === 'reservation') {
            $rules['facture_id'] = 'required|exists:facture_reservations,id';
        } elseif ($request->input('type') === 'purchase') {
            $rules['facture_id'] = 'required|exists:facture_purchases,id';
        }

        $validated = $request->validate($rules);

        $facturableType = null;
        $facturableId = null;

        if ($validated['type'] === 'reservation') {
            $facturableType = FactureReservation::class;
        } elseif ($validated['type'] === 'purchase') {
            $facturableType = FacturePurchase::class;
        }

        $facturableId = $validated['facture_id'];

        $paiement = Paiement::create([
            'montant' => $validated['montant'],
            'statut' => $validated['statut'],
            'date_paiement' => $validated['date_paiement'],
            'origine' => $validated['type'],
            'facturable_type' => $facturableType,
            'facturable_id' => $facturableId,
        ]);

        if ($validated['type'] === 'reservation') {
            $facture = FactureReservation::find($facturableId);
            $facture->montant_paye += $validated['montant'];
            $facture->reste_a_payer = $facture->montant_total - $facture->montant_paye;

            if ($facture->reste_a_payer <= 0) {
                $facture->reste_a_payer = 0;
                $facture->status = 'payée';
            }

            $facture->save();
        } elseif ($validated['type'] === 'purchase') {
            $facture = FacturePurchase::find($facturableId);
            $facture->amount_paid += $validated['montant'];
            $facture->amount_remaining = $facture->montant_total - $facture->amount_paid;

            if ($facture->amount_remaining <= 0) {
                $facture->amount_remaining = 0;
                $facture->statut = 'payée';
            }

            $facture->save();
        }

        return response()->json($paiement, 201);
    }

    public function show(Paiement $paiement)
    {
        return response()->json($paiement->load('facturable'));
    }

    public function update(Request $request, Paiement $paiement)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:1',
            'statut' => 'required|string',
            'date_paiement' => 'required|date',
            'facture_id' => 'nullable|integer',
            'type' => 'required|in:reservation,purchase',
        ]);

        if ($validated['facture_id']) {
            if ($validated['type'] === 'reservation') {
                $facture = FactureReservation::find($validated['facture_id']);
                $ancienMontant = $paiement->montant;
                $diff = $validated['montant'] - $ancienMontant;

                if ($diff > 0 && $facture->reste_a_payer < $diff) {
                    return response()->json([
                        'error' => 'Le montant dépasse le reste à payer. Il reste seulement ' . $facture->reste_a_payer . ' DH.'
                    ], 422);
                }

                $facture->montant_paye += $diff;
                $facture->reste_a_payer = $facture->montant_total - $facture->montant_paye;

                if ($facture->reste_a_payer <= 0) {
                    $facture->reste_a_payer = 0;
                    $facture->status = 'payée';
                }

                $facture->save();
            } elseif ($validated['type'] === 'purchase') {
                $facture = FacturePurchase::find($validated['facture_id']);
                $ancienMontant = $paiement->montant;
                $diff = $validated['montant'] - $ancienMontant;

                if ($diff > 0 && $facture->amount_remaining < $diff) {
                    return response()->json([
                        'error' => 'Le montant dépasse le reste à payer. Il reste seulement ' . $facture->amount_remaining . ' DH.'
                    ], 422);
                }

                $facture->amount_paid += $diff;
                $facture->amount_remaining = $facture->montant_total - $facture->amount_paid;

                if ($facture->amount_remaining <= 0) {
                    $facture->amount_remaining = 0;
                    $facture->statut = 'payée';
                }

                $facture->save();
            }
        }

        $paiement->update([
            'montant' => $validated['montant'],
            'statut' => $validated['statut'],
            'date_paiement' => $validated['date_paiement'],
            'origine' => $validated['type'],
            'facturable_type' => $validated['type'] === 'reservation' ? FactureReservation::class : FacturePurchase::class,
            'facturable_id' => $validated['facture_id'] ?? null,
        ]);

        return response()->json($paiement);
    }

    public function destroy(Paiement $paiement)
    {
        $paiement->delete();
        return response()->json(null, 204);
    }
}
