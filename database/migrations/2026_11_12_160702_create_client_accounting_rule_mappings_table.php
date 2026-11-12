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
        Schema::create('client_accounting_rule_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->enum('rule_type', ['income', 'receivable', 'expense', 'bank'])->comment('Type of accounting rule');
            $table->foreignId('accounting_rule_id')->constrained('accounting_rules')->cascadeOnDelete();
            $table->foreignId('accounting_account_id')->constrained('accounting_accounts')->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint: one mapping per client + rule_type
            $table->unique(['client_id', 'rule_type']);
            $table->index(['client_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_accounting_rule_mappings');
    }
};
