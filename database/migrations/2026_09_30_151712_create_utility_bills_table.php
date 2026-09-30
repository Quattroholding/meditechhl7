<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utility_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_upload_id')
                ->constrained('document_uploads')
                ->cascadeOnDelete();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();
            $table->enum('bill_type', ['electricity', 'water', 'gas', 'other']);
            $table->string('bill_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('service_address')->nullable();
            $table->date('billing_period_start')->nullable();
            $table->date('billing_period_end')->nullable();
            $table->decimal('consumption_kwh', 12, 2)->nullable();
            $table->decimal('consumption_cubic_meters', 12, 2)->nullable();
            $table->decimal('consumption_cubic_feet', 12, 2)->nullable();
            $table->decimal('consumption_value', 12, 2)->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('bill_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utility_bills');
    }
};
