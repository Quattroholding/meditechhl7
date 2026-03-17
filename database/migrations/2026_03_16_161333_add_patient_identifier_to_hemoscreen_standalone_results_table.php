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
        Schema::table('hemoscreen_standalone_results', function (Blueprint $table) {
            $table->string('patient_identifier', 255)->nullable()->after('device_serial');
            $table->index('patient_identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hemoscreen_standalone_results', function (Blueprint $table) {
            $table->dropIndex(['patient_identifier']);
            $table->dropColumn('patient_identifier');
        });
    }
};
