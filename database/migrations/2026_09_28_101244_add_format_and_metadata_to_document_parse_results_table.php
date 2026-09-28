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
        Schema::table('document_parse_results', function (Blueprint $table) {
            $table->string('detected_format')->default('standard')->after('confidence_score');
            $table->longText('batch_info')->nullable()->after('validation_messages'); // JSON - info de lotes/vencimientos
            $table->longText('additional_fields')->nullable()->after('batch_info'); // JSON - descuentos, impuestos, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_parse_results', function (Blueprint $table) {
            $table->dropColumn('detected_format');
            $table->dropColumn('batch_info');
            $table->dropColumn('additional_fields');
        });
    }
};
