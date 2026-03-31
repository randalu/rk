<?php

namespace App\Observers;

use App\Models\Payment;

class PaymentObserver extends BaseObserver
{
    public function created(Payment $payment): void
    {
        $this->log($payment, 'created', null, [
            'bill_id'      => $payment->bill_id,
            'amount'       => $payment->amount,
            'payment_type' => $payment->payment_type,
        ]);
    }

    public function deleted(Payment $payment): void
    {
        $this->log($payment, 'deleted', [
            'bill_id' => $payment->bill_id,
            'amount'  => $payment->amount,
        ]);
    }

    public function updated(Payment $payment): void
    {
        $this->log($payment, 'updated',
            $payment->getOriginal(),
            $payment->getDirty()
        );
    }
}