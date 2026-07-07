<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->foreignId('product_id')
                  ->nullable()
                  ->after('id')
                  ->constrained()
                  ->nullOnDelete();

            $table->foreignId('customer_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained()
                  ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropForeign(['product_id']);
            $table->dropForeign(['customer_id']);

            $table->dropColumn([
                'product_id',
                'customer_id'
            ]);

        });
    }
};
