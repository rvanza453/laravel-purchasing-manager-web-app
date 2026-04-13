<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('*', function ($view) {
            if (!auth()->check()) {
                $view->with('globalAnnouncements', collect());

                return;
            }

            if (!class_exists(\Modules\SystemSupport\Models\Announcement::class)) {
                $view->with('globalAnnouncements', collect());

                return;
            }

            $announcements = Cache::remember('global_announcements', 60, function () {
                return \Modules\SystemSupport\Models\Announcement::query()
                    ->where('is_active', true)
                    ->orderByDesc('created_at')
                    ->get();
            });

            $view->with('globalAnnouncements', $announcements);
        });
    }
}
