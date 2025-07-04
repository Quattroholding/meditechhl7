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
        Schema::create('patient_insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('insurance_company_id')->constrained('insurance_companies')->onDelete('cascade');
            $table->string('policy_number');
            $table->string('group_number')->nullable();
            $table->string('subscriber_id')->nullable();
            $table->string('subscriber_name')->nullable();
            $table->enum('relationship_to_subscriber', ['self', 'spouse', 'child', 'parent', 'other'])->default('self');
            $table->date('effective_date');
            $table->date('expiration_date')->nullable();
            $table->enum('priority', ['primary', 'secondary', 'tertiary'])->default('primary');
            $table->decimal('coverage_percentage', 5, 2)->default(0.00);
            $table->decimal('copay_amount', 10, 2)->default(0.00);
            $table->decimal('deductible_amount', 10, 2)->default(0.00);
            $table->decimal('deductible_remaining', 10, 2)->default(0.00);
            $table->decimal('out_of_pocket_max', 10, 2)->default(0.00);
            $table->decimal('out_of_pocket_remaining', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->json('coverage_details')->nullable(); // Para detalles específicos de cobertura
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['patient_id', 'priority']);
            $table->unique(['patient_id', 'policy_number', 'insurance_company_id'], 'patient_policy_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_insurance_policies');
    }
};
