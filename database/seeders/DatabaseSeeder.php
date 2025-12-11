<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(UserAdminSeeder::class);
        $this->call(PackageSeeder::class);
        $this->call(PanamaSeeder::class);
        $this->call(CptAreaSeeder::class);
        $this->call(CptSeeder::class);
        $this->call(MedicalSpecialitySeeder::class);
        $this->call(PresentIllnesTypesSeeder::class);
        $this->call(ClinicalObservationTypeSeeder::class);
        $this->call(MedicationTypeSeeder::class);
        $this->call(MedicineSeeder::class);
        $this->call(EncounterSecctionSeeder::class);
        $this->call(EncounterQuestionsSeeder::class);
        $this->call(RapidAccessSeeder::class);

        //$this->call(ClientSeeder::class);
        $this->call(InsuranceCompanySeeder::class);
        // $this->call(UserSeeder::class);
        // $this->call(ServiceCatalogSeeder::class);
        //$this->call(UserWidgetPreferenceSeeder::class);
        $this->call(SnomedBodySiteSeeder::class);
        $this->call(PatientSatisfactionSurveySeeder::class);
        $this->call(DiagnosticSeeder::class);
    }
}
