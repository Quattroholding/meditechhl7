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
        Schema::table('user_widget_preferences', function (Blueprint $table) {
            // Agregar columnas para compatibilidad con Spatie Dashboard
            $table->string('position', 20)->nullable()->after('width')->comment('Posición del tile en formato Spatie (ej: a1:c2)');
            $table->integer('height')->nullable()->after('position')->comment('Alto del widget (número de filas)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_widget_preferences', function (Blueprint $table) {
            $table->dropColumn(['position', 'height']);
        });
    }
};
