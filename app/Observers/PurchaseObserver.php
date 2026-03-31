<?php

namespace App\Observers;

use App\Models\Purchase;

class PurchaseObserver extends BaseObserver
{
    public function created(Purchase $purchase): void
    {
        $this->log($purchase, 'created', null, [
            'supplier_id' => $purchase->supplier_id,
            'total'       => $purchase->total,
            'status'      => $purchase->status,
        ]);
    }

    public function updated(Purchase $purchase): void
    {
        $dirty  = $purchase->getDirty();
        $action = isset($dirty['status']) ? $dirty['status'] : 'updated';

        $this->log($purchase, $action,
            $purchase->getOriginal(),
            $dirty
        );
    }

    public function deleted(Purchase $purchase): void
    {
        $this->log($purchase, 'deleted');
    }
}