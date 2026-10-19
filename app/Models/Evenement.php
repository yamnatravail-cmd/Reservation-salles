<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    protected $fillable = ['nom', 'type', 'date'];

    public function demandes()
    {
        return $this->hasMany(DemandeReservation::class);
    }
}
