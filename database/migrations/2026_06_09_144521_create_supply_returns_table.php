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
        Schema::create('supply_returns', function (Blueprint $table) {
            $table->id();

            // Referencias a registros originales
            $table->foreignId('supply_delivery_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supply_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();

            // Contexto clínico
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('encounter_id')->constrained()->cascadeOnDelete();

            // Detalles de la devolución
            $table->decimal('quantity_returned', 10, 2);
            $table->decimal('quantity_originally_dispensed', 10, 2);
            $table->string('unit_of_measure');
            $table->enum('reason', [
                'wrong_item',
                'wrong_quantity',
                'not_used',
                'patient_refused',
                'expired',
                'damaged',
                'other',
            ]);
            $table->text('notes')->nullable();

            // Rastreo de lote/serie
            $table->string('lot_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('expiration_date')->nullable();

            // Ubicación de devolución
            $table->foreignId('returned_to_branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->foreignId('returned_to_practitioner_id')->nullable()->constrained('practitioners')->cascadeOnDelete();

            // Auditoría
            $table->foreignId('returned_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('returned_at')->useCurrent();

            // Reversión financiera
            $table->foreignId('charge_item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('amount_reversed', 10, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('supply_delivery_id');
            $table->index('patient_id');
            $table->index('encounter_id');
            $table->index('returned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_returns');
    }
};
