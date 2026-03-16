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
        Schema::create('hemoscreen_standalone_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('practitioner_id')->constrained()->onDelete('cascade');
            $table->foreignId('scb_id')->constrained('clients')->onDelete('cascade');

            // Device information
            $table->string('device_serial', 100);
            $table->string('message_control_id', 100);
            $table->string('message_type', 50)->default('OBS.R01');

            // Test panel information (CBC panel)
            $table->string('panel_code', 20); // 85025 or 85027
            $table->string('panel_name', 255)->default('Complete Blood Count');

            // Individual observations (JSON array)
            // Structure: [{"code": "718-7", "value": "15.5", "unit": "g/dL", "status": "final", "name": "Hemoglobin"}]
            $table->json('observations');

            // Raw message storage (for debugging/audit)
            $table->json('raw_message')->nullable();

            // Timestamps
            $table->timestamp('test_performed_at');
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'test_performed_at']);
            $table->index(['practitioner_id', 'created_at']);
            $table->index(['scb_id', 'created_at']);
            $table->index('device_serial');
            $table->unique(['scb_id', 'message_control_id']); // Prevent duplicates per client
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hemoscreen_standalone_results');
    }
};
