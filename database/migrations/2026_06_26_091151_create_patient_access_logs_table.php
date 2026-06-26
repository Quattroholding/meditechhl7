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
        Schema::create('patient_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('action_type'); // view, download, export, print
            $table->string('resource_type')->default('medical_history'); // medical_history, complete_history, encounter, etc.
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->json('metadata')->nullable(); // Additional context (filename for downloads, etc.)
            $table->timestamp('access_timestamp')->useCurrent();
            $table->timestamps();

            // Indexes for compliance queries
            $table->index(['patient_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action_type', 'created_at']);
            $table->index(['client_id', 'created_at']);
            $table->index('access_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_access_logs');
    }
};
