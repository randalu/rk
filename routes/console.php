<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('sms:due-reminders')
    ->dailyAt('08:00')
    ->timezone(config('app.timezone'));

Schedule::command('sms:admin-due-alerts')
    ->dailyAt('08:05')
    ->timezone(config('app.timezone'));
