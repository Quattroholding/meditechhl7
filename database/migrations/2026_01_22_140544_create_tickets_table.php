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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('identifier', 50)->unique()->comment('TKT-XXXXXXX');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Creador del ticket');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null')->comment('Admin asignado');
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'on_hold', 'resolved', 'closed'])->default('open');
            $table->enum('category', ['bug', 'question', 'feature_request', 'improvement', 'technical_support', 'other'])->default('other');
            $table->timestamps();
            $table->softDeletes();

            // Índices para optimización de consultas
            $table->index(['client_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('assigned_to');
            $table->index('priority');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
