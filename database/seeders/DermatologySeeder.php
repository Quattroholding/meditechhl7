<?php

namespace Database\Seeders;

use App\Models\BodySiteMarking;
use App\Models\BodyStructure;
use App\Models\Encounter;
use App\Models\Media;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\SkinLesion;
use Illuminate\Database\Seeder;

class DermatologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏥 Seeding dermatology data...');

        // Get existing patients and practitioners
        $patients = Patient::limit(10)->get();
        $practitioners = Practitioner::limit(5)->get();

        if ($patients->isEmpty() || $practitioners->isEmpty()) {
            $this->command->warn('⚠️  No patients or practitioners found. Please seed those first.');

            return;
        }

        $this->command->info("📊 Found {$patients->count()} patients and {$practitioners->count()} practitioners");

        $totalStructures = 0;
        $totalLesions = 0;
        $totalMarkings = 0;
        $totalMedia = 0;

        // For each patient, create dermatology records
        foreach ($patients as $patient) {
            $practitioner = $practitioners->random();

            // Create 1-3 encounters for this patient
            $encountersCount = rand(1, 3);

            for ($i = 0; $i < $encountersCount; $i++) {
                $encounter = Encounter::where('patient_id', $patient->id)
                    ->inRandomOrder()
                    ->first();

                if (! $encounter) {
                    continue;
                }

                // Create 2-5 body structures (lesions) per encounter
                $structuresCount = rand(2, 5);

                for ($j = 0; $j < $structuresCount; $j++) {
                    // Create body structure
                    $bodyStructure = BodyStructure::factory()->create([
                        'patient_id' => $patient->id,
                        'encounter_id' => $encounter->id,
                    ]);
                    $totalStructures++;

                    // Create skin lesion details
                    $skinLesion = SkinLesion::factory()
                        ->for($bodyStructure)
                        ->create([
                            'patient_id' => $patient->id,
                            'encounter_id' => $encounter->id,
                            'practitioner_id' => $practitioner->id,
                        ]);
                    $totalLesions++;

                    // Create body site marking (visual position)
                    $marking = BodySiteMarking::factory()
                        ->for($bodyStructure)
                        ->for($skinLesion)
                        ->create();
                    $totalMarkings++;

                    // 70% chance to create media (photo/dermoscopy)
                    if (rand(1, 100) <= 70) {
                        Media::factory()
                            ->for($patient)
                            ->for($encounter)
                            ->for($practitioner)
                            ->for($skinLesion, 'subject')
                            ->dermoscopy()
                            ->create();
                        $totalMedia++;
                    }

                    // 40% chance for additional clinical photo
                    if (rand(1, 100) <= 40) {
                        Media::factory()
                            ->for($patient)
                            ->for($encounter)
                            ->for($practitioner)
                            ->for($skinLesion, 'subject')
                            ->photograph()
                            ->create();
                        $totalMedia++;
                    }
                }
            }
        }

        $this->command->newLine();
        $this->command->info('✅ Dermatology data seeded successfully!');
        $this->command->table(
            ['Resource', 'Count'],
            [
                ['Body Structures', $totalStructures],
                ['Skin Lesions', $totalLesions],
                ['Body Site Markings', $totalMarkings],
                ['Media Files', $totalMedia],
            ]
        );

        // Create some specific test cases
        $this->createTestCases($patients->first(), $practitioners->first());
    }

    /**
     * Create specific test cases for demonstration.
     */
    private function createTestCases(Patient $patient, Practitioner $practitioner): void
    {
        $this->command->newLine();
        $this->command->info('📝 Creating test cases...');

        $encounter = Encounter::where('patient_id', $patient->id)->first();

        if (! $encounter) {
            return;
        }

        // Test Case 1: Low risk nevus on face
        $structure1 = BodyStructure::factory()
            ->nevus()
            ->onFace()
            ->create([
                'patient_id' => $patient->id,
                'encounter_id' => $encounter->id,
                'location_qualifier_code' => '7771000',
                'location_qualifier_display' => 'Izquierdo',
            ]);

        $lesion1 = SkinLesion::factory()
            ->nevus()
            ->lowRisk()
            ->active()
            ->create([
                'body_structure_id' => $structure1->id,
                'patient_id' => $patient->id,
                'encounter_id' => $encounter->id,
                'practitioner_id' => $practitioner->id,
            ]);

        BodySiteMarking::factory()
            ->anterior()
            ->create([
                'body_structure_id' => $structure1->id,
                'skin_lesion_id' => $lesion1->id,
                'position_x' => 35.5,
                'position_y' => 25.0,
                'marker_color' => '#22C55E',
                'label' => 'Test-Low',
            ]);

        // Test Case 2: High risk suspicious lesion on back
        $structure2 = BodyStructure::factory()->create([
            'patient_id' => $patient->id,
            'encounter_id' => $encounter->id,
            'morphology_code' => '372244006',
            'morphology_display' => 'Melanoma maligno (sospecha)',
            'location_code' => '53120007',
            'location_display' => 'Piel del tronco',
        ]);

        $lesion2 = SkinLesion::factory()
            ->suspicious()
            ->active()
            ->create([
                'body_structure_id' => $structure2->id,
                'patient_id' => $patient->id,
                'encounter_id' => $encounter->id,
                'practitioner_id' => $practitioner->id,
            ]);

        BodySiteMarking::factory()
            ->posterior()
            ->create([
                'body_structure_id' => $structure2->id,
                'skin_lesion_id' => $lesion2->id,
                'position_x' => 50.0,
                'position_y' => 40.0,
                'marker_color' => '#EF4444',
                'marker_shape' => 'star',
                'marker_size' => 14,
                'label' => 'Test-High',
            ]);

        // Add dermoscopy image for suspicious lesion
        Media::factory()
            ->dermoscopy()
            ->create([
                'patient_id' => $patient->id,
                'encounter_id' => $encounter->id,
                'practitioner_id' => $practitioner->id,
                'subject_type' => SkinLesion::class,
                'subject_id' => $lesion2->id,
            ]);

        $this->command->info('  ✓ Created low-risk nevus on face');
        $this->command->info('  ✓ Created high-risk suspicious lesion on back');
    }
}
