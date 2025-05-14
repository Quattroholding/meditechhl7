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
        Schema::create('allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_history_id')->constrained('medical_histories')->cascadeOnDelete();
            $table->string('code')->comment('Código SNOMED/AllergyIntolerance');
            $table->string('substance')->comment('Sustancia que causa alergia');
            $table->enum('type', ['allergy', 'intolerance']);
            $table->enum('category', [
                'food',
                'medication',
                'environment',
                'biologic',
                'other'
            ]);
            $table->enum('criticality', ['low', 'high', 'unable-to-assess'])->nullable();
            $table->enum('reaction_severity', ['mild', 'moderate', 'severe'])->nullable();
            $table->text('reaction_manifestation')->nullable()->comment('Síntomas de la reacción');
            $table->text('reaction_description')->nullable();
            $table->date('reaction_onset')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['medical_history_id', 'substance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergies');
    }
};
