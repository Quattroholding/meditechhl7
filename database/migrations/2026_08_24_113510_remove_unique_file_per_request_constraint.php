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
        Schema::table('service_request_results', function (Blueprint $table) {
            $table->dropUnique('unique_file_per_request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_request_results', function (Blueprint $table) {
            $table->unique(['file_hash', 'service_request_id'], 'unique_file_per_request');
        });
    }
};
