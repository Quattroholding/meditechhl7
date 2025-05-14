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
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR FamilyMemberHistory/AllergyIntolerance/Procedure resource ID');
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->enum('category', [
                'allergy',
                'surgery',
                'chronic-illness',
                'hospitalization',
                'immunization',
                'family-history',
                'social-history',
                'other'
            ]);
            $table->string('title')->comment('Título descriptivo del antecedente');
            $table->text('description')->nullable();
            $table->date('recorded_date')->nullable()->comment('Fecha cuando se registró el antecedente');
            $table->date('occurrence_date')->nullable()->comment('Fecha cuando ocurrió el evento');
            $table->enum('clinical_status', ['active', 'recurrence', 'inactive', 'remission', 'resolved'])->nullable();
            $table->enum('verification_status', ['unconfirmed', 'provisional', 'differential', 'confirmed', 'refuted', 'entered-in-error'])->nullable();
            $table->json('extension')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_histories');
    }
};
