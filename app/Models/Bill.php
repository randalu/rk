<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'salesperson_id',
        'created_by',
        'payment_type',
        'payment_term',
        'due_date',
        'advance_payment',
        'total',
    ];

    protected $casts = [
        'due_date'        => 'date',
        'advance_payment' => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function salesperson()
    {
        return $this->belongsTo(Salesperson::class, 'salesperson_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(BillItem::class, 'bill_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'bill_id');
    }

    public function returns()
    {
        return $this->hasMany(ProductReturn::class, 'bill_id');
    }

    public function commission()
    {
        return $this->hasOne(Commission::class, 'bill_id');
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return (float) $this->total - $this->total_paid;
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->balance <= 0;
    }
}