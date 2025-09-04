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
        Schema::create('recepy_prescription_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('recepy_prescriptions')->onDelete('cascade');
            $table->string('medication_name'); // Nombre del medicamento
            $table->string('presentation')->nullable(); // Presentación (tabletas, cápsulas, jarabe, etc.)
            $table->string('concentration')->nullable(); // Concentración (ej: 500mg, 250mg/5ml)
            $table->string('dosage'); // Dosis (ej: 1 tableta, 5ml, 2 cápsulas)
            $table->string('frequency'); // Frecuencia (ej: cada 8 horas, 3 veces al día)
            $table->string('duration')->nullable(); // Duración (ej: por 7 días, por 2 semanas)
            $table->text('instructions'); // Instrucciones específicas (ej: tomar con alimentos, en ayunas)
            $table->integer('quantity')->nullable(); // Cantidad a dispensar
            $table->integer('line_order')->default(1); // Orden de la línea en la receta
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('prescription_id');
            $table->index(['prescription_id', 'line_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepy_prescription_medications');
    }
};
