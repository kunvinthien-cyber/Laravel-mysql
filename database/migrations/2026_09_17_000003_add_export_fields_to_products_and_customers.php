<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->default(0)->after('price');
            $table->string('barcode')->nullable()->after('image');
            $table->index('barcode');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('debt', 12, 2)->default(0)->after('points');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['barcode']);
            $table->dropColumn(['cost_price', 'barcode']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('debt');
        });
    }
};
