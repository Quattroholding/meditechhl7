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
        if (! Schema::hasTable('accounting_event_configs')) {
            Schema::create('accounting_event_configs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->foreignId('accounting_event_id')->constrained('accounting_events')->cascadeOnDelete();
                $table->foreignId('debit_account_id')->constrained('accounting_accounts')->cascadeOnDelete();
                $table->foreignId('credit_account_id')->constrained('accounting_accounts')->cascadeOnDelete();
                $table->foreignId('default_cost_center_id')->nullable()->constrained('cost_centers')->cascadeOnDelete();
                $table->boolean('auto_generate')->default(true);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                // Indexes
                $table->index('client_id');
                $table->index('accounting_event_id');
                $table->index('status');
                $table->unique(['client_id', 'accounting_event_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_event_configs');
    }
};
