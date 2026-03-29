<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'bill_id',
        'salesperson_id',
        'bill_total',
        'commission_type',
        'commission_rate',
        'commission_amount',
        'deducted_returns',
        'net_commission',
        'status',
        'approved_by',
        'approved_at',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'bill_total'        => 'decimal:2',
        'commission_rate'   => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'deducted_returns'  => 'decimal:2',
        'net_commission'    => 'decimal:2',
        'approved_at'       => 'datetime',
        'paid_at'           => 'datetime',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function salesperson()
    {
        return $this->belongsTo(Salesperson::class, 'salesperson_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recalculate(): void
    {
        $this->net_commission = $this->commission_amount - $this->deducted_returns;
        $this->save();
    }
}