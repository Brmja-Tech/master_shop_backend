<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();

            $table->string('owner_name');

            $table->string('phone')->unique();

            $table->string('password');

            $table->string('store_name');

            $table->foreignId('store_type_id')
                ->constrained('store_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->decimal('latitude', 10, 7)->nullable();

            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('address_description')->nullable();

            $table->string('logo')->nullable();

            $table->string('banner')->nullable();

            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('rate', 3, 2)->default(0);

            $table->boolean('is_active')->default(false);

            $table->string('working_hours')->nullable();

            $table->boolean('is_verified')->default(false);

            $table->string('temp_token')->nullable();

            $table->text('fcm_token')->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
