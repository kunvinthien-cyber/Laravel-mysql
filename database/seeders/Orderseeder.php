<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Order::create(['total' => 1200, 'status' => 'completed']);
        Order::create(['total' => 50, 'status' => 'pending']);
        Order::create(['total' => 300, 'status' => 'completed']);
        Order::create(['total' => 900, 'status' => 'completed']);
    }
}
