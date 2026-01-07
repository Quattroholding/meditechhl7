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
        Schema::create('setup_reminder_configs', function (Blueprint $table) {
            $table->id();
            $table->string('reminder_key')->unique(); // subscription, branch, consulting_room, etc.
            $table->string('title'); // Título del recordatorio
            $table->boolean('is_active')->default(true); // Si está activo o no
            $table->boolean('is_required')->default(true); // Si es requerimiento o sugerencia
            $table->integer('order')->default(0); // Orden de visualización
            $table->text('description')->nullable(); // Descripción del recordatorio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setup_reminder_configs');
    }
};
