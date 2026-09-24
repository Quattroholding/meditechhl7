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
        Schema::table('cpt_codes', function (Blueprint $table) {
            $table->string('alias')->nullable()->after('code')->comment('Common name or alias for the CPT code (e.g., hemograma completo, glucosa)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cpt_codes', function (Blueprint $table) {
            $table->dropColumn('alias');
        });
    }
};
