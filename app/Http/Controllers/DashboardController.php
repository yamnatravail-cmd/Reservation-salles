<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $data = Cache::remember('dashboard.data', 60, function () {
            return [
                'clients_count' => \App\Models\Client::count(),
                'facture_reservations_count' => \App\Models\FactureReservation::count(),
                'facture_purchases_count' => \App\Models\FacturePurchase::count(),
                'total_paiements' => \App\Models\Paiement::sum('montant'),
                'demande_reservations_count' => \App\Models\DemandeReservation::count(),
                'recent_factures' => \App\Models\FacturePurchase::latest()->take(5)->get(),
                'paiements_by_month' => \App\Models\Paiement::selectRaw('MONTH(date_paiement) as month, SUM(montant) as total')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get(),
                // ممكن تزيد بيانات أخرى حسب الحاجة
                'paiements' => \App\Models\Paiement::all(),
                'demandes' => \App\Models\DemandeReservation::all(),
                'facture_purchases' => \App\Models\FacturePurchase::all(),
                'facture_reservations' => \App\Models\FactureReservation::all(),
                'salles' => \App\Models\Salle::all(),
            ];
        });

        return response()->json($data);
    }
}
