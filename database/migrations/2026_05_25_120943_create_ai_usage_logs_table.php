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
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();

            // User and tenant information
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();

            // AI Service information
            $table->string('service'); // claude, openai-whisper, etc.
            $table->string('operation'); // transcription, medical-dictation, diagnostic-suggestions, etc.
            $table->string('model'); // claude-sonnet-4-6, whisper-1, etc.

            // Token usage
            $table->integer('input_tokens')->default(0); // Prompt/input tokens
            $table->integer('output_tokens')->default(0); // Completion/output tokens
            $table->integer('total_tokens')->default(0); // Total tokens used

            // Cost tracking (in USD cents to avoid floating point issues)
            $table->integer('estimated_cost_cents')->default(0); // Cost in cents (e.g., 150 = $1.50)

            // Request metadata
            $table->text('request_summary')->nullable(); // Brief summary of what was requested
            $table->integer('audio_duration_seconds')->nullable(); // For voice dictation
            $table->integer('audio_size_bytes')->nullable(); // For voice dictation

            // Optional associations
            $table->foreignId('encounter_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();

            // Additional metadata (JSON for flexibility)
            $table->json('metadata')->nullable();

            // Status and error tracking
            $table->enum('status', ['success', 'failed', 'partial'])->default('success');
            $table->text('error_message')->nullable();

            // Response metadata
            $table->integer('response_time_ms')->nullable(); // Response time in milliseconds
            $table->string('api_request_id')->nullable(); // External API request ID for debugging

            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['user_id', 'created_at']);
            $table->index(['client_id', 'created_at']);
            $table->index(['service', 'created_at']);
            $table->index(['encounter_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
