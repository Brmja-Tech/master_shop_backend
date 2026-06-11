<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')
                ->constrained('vendors')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('subcategory_id')
                ->constrained('subcategories')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->json('name');
            $table->json('description');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('remaining_quantity')->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->string('unit');
            $table->decimal('price', 10, 2);
            $table->date('expiry_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
