<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_withdrawal_request_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_withdrawal_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->unique(['vendor_withdrawal_request_id', 'order_id'], 'withdraw_request_order_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_withdrawal_request_orders');
    }
};
