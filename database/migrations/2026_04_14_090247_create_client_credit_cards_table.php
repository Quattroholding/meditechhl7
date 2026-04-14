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
        Schema::create('client_credit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('neopayments_customer_id')->nullable();
            $table->string('neopayments_card_id')->nullable();
            $table->string('card_token')->unique();
            $table->string('card_holder');
            $table->string('card_last_four', 4);
            $table->string('card_brand')->nullable();
            $table->string('exp_month', 2);
            $table->string('exp_year', 2);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('alias')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('client_id');
            $table->index(['is_default', 'client_id']);
            $table->index('neopayments_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_credit_cards');
    }
};
