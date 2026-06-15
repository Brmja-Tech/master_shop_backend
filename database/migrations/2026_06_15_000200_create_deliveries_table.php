<?php

use App\Enums\DeliveryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status')->default(DeliveryStatus::Searching->value);

            $table->string('pickup_address');
            $table->decimal('pickup_latitude', 10, 7);
            $table->decimal('pickup_longitude', 10, 7);

            $table->string('dropoff_address');
            $table->decimal('dropoff_latitude', 10, 7)->nullable();
            $table->decimal('dropoff_longitude', 10, 7)->nullable();

            $table->decimal('distance_km', 8, 2)->nullable();
            $table->unsignedInteger('estimated_minutes')->nullable();
            $table->decimal('driver_earnings', 10, 2)->default(0);

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->unsignedTinyInteger('driver_rating')->nullable();
            $table->text('driver_rating_comment')->nullable();
            $table->text('failure_reason')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('driver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
