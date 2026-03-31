<?php

namespace App\Observers;

use App\Models\ProductReturn;

class ReturnObserver extends BaseObserver
{
    public function created(ProductReturn $return): void
    {
        $this->log($return, 'created', null, [
            'bill_id' => $return->bill_id,
            'total'   => $return->total,
            'reason'  => $return->reason,
        ]);
    }

    public function updated(ProductReturn $return): void
    {
        $dirty = $return->getDirty();

        // Use a more descriptive action for status changes
        $action = 'updated';
        if (isset($dirty['status'])) {
            $action = $dirty['status']; // 'accepted' or 'rejected'
        }

        $this->log($return, $action,
            $return->getOriginal(),
            $dirty
        );
    }

    public function deleted(ProductReturn $return): void
    {
        $this->log($return, 'deleted');
    }
}