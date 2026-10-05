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
        // Only create if table doesn't exist (already created in production)
        if (Schema::hasTable('journal_entries')) {
            return;
        }

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('client_id')->constrained('clients');
            $table->string('entry_number', 50)->unique();
            $table->date('entry_date');
            $table->string('document_type', 50)->nullable();
            $table->string('document_number', 100)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft');
            $table->foreignId('accounting_period_id')->constrained('accounting_periods');
            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('reversed_entry_id')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['client_id', 'entry_date']);
            $table->index(['client_id', 'status']);
            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
