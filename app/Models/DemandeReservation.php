<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\FactureReservation;
use App\Models\Client;
use App\Models\Evenement;
use App\Models\Salle;

class DemandeReservation extends Model
{
    protected $fillable = [
        'client_id',
        'evenement_id',
        'salle_id',
        'statut',
        'date_debut',
        'date_fin',
        'services_choisis',
        'prix',
    ];
     protected $casts = [
        'services_choisis' => 'array', 
    ];
protected $appends = ['services_details'];

public function getServicesDetailsAttribute()
{
    return Service::whereIn('id', $this->services_choisis ?? [])->with('produit')->get();
}

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }

    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

    public function facture()
    {
        return $this->hasOne(FactureReservation::class);
    }
    public function services()
{
    return Service::whereIn('id', $this->services_choisis ?? [])->with('produit')->get();
}

}
