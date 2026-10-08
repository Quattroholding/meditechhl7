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
        if (! Schema::hasTable('treasury_movements')) {
            Schema::create('treasury_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
                $table->string('movement_number')->unique();
                $table->date('movement_date');
                $table->enum('movement_type', ['deposit', 'withdrawal', 'transfer', 'adjustment'])->default('deposit');
                $table->string('source_type')->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->foreignId('bank_id')->nullable()->constrained('banks')->cascadeOnDelete();
                $table->foreignId('cash_register_id')->nullable()->constrained('cash_registers')->cascadeOnDelete();
                $table->decimal('amount', 15, 2);
                $table->text('description');
                $table->string('reference_number')->nullable();
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->cascadeOnDelete();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('client_id');
                $table->index('movement_type');
                $table->index('movement_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treasury_movements');
    }
};
