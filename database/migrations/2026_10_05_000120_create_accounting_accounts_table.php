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
        if (Schema::hasTable('accounting_accounts')) {
            return;
        }

        Schema::create('accounting_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('client_id')->constrained('clients');
            $table->string('code', 20);
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->enum('account_type', ['asset', 'liability', 'equity', 'income', 'expense', 'cost']);
            $table->foreignId('parent_id')->nullable()->constrained('accounting_accounts');
            $table->integer('level')->default(1);
            $table->boolean('allows_transaction')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->decimal('balance', 15, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['client_id', 'code']);
            $table->index(['client_id', 'status']);
            $table->index('parent_id');
            $table->unique(['client_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_accounts');
    }
};
