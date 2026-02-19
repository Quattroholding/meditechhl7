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
        Schema::create('subscription_billing_cycles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')
                ->constrained('client_subscriptions')
                ->cascadeOnDelete();

            $table->foreignId('invoice_id')
                ->nullable()
                ->constrained('client_invoices')
                ->nullOnDelete();

            $table->date('billing_date');
            $table->decimal('amount', 10, 2);

            $table->string('status')->default('pending');
            // pending | paid | failed

            $table->integer('attempts')->default(0);

            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('next_attempt_at')->nullable();

            $table->json('gateway_response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_billing_cycles');
    }
};
