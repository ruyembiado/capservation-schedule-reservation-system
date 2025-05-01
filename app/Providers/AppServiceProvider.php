<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\NotificationController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('local')) {
            $now = Carbon::now();
            if (Schema::hasTable('reservations')) {
                // Check if it's already 8 AM or later
                if ($now->hour >= 8) {
                    if (!Cache::has('reminder_created_today')) {
                        app(NotificationController::class)->createScheduleReminder();
                        Cache::put('reminder_created_today', true, $now->endOfDay());
                    }
                }
            }
        }
    }
}
