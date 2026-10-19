<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaiementsTable extends Migration
{
    public function up()
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 15, 2);
            $table->string('statut');
            $table->dateTime('date_paiement');
            
            // عمودان للعلاقة polymorphic
            $table->unsignedBigInteger('facturable_id');
            $table->string('facturable_type');
            $table->index(['facturable_id', 'facturable_type']);
            
            $table->enum('origine', ['reservation', 'purchase']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paiements');
    }
}
