<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vendor_withdrawal_requests')) {
            return;
        }

        Schema::create('vendor_withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('method');
            $table->string('transfer_details');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending');
            $table->text('admin_note')->nullable();
            $table->foreignId('processed_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_withdrawal_requests');
    }
};
