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
        Schema::create('appointment_freed_slots', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('practitioner_id')->constrained('practitioners')->onDelete('cascade');
            $table->foreignId('consulting_room_id')->nullable()->constrained('consulting_rooms')->onDelete('set null');
            $table->foreignId('medical_speciality_id')->nullable()->references('id')->on('medical_specialties')->onDelete('set null');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // Detalles del slot
            $table->date('slot_date');
            $table->time('slot_start_time');
            $table->time('slot_end_time');
            $table->integer('duration_minutes');

            // Causa
            $table->enum('freed_by', ['cancellation', 'noshow', 'manual'])->default('cancellation');
            $table->foreignId('cancelled_appointment_id')->nullable()->constrained('appointments')->onDelete('set null');

            // Estado
            $table->enum('status', ['available', 'matched', 'expired', 'manually_filled'])->default('available')->index();
            $table->foreignId('matched_waitlist_entry_id')->nullable()->constrained('appointment_waitlist_entries')->onDelete('set null');
            $table->timestamp('matched_at')->nullable();
            $table->timestamp('expires_at');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['status', 'client_id'], 'idx_freed_status_client');
            $table->index(['slot_date', 'slot_start_time'], 'idx_freed_slot_datetime');
            $table->index(['expires_at'], 'idx_freed_expires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_freed_slots');
    }
};
