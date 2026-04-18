<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientExportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\PharmacyExportController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/parametres', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/parametres/entreprise', [SettingsController::class, 'updateApplication'])
        ->middleware('role:Admin')
        ->name('settings.application.update');

    Route::redirect('/profile', '/parametres')->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Zones (create avant {zone} pour éviter que « create » soit pris pour un ID)
    Route::middleware(['permission:view zones|manage zones'])->group(function () {
        Route::get('zones', [ZoneController::class, 'index'])->name('zones.index');
    });
    Route::middleware(['permission:manage zones'])->group(function () {
        Route::get('zones/create', [ZoneController::class, 'create'])->name('zones.create');
        Route::post('zones', [ZoneController::class, 'store'])->name('zones.store');
    });
    Route::middleware(['permission:view zones|manage zones'])->group(function () {
        Route::get('zones/{zone}', [ZoneController::class, 'show'])->name('zones.show');
    });
    Route::middleware(['permission:manage zones'])->group(function () {
        Route::get('zones/{zone}/edit', [ZoneController::class, 'edit'])->name('zones.edit');
        Route::put('zones/{zone}', [ZoneController::class, 'update'])->name('zones.update');
        Route::patch('zones/{zone}', [ZoneController::class, 'update']);
        Route::delete('zones/{zone}', [ZoneController::class, 'destroy'])->name('zones.destroy');
    });

    // Export pharmacies (avant les routes {pharmacy} pour éviter les conflits)
    Route::middleware(['permission:export pharmacies'])->group(function () {
        Route::get('pharmacies/export/{format}', [PharmacyExportController::class, 'export'])
            ->whereIn('format', ['csv', 'excel', 'pdf'])
            ->name('pharmacies.export');
    });

    Route::middleware(['permission:view pharmacies|manage pharmacies'])->group(function () {
        Route::get('pharmacies', [PharmacyController::class, 'index'])->name('pharmacies.index');
    });
    Route::middleware(['permission:manage pharmacies'])->group(function () {
        Route::get('pharmacies/create', [PharmacyController::class, 'create'])->name('pharmacies.create');
        Route::post('pharmacies', [PharmacyController::class, 'store'])->name('pharmacies.store');
    });
    Route::middleware(['permission:view pharmacies|manage pharmacies'])->group(function () {
        Route::get('pharmacies/{pharmacy}', [PharmacyController::class, 'show'])->name('pharmacies.show');
    });
    Route::middleware(['permission:manage pharmacies'])->group(function () {
        Route::get('pharmacies/{pharmacy}/edit', [PharmacyController::class, 'edit'])->name('pharmacies.edit');
        Route::put('pharmacies/{pharmacy}', [PharmacyController::class, 'update'])->name('pharmacies.update');
        Route::patch('pharmacies/{pharmacy}', [PharmacyController::class, 'update']);
        Route::delete('pharmacies/{pharmacy}', [PharmacyController::class, 'destroy'])->name('pharmacies.destroy');
    });

    Route::middleware(['permission:manage clients'])->group(function () {
        Route::get('pharmacies/{pharmacy}/convert-to-client', [ClientController::class, 'createFromPharmacy'])
            ->name('pharmacies.convert_to_client');
        Route::post('pharmacies/{pharmacy}/convert-to-client', [ClientController::class, 'storeFromPharmacy'])
            ->name('pharmacies.convert_to_client.store');
    });

    Route::middleware(['permission:manage planning'])->group(function () {
        Route::get('/planning', [PlanningController::class, 'index'])->name('planning.index');
        Route::post('/planning/visits', [PlanningController::class, 'store'])->name('planning.visits.store');
        Route::delete('/planning/visits/{visit}', [PlanningController::class, 'destroy'])->name('planning.visits.destroy');
    });

    Route::middleware(['permission:view visits|manage planning'])->group(function () {
        Route::get('/visits', fn () => view('coming_soon', ['module' => 'Visites']))->name('visits.index');
    });

    Route::middleware(['permission:view clients|manage clients'])->group(function () {
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    });

    Route::middleware(['permission:export clients'])->group(function () {
        Route::get('/clients/export/{format}', [ClientExportController::class, 'export'])
            ->whereIn('format', ['csv', 'excel', 'pdf'])
            ->name('clients.export');
    });

    Route::middleware(['permission:manage clients'])->group(function () {
        Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit')->whereNumber('client');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update')->whereNumber('client');
        Route::patch('/clients/{client}', [ClientController::class, 'update'])->whereNumber('client');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy')->whereNumber('client');
    });

    Route::middleware(['permission:view clients|manage clients'])->group(function () {
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show')->whereNumber('client');
    });

    Route::middleware(['permission:view reports|export reports'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
    Route::middleware(['permission:export reports'])->group(function () {
        Route::get('/reports/export/{format}', [ReportController::class, 'export'])
            ->whereIn('format', ['csv', 'excel', 'pdf'])
            ->name('reports.export');
    });

    Route::middleware(['permission:view users|manage users'])->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
    });

    Route::middleware(['permission:manage users'])->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}', [UserController::class, 'update']);
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';
