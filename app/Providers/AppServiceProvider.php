<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // View rendering happens after authentication, so the Setting model's
        // shop scope can safely resolve the current shop here.
        View::composer('*', function ($view) {
            try {
                $settings = Schema::hasTable('settings')
                    ? Setting::query()->pluck('value', 'key')->all()
                    : [];

                $view->with('shopSettings', $settings);
            } catch (\Throwable) {
                $view->with('shopSettings', []);
            }
        });
    }
}
