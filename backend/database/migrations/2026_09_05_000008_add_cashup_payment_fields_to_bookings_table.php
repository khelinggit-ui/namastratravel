<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('id');
            $table->string('payment_status')->nullable()->after('status');
            $table->string('payment_provider')->nullable()->after('payment_status');
            $table->string('payment_order_id')->nullable()->unique()->after('payment_provider');
            $table->string('payment_invoice_number')->nullable()->after('payment_order_id');
            $table->unsignedBigInteger('payment_amount')->nullable()->after('payment_invoice_number');
            $table->string('payment_url', 2048)->nullable()->after('payment_amount');
            $table->timestamp('paid_at')->nullable()->after('payment_url');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['payment_order_id']);
            $table->dropColumn([
                'customer_name',
                'payment_status',
                'payment_provider',
                'payment_order_id',
                'payment_invoice_number',
                'payment_amount',
                'payment_url',
                'paid_at',
            ]);
        });
    }
};
