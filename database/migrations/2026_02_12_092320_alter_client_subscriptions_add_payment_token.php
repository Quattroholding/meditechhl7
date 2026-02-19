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
        Schema::table('client_subscriptions', function (Blueprint $table) {

            $table->foreignId('payment_token_id')
                ->nullable()
                ->after('package_id')
                ->constrained('payment_tokens')
                ->nullOnDelete();

            $table->timestamp('last_billed_at')->nullable();
            $table->timestamp('next_retry_at')->nullable();

            $table->integer('retry_count')->default(0);

            $table->timestamp('grace_period_ends_at')->nullable();

            $table->string('gateway_customer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['payment_token_id']);
            $table->dropColumn([
                'payment_token_id',
                'last_billed_at',
                'next_retry_at',
                'retry_count',
                'grace_period_ends_at',
                'gateway_customer_id',
            ]);
        });
    }
};
