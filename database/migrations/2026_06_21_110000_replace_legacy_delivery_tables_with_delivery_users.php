<?php

use App\Enums\DeliveryStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->string('img')->nullable();
            $table->string('front_ident');
            $table->string('back_ident');
            $table->string('personal_deriving_license');
            $table->string('machine_license');
            $table->boolean('active_status')->default(false);
            $table->boolean('ban')->default(false);
            $table->string('approval_status')->default('pending');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->decimal('balance', 10, 2)->default(0);
            $table->unsignedTinyInteger('max_active_orders')->default(1);
            $table->string('fcm_token')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('delivery_users')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['pickup', 'delivered_cod', 'delivered_online']);
            $table->decimal('amount', 10, 2);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['delivery_id', 'type']);
            $table->index('order_id');
        });

        Schema::create('delivery_refused_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('delivery_users')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['delivery_id', 'order_id']);
            $table->index('order_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_id')
                ->nullable()
                ->after('vendor_id')
                ->constrained('delivery_users')
                ->nullOnDelete();
            $table->string('delivery_status')
                ->default('pending')
                ->after('delivery_id');

            $table->index('delivery_status');
        });

        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('drivers');
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('delivery_id');
            $table->dropIndex(['delivery_status']);
            $table->dropColumn('delivery_status');
        });

        Schema::dropIfExists('delivery_refused_orders');
        Schema::dropIfExists('delivery_wallet_transactions');
        Schema::dropIfExists('delivery_users');

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->boolean('is_busy')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

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
};
