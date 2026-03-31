<?php

namespace App\Observers;

use App\Models\Inventory;

class InventoryObserver extends BaseObserver
{
    public function created(Inventory $inventory): void
    {
        $this->log($inventory, 'created', null, [
            'name'         => $inventory->name,
            'sku'          => $inventory->sku,
            'batch_number' => $inventory->batch_number,
            'qty'          => $inventory->qty,
        ]);
    }

    public function updated(Inventory $inventory): void
    {
        $dirty = $inventory->getDirty();

        // Only log if meaningful fields changed
        $watchedFields = ['qty', 'price', 'cost', 'batch_number', 'expiry_date', 'low_stock_threshold'];
        $relevantChanges = array_intersect_key($dirty, array_flip($watchedFields));

        if (empty($relevantChanges)) return;

        $this->log($inventory, 'updated',
            array_intersect_key($inventory->getOriginal(), array_flip($watchedFields)),
            $relevantChanges
        );
    }

    public function deleted(Inventory $inventory): void
    {
        $this->log($inventory, 'deleted', [
            'name' => $inventory->name,
            'sku'  => $inventory->sku,
        ]);
    }
}