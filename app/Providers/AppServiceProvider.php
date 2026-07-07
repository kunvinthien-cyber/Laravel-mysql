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
        // ការពារការគាំងកំឡុងពេល Build time ឬពេលគ្មានការតភ្ជាប់ Database
        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::pluck('value', 'key')->all();

                config(['settings' => $settings]);
                View::share('shopSettings', $settings);
            }
        } catch (\Exception $e) {
            // រំលងដោយស្ងៀមស្ងាត់ ប្រសិនបើគ្មានការតភ្ជាប់ Database (ឧ. ក្នុងពេលរត់ config:cache ពេល build)
        }
    }
}
