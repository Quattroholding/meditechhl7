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
        Schema::table('referrals', function (Blueprint $table) {
            // Campos para especialista externo (no registrado en el sistema)
            $table->string('external_specialist_name')->nullable()->after('referred_to_id');
            $table->string('external_specialist_specialty')->nullable()->after('external_specialist_name');
            $table->string('external_specialist_license')->nullable()->after('external_specialist_specialty');
            $table->string('external_specialist_phone')->nullable()->after('external_specialist_license');
            $table->string('external_specialist_clinic')->nullable()->after('external_specialist_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn([
                'external_specialist_name',
                'external_specialist_specialty',
                'external_specialist_license',
                'external_specialist_phone',
                'external_specialist_clinic',
            ]);
        });
    }
};
