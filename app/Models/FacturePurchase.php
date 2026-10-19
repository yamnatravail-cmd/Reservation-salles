<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturePurchase extends Model
{
    protected $fillable = [
        'purchase_id',
        'date_emission',
        'statut',
        'tva_id',
        'montant_total',
        'amount_paid',
        'amount_remaining',
    ];

    
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    
    public function tva()
    {
        return $this->belongsTo(Tva::class, 'tva_id');
    }

public function paiements()
{
    return $this->morphMany(Paiement::class, 'facturable');
}



}
