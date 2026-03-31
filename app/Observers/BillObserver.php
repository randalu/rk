<?php

namespace App\Observers;

use App\Models\Bill;

class BillObserver extends BaseObserver
{
    public function created(Bill $bill): void
    {
        $this->log($bill, 'created', null, [
            'customer_id'    => $bill->customer_id,
            'total'          => $bill->total,
            'payment_term'   => $bill->payment_term,
            'salesperson_id' => $bill->salesperson_id,
        ]);
    }

    public function updated(Bill $bill): void
    {
        $this->log($bill, 'updated',
            $bill->getOriginal(),
            $bill->getDirty()
        );
    }

    public function deleted(Bill $bill): void
    {
        $this->log($bill, 'deleted');
    }
}