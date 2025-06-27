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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            // FHIR Core Fields
            $table->string('fhir_id')->unique();
            $table->json('identifier')->nullable();
            $table->enum('status', ['active', 'inactive', 'entered-in-error', 'on-hold', 'unknown'])->default('active');
            $table->json('type')->nullable(); // Account type (patient, insurance, etc.)
            $table->string('name');
            $table->text('description')->nullable();

            // Financial Fields
            $table->string('currency', 3)->default('USD');
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('credit_limit', 12, 2)->nullable();
            $table->decimal('available_balance', 12, 2)->nullable();

            // Relationships
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable(); // Owner organization
            $table->unsignedBigInteger('coverage_id')->nullable(); // Insurance coverage
            $table->unsignedBigInteger('parent_account_id')->nullable(); // For sub-accounts

            // Account Management
            $table->json('guarantor')->nullable(); // Person/organization responsible for payment
            $table->date('service_period_start')->nullable();
            $table->date('service_period_end')->nullable();
            $table->json('billing_status')->nullable();
            $table->json('procedure_sequence')->nullable();

            // Business Fields
            $table->string('external_id')->nullable(); // External system ID
            $table->json('contact_details')->nullable();
            $table->text('notes')->nullable();

            // Multi-tenant
            $table->unsignedBigInteger('client_id');

            // Audit Fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['client_id', 'status']);
            $table->index(['patient_id', 'status']);
            $table->index(['organization_id', 'status']);
            $table->index(['parent_account_id']);
            $table->index(['service_period_start', 'service_period_end']);

            // Foreign Keys
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            $table->foreign('organization_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('parent_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
