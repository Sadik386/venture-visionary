<?php

use Illuminate\Support\Facades\Schedule;

// SLA Check and Expiry Alert Scheduled Jobs
Schedule::command('queue:work --stop-when-empty')->everyMinute();
