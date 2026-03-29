<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\PaymentController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Placeholder routes — will be replaced as we build each module
    Route::resource('bills', BillController::class);
    Route::resource('payments', PaymentController::class)->only(['index', 'store', 'destroy']);
    Route::resource('customers', CustomerController::class);
    Route::get('/returns', fn() => view('coming-soon', ['module' => 'Returns']))->name('returns.index');
    Route::resource('inventory', InventoryController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::patch('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::patch('purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');
    Route::resource('suppliers', SupplierController::class);
    Route::get('/expenses', fn() => view('coming-soon', ['module' => 'Expenses']))->name('expenses.index');
    Route::get('/commissions', fn() => view('coming-soon', ['module' => 'Commissions']))->name('commissions.index');
    Route::get('/users', fn() => view('coming-soon', ['module' => 'Users']))->name('users.index');
    Route::get('/sms-recipients', fn() => view('coming-soon', ['module' => 'SMS Recipients']))->name('sms-recipients.index');
    Route::get('bills/{bill}/print', [BillController::class, 'printView'])->name('bills.print');
Route::get('bills/{bill}/pdf', [BillController::class, 'exportPdf'])->name('bills.pdf');
});

require __DIR__.'/auth.php';