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
        Schema::table('service_requests', function (Blueprint $table) {
            $table->boolean('performed_in_consultation')->default(false)->after('note')->comment('¿Se realizó en la consulta?');
            $table->text('procedure_notes')->nullable()->after('performed_in_consultation')->comment('Notas del procedimiento realizado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['performed_in_consultation', 'procedure_notes']);
        });
    }
};
