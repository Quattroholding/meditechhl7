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
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->foreignId('encounter_id')->nullable()->after('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('practitioner_id')->nullable()->after('encounter_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->after('practitioner_id')->constrained()->onDelete('cascade');
            $table->foreignId('medical_speciality_id')->nullable()->after('client_id')->references('id')->on('medical_specialties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropColumn('encounter_id');
            $table->dropColumn('practitioner_id');
            $table->dropColumn('client_id');
            $table->dropColumn('medical_speciality_id');
        });
    }
};
