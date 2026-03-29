<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $fillable = [
        'return_id',
        'bill_item_id',
        'inventory_id',
        'batch_number',
        'qty',
        'line_total',
    ];

    protected $casts = [
        'line_total' => 'decimal:2',
    ];

    public function productReturn()
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }

    public function billItem()
    {
        return $this->belongsTo(BillItem::class, 'bill_item_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}