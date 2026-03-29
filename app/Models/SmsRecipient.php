<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsRecipient extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'notify_low_stock',
        'notify_due_payments',
        'is_active',
    ];

    protected $casts = [
        'notify_low_stock'    => 'boolean',
        'notify_due_payments' => 'boolean',
        'is_active'           => 'boolean',
    ];
}