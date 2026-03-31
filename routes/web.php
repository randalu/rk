<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CommissionTierController;
use App\Http\Controllers\SmsRecipientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SalespersonController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActionLogController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Inventory
    Route::resource('inventory', InventoryController::class);

    // Purchases
    Route::resource('purchases', PurchaseController::class);
    Route::patch('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::patch('purchases/{purchase}/cancel',  [PurchaseController::class, 'cancel'])->name('purchases.cancel');

    // Bills
    Route::resource('bills', BillController::class);
    Route::get('bills/{bill}/print', [BillController::class, 'printView'])->name('bills.print');
    Route::get('bills/{bill}/pdf',   [BillController::class, 'exportPdf'])->name('bills.pdf');

    // Payments
    Route::resource('payments', PaymentController::class)->only(['index', 'store', 'destroy']);

    // Returns
    Route::resource('returns', ReturnController::class);
    Route::patch('returns/{return}/accept', [ReturnController::class, 'accept'])->name('returns.accept');
    Route::patch('returns/{return}/reject', [ReturnController::class, 'reject'])->name('returns.reject');
    Route::get('returns/bill/{bill}/items', [ReturnController::class, 'getBillItems'])->name('returns.bill-items');

    // Expenses
    Route::resource('expenses', ExpenseController::class);
    Route::resource('expense-categories', ExpenseCategoryController::class)
         ->only(['index', 'store', 'update', 'destroy']);

    // Commissions
    Route::resource('commissions', CommissionController::class)->only(['index', 'show']);
    Route::patch('commissions/{commission}/release', [CommissionController::class, 'release'])->name('commissions.release');
    Route::patch('commissions/{commission}/cancel',  [CommissionController::class, 'cancel'])->name('commissions.cancel');

    // Commission Tiers
    Route::resource('commission-tiers', CommissionTierController::class)
         ->only(['index', 'store', 'update', 'destroy']);

    // SMS Recipients
    Route::resource('sms-recipients', SmsRecipientController::class)
         ->only(['index', 'store', 'update', 'destroy']);

    // Users
    Route::resource('users', UserController::class);

    // Salespeople
    Route::resource('salespeople', SalespersonController::class)
         ->only(['index', 'edit', 'update', 'destroy']);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',            [ReportController::class, 'index'])->name('index');
        Route::get('/sales',       [ReportController::class, 'sales'])->name('sales');
        Route::get('/commissions', [ReportController::class, 'commissions'])->name('commissions');
        Route::get('/inventory',   [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/expenses',    [ReportController::class, 'expenses'])->name('expenses');
    });

    // Action Log
    Route::get('action-log', [ActionLogController::class, 'index'])->name('action-log.index');

});

require __DIR__.'/auth.php';