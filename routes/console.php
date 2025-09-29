<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:fetch-and-store-news-articles')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
