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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Invoice resource ID');
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('encounter_id')->nullable()->constrained('encounters');
            $table->string('identifier')->unique();
            $table->enum('status', ['draft', 'issued', 'balanced', 'cancelled', 'entered-in-error']);
            $table->date('date');
            $table->json('participant')->nullable()->comment('Participantes en la facturación');
            $table->json('line_item')->comment('Elementos facturados');
            $table->decimal('total_net', 10, 2);
            $table->decimal('total_gross', 10, 2);
            $table->string('payment_terms')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('invoices');
    }
};
