<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'default_payment_term',
    ];

    public function bills()
    {
        return $this->hasMany(Bill::class, 'customer_id');
    }
}