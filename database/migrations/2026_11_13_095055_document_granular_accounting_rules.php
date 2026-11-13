<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * This is a documentation-only migration to record that the client_accounting_rule_mappings
     * table has been extended with granular dimensions support.
     *
     * The actual database changes were applied via a previous migration:
     * - Added: medical_speciality_id (FK to medical_specialties)
     * - Added: payment_source (enum: 'patient', 'insurance')
     * - Added: priority (int, default 0)
     * - Added: is_default (boolean, default false)
     * - Removed: unique constraint on (client_id, rule_type)
     * - Added: unique constraint on (client_id, rule_type, medical_speciality_id, payment_source)
     * - Added: indexes for performance
     *
     * This allows the system to support:
     * 1. Income rules by medical specialty (e.g., General Medicine → 4101, Other Specialties → 4102)
     * 2. Receivable rules by payment source (e.g., Patient → 110201, Insurance → 110202)
     * 3. Default rules for each type
     */
    public function up(): void
    {
        // No-op migration - database structure is already in place
    }

    public function down(): void
    {
        // No-op migration - database structure should not be rolled back
    }
};
