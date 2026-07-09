<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (filled(env('ADMIN_EMAIL')) && filled(env('ADMIN_PASSWORD'))) {
            User::updateOrCreate(
                ['email' => env('ADMIN_EMAIL')],
                [
                    'name' => env('ADMIN_NAME', 'Super Admin'),
                    'password' => env('ADMIN_PASSWORD'),
                    'role' => 'admin',
                ]
            );
        }
        $defaultSettings = [
            ['key' => 'shop_name', 'value' => 'ហាងលក់ទំនិញទូទៅ POS'],
            ['key' => 'shop_phone', 'value' => '012 345 678'],
            ['key' => 'shop_email', 'value' => 'info@pos-shop.com'],
            ['key' => 'shop_address', 'value' => 'ភ្នំពេញ, ប្រទេសកម្ពុជា'],
            ['key' => 'currency_symbol', 'value' => '$'],
            ['key' => 'tax_rate', 'value' => '10'], // ពន្ធដំបូង 10%
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
