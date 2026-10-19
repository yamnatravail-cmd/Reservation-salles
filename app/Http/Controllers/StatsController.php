<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Salle;
use App\Models\Evenement;
use App\Models\Paiement;
use App\Models\DemandeReservation;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->query('year', date('Y'));

        $clientsCount = Client::count();
        $sallesCount = Salle::count();
        $evenementsCount = Evenement::count();

        $paiements = Paiement::selectRaw('origine, SUM(montant) as total')
            ->groupBy('origine')
            ->get()
            ->pluck('total', 'origine');

        $reservationsByMonth = DemandeReservation::whereYear('date_debut', $year)
            ->selectRaw('MONTH(date_debut) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month');

        $monthlyCounts = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyCounts[$m] = $reservationsByMonth->get($m, 0);
        }

        return response()->json([
            'clientsCount' => $clientsCount,
            'sallesCount' => $sallesCount,
            'evenementsCount' => $evenementsCount,
            'paiements' => $paiements,
            'reservationsByMonth' => array_values($monthlyCounts),
        ]);
    }

    public function clientsCategoryDistribution()
    {
        $clients = Client::all();

        $distribution = [
            'vip' => 0,
            'regular' => 0,
            'new' => 0,
        ];

        foreach ($clients as $client) {
            $count = DemandeReservation::where('client_id', $client->id)->count();

            if ($count > 10) {
                $distribution['vip']++;
            } elseif ($count >= 3) {
                $distribution['regular']++;
            } else {
                $distribution['new']++;
            }
        }

        return response()->json($distribution);
    }

    public function sallesCategoryDistribution()
    {
        $salleIds = DemandeReservation::select('salle_id')->distinct()->pluck('salle_id');
        $salles = Salle::whereIn('id', $salleIds)->get();

        $distribution = [
            'grande' => 0,
            'moyenne' => 0,
            'petite' => 0,
        ];

        foreach ($salles as $salle) {
            if ($salle->capacite >= 100) {
                $distribution['grande']++;
            } elseif ($salle->capacite >= 50) {
                $distribution['moyenne']++;
            } else {
                $distribution['petite']++;
            }
        }

        return response()->json($distribution);
    }
}
