<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Fournisseur;
use App\Models\PurchaseLine;
use App\Models\FacturePurchase;
use App\Models\Produit;

class Purchase extends Model
{
    protected $fillable = ['fournisseur_id', 'date_achat'];

    
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    
    public function lignes()
    {
        return $this->hasMany(PurchaseLine::class);
    }

   
    public function facture()
    {
        return $this->hasOne(FacturePurchase::class);
    }

public function produits()
{
    return $this->belongsToMany(Produit::class, 'purchase_lines')
        ->withPivot('quantite', 'prix_unitaire')
        ->withTimestamps();
}

}
