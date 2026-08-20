<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Kasir (POS)
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/save', [KasirController::class, 'save'])->name('kasir.save');
    Route::get('/kasir/products', [KasirController::class, 'getProducts'])->name('kasir.products');
    Route::get('/kasir/customer-search', [KasirController::class, 'searchCustomer'])->name('kasir.customer.search');

    // Transactions
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transaksi/{id}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transaksi/{id}/nota', [TransactionController::class, 'printNota'])->name('transactions.nota');
    Route::get('/transaksi/{id}/nota-thermal', [TransactionController::class, 'printThermal'])->name('transactions.thermal');
    Route::put('/transaksi/{id}/batal', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::delete('/transaksi/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Products & Services
    Route::middleware('admin')->group(function () {
        Route::resource('/produk', ProductController::class)->names([
            'index'   => 'products.index',
            'create'  => 'products.create',
            'store'   => 'products.store',
            'show'    => 'products.show',
            'edit'    => 'products.edit',
            'update'  => 'products.update',
            'destroy' => 'products.destroy',
        ]);

        // Customers
        Route::resource('/pelanggan', CustomerController::class)->names([
            'index'   => 'customers.index',
            'create'  => 'customers.create',
            'store'   => 'customers.store',
            'show'    => 'customers.show',
            'edit'    => 'customers.edit',
            'update'  => 'customers.update',
            'destroy' => 'customers.destroy',
        ]);

        // Settings
        Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/pengaturan/logo', [SettingController::class, 'uploadLogo'])->name('settings.logo');

        // Reports
        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');
    });
});
