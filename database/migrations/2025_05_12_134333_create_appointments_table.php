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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Appointment resource ID');
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('practitioner_id')->constrained('practitioners');
            $table->string('identifier')->unique()->comment('Identificador único de la cita');
            $table->enum('status', ['proposed', 'pending', 'booked', 'arrived', 'fulfilled', 'cancelled', 'noshow', 'entered-in-error', 'checked-in', 'waitlist']);
            $table->string('service_type')->comment('Tipo de servicio o especialidad');
            $table->text('description')->nullable();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->foreignId('consulting_room_id')->nullable()->references('id')->on('consulting_rooms')->onDelete('cascade');
            $table->foreignId('medical_speciality_id')->nullable()->references('id')->on('medical_specialties')->onDelete('cascade');
            $table->integer('minutes_duration')->nullable();
            $table->json('participant')->nullable()->comment('Otros participantes en la cita');
            $table->json('extension')->nullable()->comment('Extensiones FHIR');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
