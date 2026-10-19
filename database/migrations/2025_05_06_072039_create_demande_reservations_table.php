<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('evenement_id')->constrained('evenements');
            $table->foreignId('salle_id')->constrained('salles');
            $table->enum('statut', ['En attente de confirmation', 'Confirmé', 'Annulé']);
              $table->json('services_choisis')->nullable();
            $table->dateTime('date_debut');
            $table->decimal('prix', 10, 2)->nullable();
            $table->dateTime('date_fin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demande_reservations');
    }
};
