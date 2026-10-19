<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Purchase;

class Fournisseur extends Model
{
    protected $fillable = ['nom', 'adresse', 'telephone','mobile', 'email'];

    public function achats()
    {
        return $this->hasMany(Purchase::class);
    }
}

