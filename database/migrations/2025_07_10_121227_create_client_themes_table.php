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
        Schema::create('client_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');

            // Colores principales
            $table->string('primary_color')->default('#3498db');
            $table->string('secondary_color')->default('#2ecc71');
            $table->string('accent_color')->default('#e74c3c');
            $table->string('success_color')->default('#27ae60');
            $table->string('warning_color')->default('#f39c12');
            $table->string('danger_color')->default('#e74c3c');
            $table->string('info_color')->default('#3498db');

            // Colores de interfaz
            $table->string('text_color')->default('#2c3e50');
            $table->string('text_muted_color')->default('#7f8c8d');
            $table->string('background_color')->default('#ffffff');
            $table->string('sidebar_color')->default('#34495e');
            $table->string('header_color')->default('#2c3e50');
            $table->string('border_color')->default('#dee2e6');

            // Colores para documentos médicos
            $table->string('document_header_color')->default('#2c3e50');
            $table->string('document_title_color')->default('#e74c3c');
            $table->string('table_header_color')->default('#34495e');
            $table->string('diagnosis_accent_color')->default('#3498db');

            // Archivos de branding
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();
            $table->string('logo_sidebar_url')->nullable();

            // CSS personalizado
            $table->text('custom_css')->nullable();

            // Configuraciones adicionales
            $table->string('font_family')->default('Arial, sans-serif');
            $table->boolean('dark_mode')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Índices
            $table->unique('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_themes');
    }
};
