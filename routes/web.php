<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

// based on user type, we render different dashboard content?
Route::get('dashboard', function() {
    $clientAmount = Client::query()->count();
    $projectAmount = Project::query()->count();

    return Inertia::render('Dashboard', [
        'clientAmount' => $clientAmount,
        'projectAmount' => $projectAmount,
    ]);
})->name('dashboard');

Route::resource('clients', ClientController::class)
    ->except(['show'])
    ->middleware(['auth', 'verified']);

Route::resource('projects', ProjectController::class)
    ->middleware(['auth', 'verified']);

require __DIR__.'/settings.php';
