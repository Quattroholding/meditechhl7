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
        Schema::create('client_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('preference_type', 50)->index(); // e.g., 'invoice_template', 'email_settings'
            $table->string('preference_key', 100); // e.g., 'template_name', 'default_from'
            $table->json('preference_value'); // Flexible value storage
            $table->text('description')->nullable(); // Human-readable description
            $table->json('metadata')->nullable(); // Additional metadata
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint: one preference per client per type+key
            $table->unique(['client_id', 'preference_type', 'preference_key'], 'client_pref_unique');

            // Index for quick lookups
            $table->index(['client_id', 'preference_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_preferences');
    }
};
