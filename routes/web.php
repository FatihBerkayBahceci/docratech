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

// Login redirect route for auth middleware
Route::get('/login', function () {
    return view('app');
})->name('login');

// SPA Routes - Only for non-api requests!
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$');

// Fallback route (api hariç!)
Route::fallback(function () {
    return view('app');
});
