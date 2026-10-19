<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'montant',
        'statut',
        'date_paiement',
        'origine',
        'facturable_id',
        'facturable_type',
    ];

   
    public function facturable()
    {
        return $this->morphTo();
    }
}
