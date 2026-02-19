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
        Schema::create('whatsapp_conversation_usage', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->index();
            $table->string('conversation_id')->nullable();
            $table->string('message_id')->nullable();
            $table->year('year');
            $table->tinyInteger('month');
            $table->enum('type', ['user_initiated', 'business_initiated'])->default('business_initiated');
            $table->timestamp('conversation_started_at');
            $table->timestamps();

            // Índice compuesto para búsquedas mensuales
            $table->index(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversation_usage');
    }
};
