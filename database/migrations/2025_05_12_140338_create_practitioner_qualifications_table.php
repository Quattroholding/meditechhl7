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
        Schema::create('practitioner_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practitioner_id')->constrained('practitioners')->cascadeOnDelete();
            $table->string('code'); // Código de especialidad
            $table->string('system')->nullable(); // Sistema de codificación (ej. 'http://snomed.info/sct')
            $table->string('display'); // Nombre legible
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('issuer')->nullable(); // Quien emitió la calificación
            $table->softDeletes();
            $table->timestamps();
            $table->index(['practitioner_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practitioner_qualifications');
    }
};
