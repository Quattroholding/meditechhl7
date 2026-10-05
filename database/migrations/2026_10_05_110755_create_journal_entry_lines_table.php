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
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('journal_entry_id')->constrained('journal_entries');
            $table->foreignId('accounting_account_id')->constrained('accounting_accounts');
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers');
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('journal_entry_id');
            $table->index('accounting_account_id');
            $table->index(['debit', 'credit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
    }
};
