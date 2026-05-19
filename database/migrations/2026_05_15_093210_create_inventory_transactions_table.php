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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_type', 50)->comment('purchase, adjustment, transfer, supply_delivery, return, disposal, count');
            $table->dateTime('transaction_date')->index();

            // Item details
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('quantity_change', 10, 2)->comment('Positive (in) or negative (out)');
            $table->string('unit_of_measure', 50);
            $table->decimal('quantity_before', 10, 2)->comment('Balance before transaction');
            $table->decimal('quantity_after', 10, 2)->comment('Balance after transaction');

            // Location tracking
            $table->foreignId('from_location_client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->foreignId('from_location_branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('from_location_practitioner_id')->nullable()->constrained('practitioners')->onDelete('cascade');
            $table->foreignId('from_location_consulting_room_id')->nullable()->constrained('consulting_rooms')->onDelete('cascade');

            $table->foreignId('to_location_client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->foreignId('to_location_branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('to_location_practitioner_id')->nullable()->constrained('practitioners')->onDelete('cascade');
            $table->foreignId('to_location_consulting_room_id')->nullable()->constrained('consulting_rooms')->onDelete('cascade');

            // Lot tracking
            $table->string('lot_number', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->date('expiration_date')->nullable();

            // Cost tracking
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();

            // References
            $table->foreignId('supply_request_id')->nullable()->constrained('supply_requests')->onDelete('set null');
            $table->foreignId('supply_delivery_id')->nullable()->constrained('supply_deliveries')->onDelete('set null');
            $table->foreignId('charge_item_id')->nullable()->constrained('charge_items')->onDelete('set null');
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->onDelete('cascade');

            // Audit
            $table->foreignId('performed_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('authorized_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();

            // Multi-tenancy
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            $table->timestamps();

            $table->index(['inventory_item_id', 'transaction_date']);
            $table->index(['transaction_type', 'transaction_date']);
            $table->index(['patient_id', 'transaction_date']);
            $table->index(['client_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
