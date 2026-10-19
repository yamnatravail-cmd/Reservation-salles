<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DemandeReservation;

class Salle extends Model
{
    protected $fillable = ['nom', 'capacite', 'prix','status','image'];

    public function demandes()
    {
        return $this->hasMany(DemandeReservation::class);
    }
public function services()
{
    return $this->hasMany(Service::class);
}


}
