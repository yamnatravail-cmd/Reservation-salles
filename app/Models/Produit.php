<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PurchaseLine;

class Produit extends Model
{
    protected $fillable = ['nom', 'prix', 'type']; // مثال على الحقول

    // خطوط الشراء التي تحتوي هذا المنتج
    public function purchaseLines()
    {
        return $this->hasMany(PurchaseLine::class, 'produit_id');
    }
}
