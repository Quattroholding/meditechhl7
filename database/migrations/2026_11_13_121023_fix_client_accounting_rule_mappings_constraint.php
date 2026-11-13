<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old unique constraint using raw SQL
        // The constraint prevents adding multiple rules per (client_id, rule_type)
        try {
            DB::statement('ALTER TABLE `client_accounting_rule_mappings` DROP INDEX `client_accounting_rule_mappings_client_id_rule_type_unique`');
        } catch (\Exception $e) {
            // Constraint might not exist or have a different name
            \Log::warning('Could not drop constraint: '.$e->getMessage());
        }

        // Add new unique constraint that allows multiple rules per type
        // but prevents duplicate configurations for the same speciality/payment_source
        Schema::table('client_accounting_rule_mappings', function (Blueprint $table) {
            $table->unique(
                ['client_id', 'rule_type', 'medical_speciality_id', 'payment_source'],
                'unique_rule_config'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_accounting_rule_mappings', function (Blueprint $table) {
            // Remove the new unique constraint
            DB::statement('ALTER TABLE client_accounting_rule_mappings DROP INDEX IF EXISTS unique_rule_config');

            // Restore the old constraint (if needed)
            $table->unique(['client_id', 'rule_type']);
        });
    }
};
