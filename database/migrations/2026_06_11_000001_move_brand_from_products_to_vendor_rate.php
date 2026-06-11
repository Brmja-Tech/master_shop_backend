<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('vendors', 'rate')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->decimal('rate', 3, 2)->default(0)->after('delivery_fee');
            });
        }

        if (Schema::hasColumn('products', 'brand')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('brand');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('products', 'brand')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('brand')->nullable()->after('description');
            });
        }

        if (Schema::hasColumn('vendors', 'rate')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->dropColumn('rate');
            });
        }
    }
};
