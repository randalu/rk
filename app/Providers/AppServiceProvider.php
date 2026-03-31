<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Bill;
use App\Models\ProductReturn;
use App\Models\Payment;
use App\Models\Inventory;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\User;
use App\Observers\BillObserver;
use App\Observers\ReturnObserver;
use App\Observers\PaymentObserver;
use App\Observers\InventoryObserver;
use App\Observers\CustomerObserver;
use App\Observers\PurchaseObserver;
use App\Observers\ExpenseObserver;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('helpers.php');
    }

    public function boot(): void
    {
        Bill::observe(BillObserver::class);
        ProductReturn::observe(ReturnObserver::class);
        Payment::observe(PaymentObserver::class);
        Inventory::observe(InventoryObserver::class);
        Customer::observe(CustomerObserver::class);
        Purchase::observe(PurchaseObserver::class);
        Expense::observe(ExpenseObserver::class);
        User::observe(UserObserver::class);
    }
}