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
        // ចែករំលែកព័ត៌មានហាងទៅកាន់គ្រប់ Views ទាំងអស់ ប្រសិនបើតារាង settings មានពិតមែន
        if (Schema::hasTable('settings')) {
            $settings = Setting::pluck('value', 'key')->all();

            // រក្សាទុកក្នុង Config ដើម្បីហៅប្រើក្នុង Controller តាមរយៈ config('settings.shop_name')
            config(['settings' => $settings]);

            // ចែករំលែកទៅកាន់គ្រប់ Blade Views ទាំងអស់តាមរយៈអថេរ $shopSettings
            View::share('shopSettings', $settings);
        }
    }
}
