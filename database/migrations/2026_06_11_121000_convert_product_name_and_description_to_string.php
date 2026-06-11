<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_text')->nullable()->after('name');
            $table->text('description_text')->nullable()->after('description');
        });

        DB::table('products')->orderBy('id')->get(['id', 'name', 'description'])->each(function ($product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'name_text' => $this->extractText($product->name),
                    'description_text' => $this->extractText($product->description),
                ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('name_text', 'name');
            $table->renameColumn('description_text', 'description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('name_json')->nullable()->after('name');
            $table->json('description_json')->nullable()->after('description');
        });

        DB::table('products')->orderBy('id')->get(['id', 'name', 'description'])->each(function ($product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'name_json' => json_encode(['value' => $product->name], JSON_UNESCAPED_UNICODE),
                    'description_json' => json_encode(['value' => $product->description], JSON_UNESCAPED_UNICODE),
                ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('name_json', 'name');
            $table->renameColumn('description_json', 'description');
        });
    }

    private function extractText(mixed $value): string
    {
        if (is_array($value)) {
            return trim((string) ($value['ar'] ?? $value['value'] ?? reset($value) ?: ''));
        }

        if (! is_string($value) || $value === '') {
            return '';
        }

        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return trim((string) ($decoded['ar'] ?? $decoded['value'] ?? reset($decoded) ?: ''));
        }

        return trim($value);
    }
};
