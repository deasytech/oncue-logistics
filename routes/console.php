<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('rsvp:send-reminders')->cron('0 8 */2 * *');

// Drains the database queue every minute. On shared hosting (cPanel) there's no
// persistent `queue:work` process, so this relies on the cron-driven scheduler
// (`* * * * * php artisan schedule:run`) to pick up queued jobs regularly instead —
// including bulk guest message sends, which are dispatched as individual queued jobs.
Schedule::command('queue:work', ['--stop-when-empty', '--max-time=50'])
    ->everyMinute()
    ->withoutOverlapping();
