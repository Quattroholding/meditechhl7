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
        Schema::create('clinical_observation_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Código LOINC/SNOMED');
            $table->string('name');
            $table->enum('category', ['vital_sign', 'physical_exam', 'laboratory', 'imaging']);
            $table->string('system')->nullable()->comment('Sistema corporal o área de evaluación');
            $table->string('default_unit')->nullable();
            $table->decimal('min_normal_value', 10, 2)->nullable();
            $table->decimal('max_normal_value', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('procedure')->nullable()->comment('Cómo se mide/evalúa');
            $table->json('possible_abnormalities')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_observation_types');
    }
};
