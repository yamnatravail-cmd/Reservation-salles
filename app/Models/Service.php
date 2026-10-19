<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;
use App\Models\Salle;

class Service extends Model
{
    protected $fillable = ['produit_id', 'salle_id'];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }


    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }
}
