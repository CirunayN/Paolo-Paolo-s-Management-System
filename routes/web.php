<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // 1. Dashboard (All Users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. POS / Cashier (All Users)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/receipt/{order}', [PosController::class, 'receipt'])->name('pos.receipt');

    // 3. Products Catalog - Viewable by all, Modifiable ONLY by Admin
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // 4. Inventory - Viewable by all, Adjustments ONLY by Admin
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/transactions', [InventoryController::class, 'transactions'])->name('inventory.transactions');

    // 5. Customer Records - Viewable by all, Modifiable ONLY by Admin
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}/orders', [CustomerController::class, 'orders'])->name('customers.orders');

    // Profile (All Users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // View single product (constrained to numeric ID only, defined after static routes)
    Route::get('/products/{product}', [ProductController::class, 'show'])->whereNumber('product')->name('products.show');

    // =========================================================================
    // ADMIN ONLY ROUTES (Cashiers Restricted)
    // =========================================================================
    Route::middleware([AdminMiddleware::class])->group(function () {
        // Product & Category Management (Create / Edit / Delete / Quick Store)
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::post('/products/quick-store', [ProductController::class, 'quickStore'])->name('products.quick-store');
        Route::post('/categories/quick-store', [CategoryController::class, 'quickStore'])->name('categories.quick-store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Inventory Stock Adjustment
        Route::post('/inventory/adjust/{inventory}', [InventoryController::class, 'adjust'])->name('inventory.adjust');

        // Stock-In Receiving
        Route::resource('stock-in', StockInController::class)->only(['index', 'create', 'store', 'show']);

        // Suppliers
        Route::resource('suppliers', SupplierController::class)->except(['create', 'edit', 'show']);

        // Customer Record Modifications
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        // User / Staff Management
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);

        // Backup Module (Saving to E: Drive)
        Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::post('/backup/create', [BackupController::class, 'createBackup'])->name('backup.create');
        Route::post('/backup/settings', [BackupController::class, 'updateSettings'])->name('backup.settings');
        Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
        Route::delete('/backup/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy');
    });
});

require __DIR__.'/auth.php';
