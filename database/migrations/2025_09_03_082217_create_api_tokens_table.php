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
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nombre descriptivo del token');
            $table->string('token', 80)->unique()->comment('Token de acceso');
            $table->json('allowed_ips')->comment('Lista de IPs permitidas');
            $table->json('scopes')->comment('Permisos/alcances del token');
            $table->timestamp('last_used_at')->nullable()->comment('Última vez usado');
            $table->string('last_used_ip')->nullable()->comment('Última IP que usó el token');
            $table->timestamp('expires_at')->nullable()->comment('Fecha de expiración');
            $table->boolean('active')->default(true)->comment('Token activo/inactivo');
            $table->string('created_by')->nullable()->comment('Usuario que creó el token');
            $table->text('description')->nullable()->comment('Descripción del propósito');
            $table->timestamps();

            $table->index('token');
            $table->index('active');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
