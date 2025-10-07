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
        Schema::create('authorization_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('practitioner_id')->constrained('practitioners')->onDelete('cascade');
            $table->string('code', 4)->comment('Código de 4 dígitos');
            $table->timestamp('expires_at')->comment('Fecha de expiración del código (máximo 1 hora)');
            $table->timestamp('used_at')->nullable()->comment('Fecha y hora en que se usó el código');
            $table->softDeletes();
            $table->timestamps();

            // Índice para búsqueda rápida de códigos válidos
            $table->index(['patient_id', 'practitioner_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authorization_codes');
    }
};
