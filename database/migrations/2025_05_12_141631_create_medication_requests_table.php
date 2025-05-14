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
        Schema::create('medication_requests', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR MedicationRequest resource ID');
            $table->foreignId('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('practitioner_id')->constrained('practitioners');
            $table->foreignId('medication_id')->constrained('medicines');
            $table->string('identifier')->unique();
            $table->enum('status', ['active', 'on-hold', 'cancelled', 'completed', 'entered-in-error', 'stopped', 'draft', 'unknown']);
            $table->enum('intent', ['proposal', 'plan', 'order', 'original-order', 'reflex-order', 'filler-order', 'instance-order', 'option']);
            $table->enum('priority', ['routine', 'urgent', 'asap', 'stat'])->default('routine');
            $table->text('reason')->nullable()->comment('Indicación/motivo');
            $table->json('dosage_instruction')->nullable();
            $table->string('dosage_text')->nullable();
            $table->string('route')->nullable();
            $table->string('frequency')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('refills')->nullable();
            $table->date('valid_from');
            $table->date('valid_to')->nullable();
            $table->boolean('substitution_allowed')->default(true);
            $table->text('note')->nullable();
            $table->json('extension')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['encounter_id', 'patient_id', 'medication_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_requests');
    }
};
