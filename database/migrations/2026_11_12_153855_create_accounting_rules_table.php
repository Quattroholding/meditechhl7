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
        Schema::create('accounting_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('account_type'); // Maps to AccountType enum: ASSET, LIABILITY, EQUITY, INCOME, COST, EXPENSE
            $table->enum('nature', ['debit', 'credit']);
            $table->boolean('requires_cost_center')->default(false);
            $table->boolean('is_system_account')->default(false);
            $table->boolean('is_control_account')->default(false);
            $table->boolean('closes_period')->default(false);
            $table->foreignId('default_debit_account_id')->nullable()->constrained('accounting_accounts')->nullOnDelete();
            $table->foreignId('default_credit_account_id')->nullable()->constrained('accounting_accounts')->nullOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_rules');
    }
};
