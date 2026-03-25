<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_ingredients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medication_id')
                ->constrained('medications')
                ->cascadeOnDelete();

            // Sustancia activa
            $table->string('substance_code', 100)->nullable(); // SNOMED / RxNorm
            $table->string('substance_display', 255); // Nombre de la sustancia

            // Concentración
            $table->decimal('strength_value', 10, 2)->nullable();
            $table->string('strength_unit', 50)->nullable(); // mg, ml, %

            $table->timestamps();

            // Índices
            $table->index('substance_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_ingredients');
    }
};
