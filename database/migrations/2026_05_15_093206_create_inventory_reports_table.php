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
        Schema::create('inventory_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fhir_id')->unique()->comment('UUID for FHIR resource');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->string('status', 50)->default('active')->comment('draft, requested, active, entered-in-error');

            // Flexible location: Branch OR Practitioner (or both for granular control)
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('practitioner_id')->nullable()->constrained('practitioners')->onDelete('cascade');
            $table->foreignId('consulting_room_id')->nullable()->constrained('consulting_rooms')->onDelete('cascade');

            // Stock levels
            $table->decimal('quantity_on_hand', 10, 2)->default(0)->comment('Current available quantity');
            $table->decimal('quantity_reserved', 10, 2)->default(0)->comment('Quantity reserved for orders');

            // Lot tracking
            $table->string('lot_number', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->date('expiration_date')->nullable()->index();

            // Audit
            $table->dateTime('reported_datetime')->nullable();
            $table->foreignId('reported_by_practitioner_id')->nullable()->constrained('practitioners')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();

            // Unique constraint: One active report per item per location
            $table->unique(['inventory_item_id', 'client_id', 'branch_id', 'practitioner_id', 'status'], 'unique_active_inventory_report');

            $table->index(['inventory_item_id', 'client_id']);
            $table->index(['branch_id', 'status']);
            $table->index(['practitioner_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_reports');
    }
};
