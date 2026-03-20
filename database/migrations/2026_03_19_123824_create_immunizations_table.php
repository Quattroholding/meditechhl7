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
        Schema::create('immunizations', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Immunization resource ID');
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained('encounters');
            $table->foreignId('practitioner_id')->constrained('practitioners');
            $table->foreignId('vaccine_type_id')->constrained('vaccine_types');
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->foreignId('consulting_room_id')->nullable()->constrained('consulting_rooms');
            $table->string('vaccine_code')->comment('CVX vaccine code');
            $table->string('vaccine_display')->comment('Vaccine name');
            $table->dateTime('occurrence_datetime')->comment('When the vaccine was administered');
            $table->enum('status', ['completed', 'entered-in-error', 'not-done'])->default('completed');
            $table->string('lot_number')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('manufacturer')->nullable();
            $table->enum('route_code', ['IM', 'PO', 'NASAL', 'SC', 'ID'])->nullable()->comment('Route of administration: IM=intramuscular, PO=oral, NASAL=intranasal, SC=subcutaneous, ID=intradermal');
            $table->string('site_code')->nullable()->comment('Body site where vaccine was administered');
            $table->decimal('dose_quantity', 8, 2)->nullable();
            $table->string('dose_unit')->nullable()->default('mL');
            $table->integer('dose_number')->nullable()->comment('Dose number in series (1, 2, 3, etc.)');
            $table->integer('series_doses')->nullable()->comment('Total doses in series');
            $table->json('reaction')->nullable()->comment('Adverse reactions');
            $table->text('note')->nullable();
            $table->boolean('primary_source')->default(true)->comment('Whether vaccine record came from primary source');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'vaccine_type_id']);
            $table->index(['patient_id', 'occurrence_datetime']);
            $table->index(['vaccine_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunizations');
    }
};
