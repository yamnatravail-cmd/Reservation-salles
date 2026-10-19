<?php

use App\Http\Controllers\DemandeReservationController;
use App\Models\DemandeReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\FactureReservationController;
use App\Http\Controllers\FacturePurchaseController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/users', function () {
    return \App\Models\User::all();
});


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::post('/register', [RegisteredUserController::class, 'store']);
   Route::get('factures-reservation/{id}/pdf', [FactureReservationController::class, 'PDF']);
    Route::get('/factures/{id}/pdf', [FacturePurchaseController::class, 'downloadPDF']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/factures-reservation/{id}/paiement', [FactureReservationController::class, 'ajouterPaiement']);

    // Clients
    Route::apiResource('clients', App\Http\Controllers\ClientController::class);

    // Salles
    Route::apiResource('salles', App\Http\Controllers\SalleController::class);

    // Evenements
    Route::apiResource('evenements', App\Http\Controllers\EvenementController::class);

    // Demandes de réservation
    Route::apiResource('demandes', App\Http\Controllers\DemandeReservationController::class);

    // Factures de réservation
    Route::apiResource('factures-reservation', App\Http\Controllers\FactureReservationController::class);
  
      Route::apiResource('tvas', App\Http\Controllers\TvaController::class);

    // Paiements
    Route::apiResource('paiements', App\Http\Controllers\PaiementController::class);

    // Produits
    Route::apiResource('produits', App\Http\Controllers\ProduitController::class);

    // Catégories des produits
    Route::apiResource('categories-produits', App\Http\Controllers\CategoryProduitController::class);

    // Services
    Route::apiResource('services', App\Http\Controllers\ServiceController::class);

    // Fournisseurs
    Route::apiResource('fournisseurs', App\Http\Controllers\FournisseurController::class);

    // Achats
    Route::apiResource('achats', App\Http\Controllers\PurchaseController::class);

    // Factures d'achat
    Route::apiResource('factures-achat', App\Http\Controllers\FacturePurchaseController::class);
Route::get('/client-reservations/{id}', [App\Http\Controllers\DemandeReservationController::class, 'getClientReservations']);

    // Personnel
    Route::apiResource('personnels', App\Http\Controllers\PersonnelController::class);
        Route::get('/ceatereservation', [App\Http\Controllers\DemandeReservationController::class, 'createR'])->name('ceatereservation');
        Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/reservations-data', [App\Http\Controllers\DemandeReservationController::class, 'allWithRelations']);
Route::post('/salles/{id}/update-status', [App\Http\Controllers\SalleController::class, 'updateStatus']);
Route::get('/reservation-data/{id}', [App\Http\Controllers\DemandeReservationController::class, 'getReservationData']);

Route::get('/reservations-infos', [App\Http\Controllers\DemandeReservationController::class, 'infos']);



});
 Route::middleware('auth:sanctum')->get('/salles/{id}/details', [App\Http\Controllers\SalleController::class, 'detailsWithServices']);

