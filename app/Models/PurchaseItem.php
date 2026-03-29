<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
    'purchase_id',
    'inventory_id',
    'batch_number',
    'expiry_date',
    'qty',
    'unit_cost',
    'line_total',
];

protected $casts = [
    'expiry_date' => 'date',
    'unit_cost'   => 'decimal:2',
    'line_total'  => 'decimal:2',
];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}