<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;
use App\Models\Purchase;

class PurchaseLine extends Model
{
   protected $fillable = ['purchase_id', 'produit_id', 'quantite', 'prix_unitaire'];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function achat()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }
}
