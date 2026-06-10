<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->text('description')->nullable()->after('store_name');
            $table->time('work_from')->nullable()->after('working_hours');
            $table->time('work_to')->nullable()->after('work_from');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'work_from',
                'work_to',
            ]);
        });
    }
};
