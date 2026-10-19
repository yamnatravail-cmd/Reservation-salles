<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturePurchasesTable extends Migration
{
    public function up()
    {
       Schema::create('facture_purchases', function (Blueprint $table) {
    $table->id();
    $table->foreignId('purchase_id')->nullable()->constrained('purchases')->onDelete('cascade');
    $table->decimal('montant_total', 15, 2)->nullable();
    $table->decimal('amount_paid', 15, 2)->default(0);
    $table->decimal('amount_remaining', 15, 2)->nullable();
    $table->date('date_emission')->nullable();
    $table->string('statut')->nullable();
    $table->foreignId('tva_id')->nullable()->constrained('tvas')->onDelete('set null');
    $table->timestamps();
});

    }

    public function down()
    {
        Schema::dropIfExists('facture_purchases');
    }
}
