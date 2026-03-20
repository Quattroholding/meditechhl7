<?php

namespace Database\Seeders;

use App\Models\VaccineType;
use Illuminate\Database\Seeder;

class VaccineTypesSeeder extends Seeder
{
    /**
     * Seed Panama vaccination schedule based on tabla_vacunacion.png.
     */
    public function run(): void
    {
        $vaccines = [
            [
                'cvx_code' => '08',
                'vaccine_name_en' => 'Hepatitis B',
                'vaccine_name_es' => 'Hepatitis B',
                'target_disease' => 'hepatitis_b',
                'min_age_months' => 0,
                'max_age_months' => 1,
                'doses_required' => 1,
                'interval_days' => null,
                'display_order' => 1,
            ],
            [
                'cvx_code' => '19',
                'vaccine_name_en' => 'BCG (Tuberculosis)',
                'vaccine_name_es' => 'BCG (Tuberculosis)',
                'target_disease' => 'tuberculosis',
                'min_age_months' => 0,
                'max_age_months' => 1,
                'doses_required' => 1,
                'interval_days' => null,
                'display_order' => 2,
            ],
            [
                'cvx_code' => '133',
                'vaccine_name_en' => 'Pneumococcal Conjugate',
                'vaccine_name_es' => 'Neumococo Conjugada',
                'target_disease' => 'pneumococcal',
                'min_age_months' => 2,
                'max_age_months' => 12,
                'doses_required' => 3,
                'interval_days' => 60,
                'display_order' => 3,
            ],
            [
                'cvx_code' => '146',
                'vaccine_name_en' => 'Hexavalent (DTaP-IPV-Hib-HepB)',
                'vaccine_name_es' => 'Hexavalente (DTaP-IPV-Hib-HepB)',
                'target_disease' => 'diphtheria,tetanus,pertussis,polio,hib,hepatitis_b',
                'min_age_months' => 2,
                'max_age_months' => 6,
                'doses_required' => 3,
                'interval_days' => 60,
                'display_order' => 4,
            ],
            [
                'cvx_code' => '116',
                'vaccine_name_en' => 'Rotavirus',
                'vaccine_name_es' => 'Rotavirus',
                'target_disease' => 'rotavirus',
                'min_age_months' => 2,
                'max_age_months' => 4,
                'doses_required' => 2,
                'interval_days' => 60,
                'display_order' => 5,
            ],
            [
                'cvx_code' => '141',
                'vaccine_name_en' => 'Influenza',
                'vaccine_name_es' => 'Influenza',
                'target_disease' => 'influenza',
                'min_age_months' => 6,
                'max_age_months' => 59,
                'doses_required' => 2,
                'interval_days' => 30,
                'display_order' => 6,
            ],
            [
                'cvx_code' => '03',
                'vaccine_name_en' => 'MMR (Measles, Mumps, Rubella)',
                'vaccine_name_es' => 'SPR (Sarampión, Paperas, Rubéola)',
                'target_disease' => 'measles,mumps,rubella',
                'min_age_months' => 12,
                'max_age_months' => 18,
                'doses_required' => 1,
                'interval_days' => null,
                'display_order' => 7,
            ],
            [
                'cvx_code' => '83',
                'vaccine_name_en' => 'Hepatitis A',
                'vaccine_name_es' => 'Hepatitis A',
                'target_disease' => 'hepatitis_a',
                'min_age_months' => 12,
                'max_age_months' => 18,
                'doses_required' => 1,
                'interval_days' => null,
                'display_order' => 8,
            ],
            [
                'cvx_code' => '21',
                'vaccine_name_en' => 'Varicella',
                'vaccine_name_es' => 'Varicela',
                'target_disease' => 'varicella',
                'min_age_months' => 15,
                'max_age_months' => 48,
                'doses_required' => 2,
                'interval_days' => 180,
                'display_order' => 9,
            ],
            [
                'cvx_code' => '37',
                'vaccine_name_en' => 'Yellow Fever',
                'vaccine_name_es' => 'Fiebre Amarilla',
                'target_disease' => 'yellow_fever',
                'min_age_months' => 15,
                'max_age_months' => 18,
                'doses_required' => 1,
                'interval_days' => null,
                'display_order' => 10,
            ],
            [
                'cvx_code' => '120',
                'vaccine_name_en' => 'DPT-Hib (4-in-1)',
                'vaccine_name_es' => 'Tetravalente DPT-Hib',
                'target_disease' => 'diphtheria,tetanus,pertussis,hib',
                'min_age_months' => 18,
                'max_age_months' => 18,
                'doses_required' => 1,
                'interval_days' => null,
                'display_order' => 11,
            ],
            [
                'cvx_code' => '10',
                'vaccine_name_en' => 'Polio (IPV)',
                'vaccine_name_es' => 'Polio (IPV)',
                'target_disease' => 'polio',
                'min_age_months' => 18,
                'max_age_months' => 48,
                'doses_required' => 2,
                'interval_days' => 730,
                'display_order' => 12,
            ],
            [
                'cvx_code' => '33',
                'vaccine_name_en' => 'Pneumococcal Polysaccharide (PPSV23)',
                'vaccine_name_es' => 'Neumococo 23',
                'target_disease' => 'pneumococcal',
                'min_age_months' => 24,
                'max_age_months' => 24,
                'doses_required' => 1,
                'interval_days' => null,
                'display_order' => 13,
            ],
            [
                'cvx_code' => '01',
                'vaccine_name_en' => 'DPT',
                'vaccine_name_es' => 'DPT',
                'target_disease' => 'diphtheria,tetanus,pertussis',
                'min_age_months' => 48,
                'max_age_months' => 48,
                'doses_required' => 1,
                'interval_days' => null,
                'display_order' => 14,
            ],
        ];

        foreach ($vaccines as $vaccine) {
            VaccineType::updateOrCreate(
                ['cvx_code' => $vaccine['cvx_code']],
                $vaccine
            );
        }

        $this->command->info('Panama vaccination schedule seeded successfully.');
    }
}
