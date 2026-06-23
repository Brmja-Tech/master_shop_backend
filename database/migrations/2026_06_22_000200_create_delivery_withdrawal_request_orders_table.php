<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('delivery_withdrawal_request_orders')) {
            Schema::drop('delivery_withdrawal_request_orders');
        }

        Schema::create('delivery_withdrawal_request_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_withdrawal_request_id');
            $table->unsignedBigInteger('order_id');
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->unique(['delivery_withdrawal_request_id', 'order_id'], 'delivery_withdraw_req_order_unique');
            $table->foreign('delivery_withdrawal_request_id', 'dwro_withdraw_req_fk')
                ->references('id')
                ->on('delivery_withdrawal_requests')
                ->cascadeOnDelete();
            $table->foreign('order_id', 'dwro_order_fk')
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_withdrawal_request_orders');
    }
};
