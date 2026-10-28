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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('order_type')->default('offline')->after('sale_date'); // 'offline' (kasir) atau 'online' (web)
            $table->string('status')->default('completed')->after('order_type'); // 'pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->text('shipping_address')->nullable()->after('customer_phone');
            $table->date('delivery_date')->nullable()->after('sale_date');
            $table->string('payment_proof')->nullable()->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'status', 'customer_phone', 'shipping_address', 'delivery_date', 'payment_proof']);
        });
    }
};
