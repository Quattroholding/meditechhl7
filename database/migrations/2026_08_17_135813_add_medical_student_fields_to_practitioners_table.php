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
        Schema::table('practitioners', function (Blueprint $table) {
            $table->boolean('is_medical_student')->default(false)->after('active')->comment('Indica si es estudiante de medicina');
            $table->date('estimated_graduation_date')->nullable()->after('is_medical_student')->comment('Fecha estimada de graduación');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practitioners', function (Blueprint $table) {
            $table->dropColumn(['is_medical_student', 'estimated_graduation_date']);
        });
    }
};
