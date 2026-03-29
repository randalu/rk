<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;

    protected $table = 'inventory'; // add this line

    protected $fillable = [
        'supplier_id',
        'name',
        'sku',
        'batch_number',
        'expiry_date',
        'qty',
        'low_stock_threshold',
        'price',
        'cost',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'price'       => 'decimal:2',
        'cost'        => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function billItems()
    {
        return $this->hasMany(BillItem::class, 'inventory_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'inventory_id');
    }

    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class, 'inventory_id');
    }

    public function isLowStock(): bool
    {
        return $this->qty <= $this->low_stock_threshold;
    }
}