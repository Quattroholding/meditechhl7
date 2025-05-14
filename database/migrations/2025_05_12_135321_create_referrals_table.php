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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR ServiceRequest resource ID');
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('encounter_id')->nullable()->constrained('encounters');
            $table->foreignId('practitioner_id')->constrained('practitioners');
            $table->foreignId('referred_to_id')->constrained('practitioners')->comment('Especialista referido');
            $table->string('identifier')->unique();
            $table->enum('status', ['draft', 'active', 'on-hold', 'revoked', 'completed', 'entered-in-error', 'unknown']);
            $table->enum('intent', ['proposal', 'plan', 'directive', 'order', 'original-order', 'reflex-order', 'filler-order', 'instance-order', 'option']);
            $table->string('priority')->nullable();
            $table->string('code')->comment('Código del servicio solicitado');
            $table->text('reason')->nullable()->comment('Motivo de la referencia');
            $table->text('description')->nullable();
            $table->date('occurrence_date')->nullable();
            $table->json('supporting_info')->nullable();
            $table->json('extension')->nullable()->comment('Extensiones FHIR');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['encounter_id', 'patient_id', 'referred_to_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
