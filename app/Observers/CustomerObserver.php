<?php

namespace App\Observers;

use App\Models\Customer;

class CustomerObserver extends BaseObserver
{
    public function created(Customer $customer): void
    {
        $this->log($customer, 'created', null, [
            'name'  => $customer->name,
            'phone' => $customer->phone,
        ]);
    }

    public function updated(Customer $customer): void
    {
        $this->log($customer, 'updated',
            $customer->getOriginal(),
            $customer->getDirty()
        );
    }

    public function deleted(Customer $customer): void
    {
        $this->log($customer, 'deleted', ['name' => $customer->name]);
    }
}