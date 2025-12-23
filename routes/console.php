<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// تشغيل النسخ الاحتياطي كل 3 أيام الساعة 2 صباحاً
Schedule::command('backup:run')->cron('0 2 */3 * *');

// تنظيف النسخ القديمة يومياً الساعة 3 صباحاً
Schedule::command('backup:clean')->dailyAt('03:00');

// مراقبة حالة النسخ يومياً الساعة 4 صباحاً
Schedule::command('backup:monitor')->dailyAt('04:00');
