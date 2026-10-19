<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FactureReservationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
Route::get('/facture', function () {
    return view('facture');
});
require __DIR__.'/auth.php';
Route::get('/facture', [FactureReservationController::class, 'PDF'])->name('rahima');

Route::post('/hello' ,[HelloController::class, 'store'])->name('hello.index');
Route::resource('/hello', HelloController::class);
