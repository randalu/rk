<?php

use Illuminate\Support\Facades\Schedule;

// Run daily at 8am
Schedule::command('sms:due-reminders')->dailyAt('08:00');
Schedule::command('sms:admin-due-alerts')->dailyAt('07:30');