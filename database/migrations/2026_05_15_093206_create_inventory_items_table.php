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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fhir_id')->unique()->comment('UUID for FHIR resource');
            $table->string('status', 50)->default('active')->comment('active, inactive, entered-in-error, unknown');
            $table->json('category')->nullable()->comment('CodeableConcept - medical-supply, medication, device');
            $table->json('code')->nullable()->comment('SNOMED, GTIN, custom codes');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('sku', 100)->unique()->comment('Stock Keeping Unit');
            $table->string('barcode', 100)->nullable()->index();
            $table->string('item_type', 50)->comment('supply, consumable, equipment, medication, device');
            $table->decimal('base_cost', 10, 2)->nullable()->comment('Cost of acquisition');
            $table->decimal('base_price', 10, 2)->nullable()->comment('Default selling price');
            $table->string('currency', 10)->default('USD');
            $table->string('unit_of_measure', 50)->default('unit')->comment('unit, box, vial, ml, etc');
            $table->boolean('requires_prescription')->default(false);
            $table->boolean('track_by_lot')->default(false);
            $table->boolean('track_by_serial')->default(false);
            $table->boolean('expiration_tracking')->default(false);
            $table->integer('reorder_point')->nullable()->comment('Minimum quantity before reorder alert');
            $table->integer('reorder_quantity')->nullable()->comment('Quantity to order when below reorder_point');
            $table->json('characteristic')->nullable()->comment('Additional FHIR characteristics');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['client_id', 'item_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
