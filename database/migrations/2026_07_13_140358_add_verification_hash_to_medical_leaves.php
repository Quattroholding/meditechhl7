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
        Schema::table('medical_leaves', function (Blueprint $table) {
            $table->string('verification_hash')->unique()->nullable()->after('identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_leaves', function (Blueprint $table) {
            $table->dropColumn('verification_hash');
        });
    }
};
