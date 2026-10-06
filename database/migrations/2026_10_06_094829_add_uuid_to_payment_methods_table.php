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
        if (! Schema::hasColumn('payment_methods', 'uuid')) {
            Schema::table('payment_methods', function (Blueprint $table) {
                $table->char('uuid', 36)->nullable()->unique()->after('id');
            });

            // Generate UUIDs for existing records using raw SQL
            DB::statement('UPDATE payment_methods SET uuid = UUID() WHERE uuid IS NULL');
        } else {
            // If column exists but might have nulls, generate UUIDs for them
            DB::table('payment_methods')
                ->whereNull('uuid')
                ->orWhere('uuid', '')
                ->update(['uuid' => DB::raw('UUID()')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
