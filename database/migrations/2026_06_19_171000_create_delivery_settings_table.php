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
        if (!Schema::hasTable('delivery_settings')) {
            Schema::create('delivery_settings', function (Blueprint $table) {
                $table->id();
                $table->decimal('price_per_km', 10, 2)->default(0.00);
                $table->decimal('min_delivery_fee', 10, 2)->default(0.00);
                $table->timestamps();
            });
        } else {
            Schema::table('delivery_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('delivery_settings', 'min_delivery_fee')) {
                    $table->decimal('min_delivery_fee', 10, 2)->default(0.00)->after('price_per_km');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_settings');
    }
};
