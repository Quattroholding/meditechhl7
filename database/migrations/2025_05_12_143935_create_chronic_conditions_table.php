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
        Schema::create('chronic_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_history_id')->constrained('medical_histories')->cascadeOnDelete();
            $table->string('code')->comment('Código ICD-10/SNOMED');
            $table->string('display')->comment('Nombre de la condición');
            $table->date('onset_date')->nullable();
            $table->date('abatement_date')->nullable();
            $table->enum('severity', ['mild', 'moderate', 'severe'])->nullable();
            $table->text('treatment')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['medical_history_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chronic_conditions');
    }
};
