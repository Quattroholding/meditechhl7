<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update categories following SOAP model
        // S - Subjective: Chief Complaint, Present Illness, General Note
        DB::table('encounter_sections')
            ->where('name', 'Chief Complaint')
            ->update(['category' => 'subjective']);

        DB::table('encounter_sections')
            ->where('name', 'Present Illness')
            ->update(['category' => 'subjective']);

        DB::table('encounter_sections')
            ->where('name', 'General Note')
            ->update(['category' => 'subjective']);

        // O - Objective: Vital Signs, Physical Exams
        DB::table('encounter_sections')
            ->where('name', 'Vital Signs')
            ->update(['category' => 'objective']);

        DB::table('encounter_sections')
            ->where('name', 'Physical Exams')
            ->update(['category' => 'objective']);

        // A - Assessment: Conditions (Diagnoses)
        DB::table('encounter_sections')
            ->where('name', 'Conditions')
            ->update(['category' => 'assessment']);

        // P - Plan: Laboratories, Images, Procedures, Referral, Medicines
        DB::table('encounter_sections')
            ->where('name', 'Laboratories')
            ->update(['category' => 'plan']);

        DB::table('encounter_sections')
            ->where('name', 'Images')
            ->update(['category' => 'plan']);

        DB::table('encounter_sections')
            ->where('name', 'Procedures')
            ->update(['category' => 'plan']);

        DB::table('encounter_sections')
            ->where('name', 'Referral Specialist')
            ->update(['category' => 'plan']);

        DB::table('encounter_sections')
            ->where('name', 'Medicines')
            ->update(['category' => 'plan']);

        // Specialty sections: Already have their category (Urologia, Dermatologia, Pediatría)
        // Update to standardized "specialty" category
        DB::table('encounter_sections')
            ->whereNotNull('medical_speciality_id')
            ->update(['category' => 'specialty']);

        // System sections: Services, Supplies
        DB::table('encounter_sections')
            ->where('name', 'Services')
            ->update(['category' => 'system']);

        DB::table('encounter_sections')
            ->where('name', 'Supplies')
            ->update(['category' => 'system']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert specialty sections to their original category names
        DB::table('encounter_sections')
            ->where('id', 12)
            ->update(['category' => 'Urologia']);

        DB::table('encounter_sections')
            ->where('id', 14)
            ->update(['category' => 'Dermatologia']);

        DB::table('encounter_sections')
            ->whereIn('id', [15, 16])
            ->update(['category' => 'Pediatría']);

        // Set all other categories to null
        DB::table('encounter_sections')
            ->whereIn('category', ['subjective', 'objective', 'assessment', 'plan', 'system'])
            ->update(['category' => null]);
    }
};
