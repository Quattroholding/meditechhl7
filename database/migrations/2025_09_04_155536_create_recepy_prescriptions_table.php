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
        Schema::create('recepy_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_profile_id')->constrained('recepy_doctor_profiles')->onDelete('cascade');
            $table->string('patient_name');
            $table->string('patient_document')->nullable(); // Documento de identidad del paciente
            $table->date('patient_birth_date')->nullable();
            $table->enum('patient_gender', ['M', 'F', 'O'])->nullable(); // M: Masculino, F: Femenino, O: Otro
            $table->text('patient_address')->nullable();
            $table->string('patient_phone', 20)->nullable();
            $table->text('diagnosis')->nullable(); // Diagnóstico
            $table->text('additional_notes')->nullable(); // Notas adicionales
            $table->date('prescription_date');
            $table->string('prescription_number')->unique(); // Número único de la receta
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
            
            $table->index('doctor_profile_id');
            $table->index('prescription_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepy_prescriptions');
    }
};
