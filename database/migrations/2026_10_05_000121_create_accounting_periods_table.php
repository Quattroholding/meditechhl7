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
        if (Schema::hasTable('accounting_periods')) {
            return;
        }

        Schema::create('accounting_periods', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('client_id')->constrained('clients');
            $table->string('name', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('fiscal_year');
            $table->integer('period_number');
            $table->enum('status', ['open', 'closed', 'locked'])->default('open');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['client_id', 'fiscal_year']);
            $table->index(['client_id', 'status']);
            $table->unique(['client_id', 'fiscal_year', 'period_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_periods');
    }
};
