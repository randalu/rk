<?php

namespace App\Observers;

use App\Models\Expense;

class ExpenseObserver extends BaseObserver
{
    public function created(Expense $expense): void
    {
        $this->log($expense, 'created', null, [
            'category_id'  => $expense->category_id,
            'amount'       => $expense->amount,
            'expense_date' => $expense->expense_date,
        ]);
    }

    public function updated(Expense $expense): void
    {
        $this->log($expense, 'updated',
            $expense->getOriginal(),
            $expense->getDirty()
        );
    }

    public function deleted(Expense $expense): void
    {
        $this->log($expense, 'deleted', ['amount' => $expense->amount]);
    }
}