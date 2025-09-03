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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('primary_insurance_id')->nullable()->constrained('patient_insurance_policies')->onDelete('set null');
            $table->foreignId('secondary_insurance_id')->nullable()->constrained('patient_insurance_policies')->onDelete('set null');
            $table->decimal('insurance_billed_amount', 10, 2)->default(0.00);
            $table->decimal('insurance_paid_amount', 10, 2)->default(0.00);
            $table->decimal('insurance_pending_amount', 10, 2)->default(0.00);
            $table->decimal('patient_copay_amount', 10, 2)->default(0.00);
            $table->decimal('patient_deductible_amount', 10, 2)->default(0.00);
            $table->decimal('patient_coinsurance_amount', 10, 2)->default(0.00);
            $table->decimal('patient_responsibility_amount', 10, 2)->default(0.00);
            $table->decimal('patient_paid_amount', 10, 2)->default(0.00);
            $table->decimal('patient_balance_amount', 10, 2)->default(0.00);
            $table->enum('insurance_status', ['none', 'pending', 'submitted', 'processing', 'approved', 'partially_paid', 'paid', 'denied'])->default('none');
            $table->boolean('has_insurance')->default(false);
            $table->text('insurance_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['primary_insurance_id']);
            $table->dropForeign(['secondary_insurance_id']);
            $table->dropColumn([
                'primary_insurance_id',
                'secondary_insurance_id',
                'insurance_billed_amount',
                'insurance_paid_amount',
                'insurance_pending_amount',
                'patient_copay_amount',
                'patient_deductible_amount',
                'patient_coinsurance_amount',
                'patient_responsibility_amount',
                'patient_paid_amount',
                'patient_balance_amount',
                'insurance_status',
                'has_insurance',
                'insurance_notes',
            ]);
        });
    }
};
