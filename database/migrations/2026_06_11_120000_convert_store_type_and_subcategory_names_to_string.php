<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_types', function (Blueprint $table) {
            $table->string('name_text')->nullable()->after('name');
        });

        DB::table('store_types')->orderBy('id')->get(['id', 'name'])->each(function ($storeType) {
            DB::table('store_types')
                ->where('id', $storeType->id)
                ->update(['name_text' => $this->extractArabicName($storeType->name)]);
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->string('name_text')->nullable()->after('name');
        });

        DB::table('subcategories')->orderBy('id')->get(['id', 'name'])->each(function ($subcategory) {
            DB::table('subcategories')
                ->where('id', $subcategory->id)
                ->update(['name_text' => $this->extractArabicName($subcategory->name)]);
        });

        Schema::table('store_types', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('store_types', function (Blueprint $table) {
            $table->renameColumn('name_text', 'name');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->renameColumn('name_text', 'name');
        });
    }

    public function down(): void
    {
        Schema::table('store_types', function (Blueprint $table) {
            $table->json('name_json')->nullable()->after('name');
        });

        DB::table('store_types')->orderBy('id')->get(['id', 'name'])->each(function ($storeType) {
            DB::table('store_types')
                ->where('id', $storeType->id)
                ->update(['name_json' => json_encode(['ar' => $storeType->name], JSON_UNESCAPED_UNICODE)]);
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->json('name_json')->nullable()->after('name');
        });

        DB::table('subcategories')->orderBy('id')->get(['id', 'name'])->each(function ($subcategory) {
            DB::table('subcategories')
                ->where('id', $subcategory->id)
                ->update(['name_json' => json_encode(['ar' => $subcategory->name], JSON_UNESCAPED_UNICODE)]);
        });

        Schema::table('store_types', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('store_types', function (Blueprint $table) {
            $table->renameColumn('name_json', 'name');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('subcategories', function (Blueprint $table) {
            $table->renameColumn('name_json', 'name');
        });
    }

    private function extractArabicName(mixed $value): string
    {
        if (is_array($value)) {
            return trim((string) ($value['ar'] ?? reset($value) ?: ''));
        }

        if (! is_string($value) || $value === '') {
            return '';
        }

        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return trim((string) ($decoded['ar'] ?? reset($decoded) ?: ''));
        }

        return trim($value);
    }
};
