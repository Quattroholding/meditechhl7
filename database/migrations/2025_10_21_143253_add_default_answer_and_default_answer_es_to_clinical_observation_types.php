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
        Schema::table('clinical_observation_types', function (Blueprint $table) {
            $table->text('default_answer')->nullable()->after('description');
            $table->text('default_answer_es')->nullable()->after('default_answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinical_observation_types', function (Blueprint $table) {
            $table->dropColumn(['default_answer', 'default_answer_es']);
        });
    }
};
