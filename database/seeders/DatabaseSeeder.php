<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ១. បង្កើតគណនី Admin គំរូ
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );

        // ២. បង្កើតគណនី Staff គំរូ
        User::updateOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Stock Staff',
                'password' => Hash::make('12345678'),
                'role' => 'staff',
            ]
        );

        // ៣. បង្កើតគណនី Cashier គំរូ
        User::updateOrCreate(
            ['email' => 'cashier@gmail.com'],
            [
                'name' => 'POS Cashier',
                'password' => Hash::make('12345678'),
                'role' => 'cashier',
            ]
        );
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

