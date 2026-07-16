<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('rsvp:send-reminders')->cron('0 8 */2 * *');
