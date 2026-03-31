<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $table = 'sms_log'; // add this line
    
    protected $fillable = [
        'recipient_phone',
        'recipient_type',
        'sms_type',
        'message',
        'reference_id',
        'reference_type',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function reference()
    {
        return $this->morphTo();
    }
}