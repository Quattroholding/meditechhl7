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
        Schema::create('observation_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('observation_id')->constrained('observations')->cascadeOnDelete();
            $table->string('code'); // Código LOINC/SNOMED
            $table->string('value')->nullable();
            $table->string('unit')->nullable();
            $table->string('interpretation')->nullable();
            $table->json('reference_range')->nullable();
            $table->timestamps();

            $table->index(['observation_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observation_components');
    }
};
