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
        Schema::create('recepy_doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('logo')->nullable(); // Ruta del logo de la receta
            $table->text('address')->nullable(); // Dirección del médico
            $table->string('speciality',100)->nullable(); // Especialidad medica
            $table->string('facility',100)->nullable(); // Centro médico
            $table->string('phone', 20)->nullable(); // Teléfono
            $table->string('email')->nullable(); // Email del médico
            $table->text('signature')->nullable(); // Ruta de la firma digital
            $table->text('seal')->nullable(); // Ruta del sello
            $table->string('medical_license_number')->nullable(); // Número de registro médico
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepy_doctor_profiles');
    }
};
