<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\SupplierController;
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

            Route::get('/{vehicle}', [VehicleController::class, 'show'])
                ->whereNumber('vehicle')
                ->name('show');

            Route::get('/{vehicle}/editar', [VehicleController::class, 'edit'])
                ->whereNumber('vehicle')
                ->name('edit');

            Route::put('/{vehicle}', [VehicleController::class, 'update'])
                ->whereNumber('vehicle')
                ->name('update');
        });

    Route::prefix('cadastros/produtos')
        ->name('products.')
        ->group(function () {
            Route::get('/', [ProductController::class, 'index'])
                ->name('index');

            Route::get('/novo', [ProductController::class, 'create'])
                ->name('create');

            Route::post('/', [ProductController::class, 'store'])
                ->name('store');

            Route::get('/{product}', [ProductController::class, 'show'])
                ->name('show');

            Route::get('/{product}/editar', [ProductController::class, 'edit'])
                ->name('edit');

            Route::put('/{product}', [ProductController::class, 'update'])
                ->name('update');
        });

    Route::prefix('cadastros/fornecedores')
        ->name('suppliers.')
        ->group(function () {
            Route::get('/', [SupplierController::class, 'index'])
                ->name('index');

            Route::get('/novo', [SupplierController::class, 'create'])
                ->name('create');

            Route::post('/', [SupplierController::class, 'store'])
                ->name('store');

            Route::get('/{supplier}', [SupplierController::class, 'show'])
                ->name('show');

            Route::get('/{supplier}/editar', [SupplierController::class, 'edit'])
                ->name('edit');

            Route::put('/{supplier}', [SupplierController::class, 'update'])
                ->name('update');
        });

    Route::prefix('ordens-servico')
        ->name('service-orders.')
        ->group(function () {
            Route::get('/', [ServiceOrderController::class, 'index'])
                ->name('index');

            Route::get('/nova', [ServiceOrderController::class, 'create'])
                ->name('create');

            Route::post('/', [ServiceOrderController::class, 'store'])
                ->name('store');

            Route::get('/cliente/{customer}/veiculos', [ServiceOrderController::class, 'customerVehicles'])
                ->whereNumber('customer')
                ->name('customer-vehicles');

            Route::get('/{serviceOrder}', [ServiceOrderController::class, 'show'])
                ->whereNumber('serviceOrder')
                ->name('show');

            Route::patch('/{serviceOrder}/status', [ServiceOrderController::class, 'updateStatus'])
                ->whereNumber('serviceOrder')
                ->name('update-status');

            Route::post('/{serviceOrder}/itens', [ServiceOrderController::class, 'addItem'])
                ->whereNumber('serviceOrder')
                ->name('add-item');

            Route::delete('/{serviceOrder}/itens/{item}', [ServiceOrderController::class, 'removeItem'])
                ->whereNumber('serviceOrder')
                ->whereNumber('item')
                ->name('remove-item');
        });

    Route::post('/logout', [AuthController::class, 'destroy'])
        ->name('logout');
});
