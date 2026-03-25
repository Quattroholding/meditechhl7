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
        Schema::create('practitioner_insurance_company', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practitioner_id')->constrained()->onDelete('cascade');
            $table->foreignId('insurance_company_id')->constrained()->onDelete('cascade');
            $table->boolean('accepts')->default(true);
            $table->decimal('custom_coverage_percentage', 5, 2)->nullable();
            $table->decimal('custom_copay_amount', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['practitioner_id', 'insurance_company_id'], 'practitioner_insurance_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practitioner_insurance_company');
    }
};
