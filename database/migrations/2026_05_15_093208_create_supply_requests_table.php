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
        Schema::create('supply_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fhir_id')->unique()->comment('UUID for FHIR resource');
            $table->string('identifier')->unique()->comment('Human-readable identifier');
            $table->string('status', 50)->default('draft')->comment('draft, active, suspended, cancelled, completed, entered-in-error, unknown');
            $table->string('intent', 50)->default('order')->comment('proposal, plan, order');
            $table->string('priority', 50)->default('routine')->comment('routine, urgent, asap, stat');

            // Supply details
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->string('unit_of_measure', 50);

            // Clinical context
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('encounter_id')->constrained('encounters')->onDelete('cascade');
            $table->foreignId('practitioner_id')->constrained('practitioners')->onDelete('cascade')->comment('Requester');

            // Pricing
            $table->boolean('is_billable')->default(true);
            $table->boolean('is_free')->default(false)->comment('Gift to patient');
            $table->decimal('custom_price', 10, 2)->nullable()->comment('Override base price');

            // FHIR fields
            $table->json('reason_code')->nullable()->comment('CodeableConcept for reason');
            $table->string('reason_reference')->nullable()->comment('Reference to Condition, Observation');
            $table->dateTime('occurrence_datetime')->nullable()->comment('When supply is needed');

            // Multi-tenancy
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('consulting_room_id')->nullable()->constrained('consulting_rooms')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['encounter_id', 'status']);
            $table->index(['patient_id', 'status']);
            $table->index(['practitioner_id', 'status']);
            $table->index(['inventory_item_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_requests');
    }
};
