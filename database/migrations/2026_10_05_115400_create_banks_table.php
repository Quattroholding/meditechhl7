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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('account_number')->unique();
            $table->enum('account_type', ['checking', 'savings'])->default('checking');
            $table->string('currency')->default('PAB');
            $table->foreignId('accounting_account_id')->constrained('accounting_accounts')->cascadeOnDelete();
            $table->decimal('balance', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('client_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
