<?php

use App\Http\Controllers\ClientController;
use App\Models\Client;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function() {
    $clientAmount = Client::query()->count();

    return Inertia::render('Dashboard', [
        'clientAmount' => $clientAmount,
    ]);
})->name('dashboard');

Route::resource('clients', ClientController::class)
    ->except(['show'])
    ->middleware(['auth', 'verified', 'can:manage clients']);

require __DIR__.'/settings.php';
