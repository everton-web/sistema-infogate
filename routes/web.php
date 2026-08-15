<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::middleware(['auth', 'company'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::prefix('cadastros/clientes')
        ->name('customers.')
        ->group(function () {
            Route::get('/', [CustomerController::class, 'index'])
                ->name('index');

            Route::get('/novo', [CustomerController::class, 'create'])
                ->name('create');

            Route::post('/', [CustomerController::class, 'store'])
                ->name('store');

            Route::get('/{customer}', [CustomerController::class, 'show'])
                ->name('show');

            Route::get('/{customer}/editar', [CustomerController::class, 'edit'])
                ->name('edit');

            Route::put('/{customer}', [CustomerController::class, 'update'])
                ->name('update');
        });

    Route::prefix('cadastros/veiculos')
        ->name('vehicles.')
        ->group(function () {
            Route::get('/', [VehicleController::class, 'index'])
                ->name('index');

            Route::get('/novo', [VehicleController::class, 'create'])
                ->name('create');

            Route::post('/', [VehicleController::class, 'store'])
                ->name('store');

            Route::get('/modelos/{brand}', [VehicleController::class, 'models'])
                ->name('models');
        });

    Route::post('/logout', [AuthController::class, 'destroy'])
        ->name('logout');
});
