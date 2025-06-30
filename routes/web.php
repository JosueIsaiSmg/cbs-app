<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VacanteController;
use App\Http\Controllers\ProspectoController;
use App\Http\Controllers\EntrevistaController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::resource('vacantes', VacanteController::class)->middleware(['auth:sanctum', 'verified']);
Route::get('/vacantes/create', [VacanteController::class, 'create'])->name('vacantes.create');
Route::get('/vacantes/{vacante}/edit', [VacanteController::class, 'edit'])->name('vacantes.edit');
Route::put('/vacantes/{vacante}', [VacanteController::class, 'update'])->name('vacantes.update');

Route::resource('prospectos', ProspectoController::class)->middleware(['auth:sanctum', 'verified']);
Route::get('/prospectos/create', [ProspectoController::class, 'create'])->name('prospectos.create');
Route::get('/prospectos/{prospecto}/edit', [ProspectoController::class, 'edit'])->name('prospectos.edit');
Route::put('/prospectos/{prospecto}', [ProspectoController::class, 'update'])->name('prospectos.update');

Route::resource('entrevistas', EntrevistaController::class)->middleware(['auth:sanctum', 'verified']);
Route::get('/entrevistas/create', [EntrevistaController::class, 'create'])->name('entrevistas.create');
Route::get('/entrevistas/{vacante}/{prospecto}/edit', [EntrevistaController::class, 'edit'])->name('entrevistas.edit');
Route::put('/entrevistas/{vacante}/{prospecto}', [EntrevistaController::class, 'update'])->name('entrevistas.update');
Route::delete('/entrevistas/{vacante}/{prospecto}', [EntrevistaController::class, 'destroy'])->name('entrevistas.destroy');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
