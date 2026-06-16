<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vendor_withdrawal_request_orders')) {
            Schema::drop('vendor_withdrawal_request_orders');
        }

        Schema::create('vendor_withdrawal_request_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_withdrawal_request_id');
            $table->unsignedBigInteger('order_id');
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->unique(['vendor_withdrawal_request_id', 'order_id'], 'withdraw_request_order_unique');
            $table->foreign('vendor_withdrawal_request_id', 'vwro_withdraw_req_fk')
                ->references('id')
                ->on('vendor_withdrawal_requests')
                ->cascadeOnDelete();
            $table->foreign('order_id', 'vwro_order_fk')
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_withdrawal_request_orders');
    }
};
