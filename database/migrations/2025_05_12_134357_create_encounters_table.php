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
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Encounter resource ID');
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('practitioner_id')->constrained('practitioners');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('identifier')->unique()->comment('Identificador único del encuentro');
            $table->enum('status', ['planned', 'arrived', 'triaged', 'in-progress', 'onleave', 'finished', 'cancelled']);
            $table->enum('class', ['AMB', 'EMER', 'FLD', 'HH', 'IMP', 'ACUTE', 'NONAC', 'OBSENC', 'PRENC', 'SS', 'VR']);
            $table->enum('type', ['AMB', 'EMER', 'FLD', 'HH', 'IMP', 'ACUTE', 'NONAC', 'OBSENC', 'PRENC', 'SS', 'VR']);
            $table->string('priority')->nullable();
            $table->text('reason')->nullable()->comment('Motivo de la consulta');
            $table->json('diagnosis')->nullable()->comment('Diagnósticos relacionados');
            $table->json('hospitalization')->nullable()->comment('Datos de hospitalización si aplica');
            $table->json('location')->nullable()->comment('Ubicaciones durante el encuentro');
            $table->json('extension')->nullable()->comment('Extensiones FHIR');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
