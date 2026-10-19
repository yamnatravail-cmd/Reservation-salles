<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DemandeReservation;
use App\Models\Paiement;
use App\Models\Tva;

class FactureReservation extends Model
{
    protected $fillable = [
        'demande_id',
        'date_emission',
        'montant_total',
        'status',
        'montant_paye',
        'reste_a_payer',
        'tva_id',
    ];

    public function demande()
    {
        return $this->belongsTo(DemandeReservation::class);
    }

    public function tva()
    {
        return $this->belongsTo(Tva::class);
    }

    public function paiements()
    {
        return $this->morphMany(Paiement::class, 'facturable');
    }
}
