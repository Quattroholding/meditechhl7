<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('cash_registers', 'uuid')) {
            Schema::table('cash_registers', function (Blueprint $table) {
                $table->char('uuid', 36)->nullable()->after('id');
            });
        }

        // Generate UUIDs for existing records
        DB::table('cash_registers')->whereNull('uuid')->orderBy('id')->each(function ($record) {
            DB::table('cash_registers')
                ->where('id', $record->id)
                ->update(['uuid' => Str::uuid()]);
        });

        // Make uuid not nullable and unique if not already
        if (Schema::hasColumn('cash_registers', 'uuid')) {
            Schema::table('cash_registers', function (Blueprint $table) {
                // Only add unique constraint if it doesn't exist
                $indexExists = DB::select("SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'cash_registers' AND COLUMN_NAME = 'uuid' AND SEQ_IN_INDEX = 1 AND NON_UNIQUE = 0", [DB::getDatabaseName()]);
                if (empty($indexExists)) {
                    $table->unique('uuid');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_registers', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
