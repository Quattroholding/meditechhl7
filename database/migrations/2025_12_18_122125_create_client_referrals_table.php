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
        Schema::create('client_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('referred_client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('referral_code_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->string('referrer_reward_type')->nullable();
            $table->decimal('referrer_reward_value', 10, 2)->nullable();
            $table->boolean('referrer_reward_applied')->default(false);
            $table->foreignId('referrer_reward_invoice_id')->nullable()->constrained('client_invoices')->onDelete('set null');
            $table->boolean('referred_reward_applied')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index('referrer_client_id');
            $table->index('referred_client_id');
            $table->index('referral_code_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_referrals');
    }
};
