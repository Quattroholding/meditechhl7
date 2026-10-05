<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cost centers table may have financial fields already, check and add if needed
        if (! Schema::hasColumn('cost_centers', 'uuid')) {
            Schema::table('cost_centers', function (Blueprint $table) {
                $table->uuid('uuid')->unique()->after('id');
            });
        }

        if (! Schema::hasColumn('cost_centers', 'medical_speciality_id')) {
            Schema::table('cost_centers', function (Blueprint $table) {
                $table->foreignId('medical_speciality_id')
                    ->nullable()
                    ->constrained('medical_specialties')
                    ->onDelete('set null')
                    ->after('branch_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('cost_centers', function (Blueprint $table) {
            if (Schema::hasColumn('cost_centers', 'medical_speciality_id')) {
                $table->dropForeignKeyIfExists(['medical_speciality_id']);
                $table->dropColumn('medical_speciality_id');
            }

            if (Schema::hasColumn('cost_centers', 'uuid')) {
                $table->dropColumn('uuid');
            }
        });
    }
};
