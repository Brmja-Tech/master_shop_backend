<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('approval_status')->default('pending')->after('is_verified');
            $table->boolean('ban')->default(false)->after('is_active');
        });

        // Sync existing data: if is_verified is true, make it approved, else pending.
        DB::table('vendors')->where('is_verified', true)->update(['approval_status' => 'approved']);
        DB::table('vendors')->where('is_verified', false)->update(['approval_status' => 'pending']);
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'ban']);
        });
    }
};
