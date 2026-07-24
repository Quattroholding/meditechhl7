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
        Schema::create('appointment_waitlist_entries', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('appointment_id')->unique()->constrained('appointments')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('practitioner_id')->constrained('practitioners')->onDelete('cascade');
            $table->foreignId('medical_speciality_id')->nullable()->references('id')->on('medical_specialties')->onDelete('set null');
            $table->foreignId('consulting_room_id')->nullable()->constrained('consulting_rooms')->onDelete('set null');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // Preferencias del paciente
            $table->date('preferred_date')->nullable();
            $table->time('preferred_time')->nullable();
            $table->time('preferred_time_range_start')->nullable();
            $table->time('preferred_time_range_end')->nullable();
            $table->boolean('is_flexible_date')->default(false);
            $table->boolean('is_flexible_time')->default(false);
            $table->integer('max_wait_days')->default(30);

            // Priorización
            $table->decimal('priority_score', 5, 2)->default(0.00)->index();
            $table->enum('urgency_level', ['routine', 'urgent', 'very_urgent', 'emergency'])->default('routine');
            $table->text('reason')->nullable();

            // Estado
            $table->enum('status', ['active', 'assigned', 'expired', 'cancelled'])->default('active')->index();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('expires_at');

            // Notificaciones
            $table->timestamp('notification_sent_at')->nullable();
            $table->integer('notification_count')->default(0);
            $table->timestamp('last_notification_at')->nullable();

            // Auditoría
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['status', 'client_id'], 'idx_waitlist_status_client');
            $table->index(['priority_score', 'created_at'], 'idx_waitlist_priority');
            $table->index(['expires_at'], 'idx_waitlist_expires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_waitlist_entries');
    }
};
