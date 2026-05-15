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
        Schema::create('supply_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fhir_id')->unique()->comment('UUID for FHIR resource');
            $table->string('identifier')->unique()->comment('Human-readable identifier');
            $table->string('status', 50)->default('completed')->comment('in-progress, completed, abandoned, entered-in-error');

            // Reference to supply request
            $table->foreignId('based_on_supply_request_id')->nullable()->constrained('supply_requests')->onDelete('set null');

            // Supply details
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('supplied_quantity', 10, 2);
            $table->string('unit_of_measure', 50);

            // Lot tracking (captured at delivery time)
            $table->string('lot_number', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->date('expiration_date')->nullable();

            // Clinical context
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('encounter_id')->constrained('encounters')->onDelete('cascade');
            $table->foreignId('practitioner_id')->constrained('practitioners')->onDelete('cascade')->comment('Supplier');

            // Timing
            $table->dateTime('occurrence_datetime')->comment('When supply was delivered');

            // Multi-tenancy (location of origin)
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('practitioner_inventory_id')->nullable()->constrained('practitioners')->onDelete('cascade')->comment('If from practitioner inventory');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['encounter_id', 'status']);
            $table->index(['based_on_supply_request_id']);
            $table->index(['patient_id']);
            $table->index(['inventory_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_deliveries');
    }
};
