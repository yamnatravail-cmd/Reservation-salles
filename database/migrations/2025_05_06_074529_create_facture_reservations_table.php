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
        Schema::create('facture_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_id')->constrained('demande_reservations')->onDelete('cascade');
            $table->dateTime('date_emission');
            $table->float('montant_total');
            $table->string('status');
            $table->foreignId('tva_id')->constrained('tvas');
            $table->decimal('montant_paye', 8, 2)->default(0);
            $table->decimal('reste_a_payer', 8, 2)->default(0);
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
        Schema::dropIfExists('facture_reservations');
    }
};
