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
        Schema::table('encounters', function (Blueprint $table) {
            $table->string('source_system', 50)->default('SAMI')->after('fhir_id');
        });
        Schema::table('vital_signs', function (Blueprint $table) {
            $table->string('source_system', 50)->default('SAMI')->after('fhir_id');
        });
        Schema::table('present_illnesses', function (Blueprint $table) {
            $table->string('source_system', 50)->default('SAMI')->after('fhir_id');
        });
        Schema::table('physical_exams', function (Blueprint $table) {
            $table->string('source_system', 50)->default('SAMI')->after('fhir_id');
        });
        Schema::table('medication_requests', function (Blueprint $table) {
            $table->string('source_system', 50)->default('SAMI')->after('fhir_id');
        });
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('source_system', 50)->default('SAMI')->after('fhir_id');
        });
        Schema::table('conditions', function (Blueprint $table) {
            $table->string('source_system', 50)->default('SAMI')->after('fhir_id');
        });
        Schema::table('patients', function (Blueprint $table) {
            $table->string('source_system', 50)->default('SAMI')->after('fhir_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encounters', function (Blueprint $table) {
            $table->dropColumn('source_system');
        });
        Schema::table('vital_signs', function (Blueprint $table) {
            $table->dropColumn('source_system');
        });
        Schema::table('present_illnesses', function (Blueprint $table) {
            $table->dropColumn('source_system');
        });
        Schema::table('physical_exams', function (Blueprint $table) {
            $table->dropColumn('source_system');
        });
        Schema::table('medication_requests', function (Blueprint $table) {
            $table->dropColumn('source_system');
        });
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn('source_system');
        });
        Schema::table('conditions', function (Blueprint $table) {
            $table->dropColumn('source_system');
        });
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('source_system');
        });
    }
};
