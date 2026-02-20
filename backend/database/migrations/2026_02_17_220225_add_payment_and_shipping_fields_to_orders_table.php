<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Solo agregar columnas que no existen
            if (!Schema::hasColumn('orders', 'shipping_name')) {
                $table->string('shipping_name')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'shipping_lastname')) {
                $table->string('shipping_lastname')->nullable()->after('shipping_name');
            }
            if (!Schema::hasColumn('orders', 'shipping_dni')) {
                $table->string('shipping_dni')->nullable()->after('shipping_lastname');
            }
            if (!Schema::hasColumn('orders', 'phone')) {
                $table->string('phone')->nullable()->after('shipping_dni');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_name', 'shipping_lastname', 'shipping_dni', 'phone']);
        });
    }
};