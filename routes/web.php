<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    abort(404, 'Page not found');
});

/*
Route::get('/', function () {
    return view('welcome');
});
Route::get('/password/reset/{token}', function () {
    // Ajoutez votre logique ou retournez une vue pour la réinitialisation du mot de passe ici.
})->name('password.reset');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
*/