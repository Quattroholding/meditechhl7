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
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('medical_speciality_id')->nullable()->references('id')->on('medical_specialties')->onDelete('cascade');
        });

        Schema::table('encounters', function (Blueprint $table) {
            $table->foreignId('medical_speciality_id')->nullable()->references('id')->on('medical_specialties')->onDelete('cascade');
        });

        Schema::table('practitioner_qualifications', function (Blueprint $table) {
            $table->foreignId('medical_speciality_id')->nullable()->references('id')->on('medical_specialties')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('medical_speciality_id');
        });

        Schema::table('encounters', function (Blueprint $table) {
            $table->dropColumn('medical_speciality_id');
        });

        Schema::table('practitioner_qualifications', function (Blueprint $table) {
            $table->dropColumn('medical_speciality_id');
        });
    }
};
