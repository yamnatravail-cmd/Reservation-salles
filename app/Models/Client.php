<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DemandeReservation;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['nom','Prenom','mobel','adresse', 'email', 'telephone'];

    public function demandes()
    {
        return $this->hasMany(DemandeReservation::class);
    }
}

