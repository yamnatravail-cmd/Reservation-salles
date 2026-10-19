<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;
class CategoryProduit extends Model
{
    protected $fillable = ['nom', 'produit_id'];

    // علاقة ينتمي إلى Produit
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}

