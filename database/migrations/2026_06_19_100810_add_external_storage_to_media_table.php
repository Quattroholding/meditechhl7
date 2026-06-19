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
        Schema::table('media', function (Blueprint $table) {
            $table->string('storage_disk')->default('public')->after('file_path');
            $table->string('external_id')->nullable()->after('storage_disk');
            $table->text('external_metadata')->nullable()->after('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['storage_disk', 'external_id', 'external_metadata']);
        });
    }
};
