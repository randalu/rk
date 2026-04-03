<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'system_name',
        'logo_path',
        'company_name',
        'company_tagline',
        'company_phone',
        'company_email',
        'company_website',
        'company_address',
        'company_registration_no',
        'invoice_footer_heading',
        'invoice_footer_notes',
        'due_reminder_email_subject',
        'due_reminder_email_body',
    ];
}
