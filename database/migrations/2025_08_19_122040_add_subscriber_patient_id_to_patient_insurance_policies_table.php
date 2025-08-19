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
        Schema::table('patient_insurance_policies', function (Blueprint $table) {
            if (! Schema::hasColumn('patient_insurance_policies', 'subscriber_patient_id')) {
                $table->foreignId('subscriber_patient_id')->nullable()->after('subscriber_name')
                    ->constrained('patients')->onDelete('set null')
                    ->comment('Reference to Patient who is the actual insurance subscriber');

                $table->index(['subscriber_patient_id'], 'pip_subscriber_patient_id_idx');
                $table->index(['patient_id', 'subscriber_patient_id'], 'pip_patient_subscriber_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_insurance_policies', function (Blueprint $table) {
            $table->dropForeign(['subscriber_patient_id']);
            $table->dropIndex('pip_subscriber_patient_id_idx');
            $table->dropIndex('pip_patient_subscriber_idx');
            $table->dropColumn('subscriber_patient_id');
        });
    }
};
