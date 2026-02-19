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
        Schema::create('whatsapp_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('event_type')->nullable()->index(); // message, status, error, etc.
            $table->string('message_id')->nullable()->index(); // wamid from Meta
            $table->string('phone_number_id')->nullable()->index(); // Business phone number ID
            $table->string('from')->nullable(); // Sender phone number
            $table->string('to')->nullable(); // Recipient phone number
            $table->string('status')->nullable(); // delivered, read, failed, sent
            $table->text('error_message')->nullable(); // Error details if failed
            $table->json('raw_payload')->nullable(); // Full webhook payload
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamp('webhook_received_at')->useCurrent();
            $table->timestamps();

            // Indexes for common queries
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_webhooks');
    }
};
