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
        Schema::create('whatsapp_agents', function (Blueprint $table) {

            $table->id();

            // ID que manda Meta en el webhook
            $table->string('phone_number_id')->unique();

            // tipo de manejo
            $table->enum('type', [
                'n8n',
                'laravel',
            ]);

            // webhook de n8n (si aplica)
            $table->string('webhook_url')->nullable();

            // descripción
            $table->string('description')->nullable();

            // activo / inactivo
            $table->boolean('active')->default(true);
            $table->integer('client_id')->nullable();
            $table->string('api_base_url', 150)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_agents');
    }
};
