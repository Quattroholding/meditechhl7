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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();

            // FHIR
            $table->uuid('fhir_id')->unique();
            $table->string('status', 20)->default('active'); // active | inactive | entered-in-error

            // Código del medicamento
            $table->string('code', 100)->nullable();
            $table->string('code_system', 150)->nullable(); // RxNorm, ATC, SNOMED
            $table->string('display', 255); // Nombre legible

            $table->string('home_name', 255)->nullable(); // Nombre legible
            $table->string('generic_name', 255)->nullable(); // Nombre legible

            // Detalles farmacéuticos
            $table->string('form', 100)->nullable(); // tablet, capsule, syrup
            $table->string('manufacturer', 255)->nullable();
            $table->boolean('is_brand')->default(false);

            // Opcional
            $table->json('fhir_payload')->nullable(); // recurso FHIR completo si lo deseas

            $table->softDeletes();
            $table->timestamps();

            // Índices
            $table->index(['code', 'code_system']);
            $table->index('display');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
