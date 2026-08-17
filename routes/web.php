<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarrantyController;
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
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/novo', [CustomerController::class, 'create'])->name('create');
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
            Route::get('/{customer}/editar', [CustomerController::class, 'edit'])->name('edit');
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        });

    Route::prefix('cadastros/veiculos')
        ->name('vehicles.')
        ->group(function () {
            Route::get('/', [VehicleController::class, 'index'])->name('index');
            Route::get('/novo', [VehicleController::class, 'create'])->name('create');
            Route::post('/', [VehicleController::class, 'store'])->name('store');
            Route::get('/modelos/{brand}', [VehicleController::class, 'models'])->name('models');
        });

    Route::prefix('cadastros/produtos')
        ->name('products.')
        ->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/novo', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/editar', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        });

    Route::prefix('cadastros/fornecedores')
        ->name('suppliers.')
        ->group(function () {
            Route::get('/', [SupplierController::class, 'index'])->name('index');
            Route::get('/novo', [SupplierController::class, 'create'])->name('create');
            Route::post('/', [SupplierController::class, 'store'])->name('store');
            Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
            Route::get('/{supplier}/editar', [SupplierController::class, 'edit'])->name('edit');
            Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
        });

    Route::prefix('ordens-servico')
        ->name('service-orders.')
        ->group(function () {
            Route::get('/', [ServiceOrderController::class, 'index'])->name('index');
            Route::get('/nova', [ServiceOrderController::class, 'create'])->name('create');
            Route::post('/', [ServiceOrderController::class, 'store'])->name('store');
            Route::get('/{serviceOrder}', [ServiceOrderController::class, 'show'])->name('show');
        });

    Route::get('/api/clientes/{customer}/veiculos', [ServiceOrderController::class, 'customerVehicles'])
        ->name('api.customer-vehicles');

    Route::prefix('estoque')
        ->name('stock.')
        ->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::get('/{product}/movimentacoes', [StockController::class, 'movements'])->name('movements');
            Route::post('/{product}/movimentacoes', [StockController::class, 'store'])->name('store');
        });

    Route::prefix('vendas')
        ->name('sales.')
        ->group(function () {
            Route::get('/', [SaleController::class, 'index'])->name('index');
            Route::get('/nova', [SaleController::class, 'create'])->name('create');
            Route::post('/', [SaleController::class, 'store'])->name('store');
            Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        });

    Route::prefix('orcamentos')
        ->name('quotes.')
        ->group(function () {
            Route::get('/', [QuoteController::class, 'index'])->name('index');
            Route::get('/novo', [QuoteController::class, 'create'])->name('create');
            Route::post('/', [QuoteController::class, 'store'])->name('store');
            Route::get('/{quote}', [QuoteController::class, 'show'])->name('show');
        });

    Route::prefix('garantias')
        ->name('warranties.')
        ->group(function () {
            Route::get('/', [WarrantyController::class, 'index'])->name('index');
            Route::get('/nova', [WarrantyController::class, 'create'])->name('create');
            Route::post('/', [WarrantyController::class, 'store'])->name('store');
            Route::get('/{warranty}', [WarrantyController::class, 'show'])->name('show');
        });

    Route::prefix('compras')
        ->name('purchases.')
        ->group(function () {
            Route::get('/', [PurchaseController::class, 'index'])->name('index');
            Route::get('/nova', [PurchaseController::class, 'create'])->name('create');
            Route::post('/', [PurchaseController::class, 'store'])->name('store');
            Route::get('/{purchase}', [PurchaseController::class, 'show'])->name('show');
        });

    Route::prefix('financeiro')
        ->name('financial.')
        ->group(function () {
            Route::get('/', [FinancialController::class, 'index'])->name('index');
            Route::get('/novo', [FinancialController::class, 'create'])->name('create');
            Route::post('/', [FinancialController::class, 'store'])->name('store');
            Route::get('/{entry}', [FinancialController::class, 'show'])->name('show');
            Route::post('/{entry}/pagar', [FinancialController::class, 'pay'])->name('pay');
        });

    Route::prefix('caixa')
        ->name('cash-register.')
        ->group(function () {
            Route::get('/', [CashRegisterController::class, 'index'])->name('index');
            Route::get('/abrir', [CashRegisterController::class, 'open'])->name('open');
            Route::post('/', [CashRegisterController::class, 'store'])->name('store');
            Route::get('/{cashRegister}', [CashRegisterController::class, 'show'])->name('show');
            Route::post('/{cashRegister}/fechar', [CashRegisterController::class, 'close'])->name('close');
        });

    Route::get('/relatorios', [ReportController::class, 'index'])->name('reports.index');

    Route::prefix('admin/usuarios')
        ->name('users.')
        ->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/novo', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/editar', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
        });

    Route::post('/logout', [AuthController::class, 'destroy'])
        ->name('logout');
});
