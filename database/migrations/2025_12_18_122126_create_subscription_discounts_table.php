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
        Schema::create('subscription_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('client_subscriptions')->onDelete('cascade');
            $table->string('discount_type');
            $table->decimal('discount_value', 10, 2);
            $table->string('reason');
            $table->string('source');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->integer('applies_to_invoices')->default(1);
            $table->integer('invoices_applied')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('subscription_id');
            $table->index('is_active');
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_discounts');
    }
};
