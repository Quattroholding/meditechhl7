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
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Procedure resource ID');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('encounter_id')->nullable()->constrained('encounters');
            $table->foreignId('practitioner_id')->nullable()->constrained('practitioners');
            $table->foreignId('medical_history_id')->nullable()->constrained('medical_histories')->cascadeOnDelete();
            $table->string('identifier')->nullable();
            $table->enum('status', ['preparation', 'in-progress', 'not-done', 'on-hold', 'stopped', 'completed', 'entered-in-error', 'unknown']);
            $table->string('code')->comment('Código CPT o otro sistema');
            $table->text('reason')->nullable();
            $table->dateTime('performed_date')->nullable();
            $table->json('performer')->nullable();
            $table->json('body_site')->nullable();
            $table->json('report')->nullable();
            $table->json('complication')->nullable();
            $table->json('follow_up')->nullable();
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
        Schema::dropIfExists('procedures');
    }
};
