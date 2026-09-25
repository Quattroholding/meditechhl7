<?php

namespace Database\Seeders;

use App\Models\CptCode;
use Illuminate\Database\Seeder;

class CptCodeAliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map of CPT codes to aliases (Spanish medical terminology)
        $commonAliases = [
            // Laboratory - Common Blood Tests
            '85027' => 'hemograma',
            '80076' => 'panel básico',
            '82947' => 'glucosa',
            '83036' => 'HbA1c',
            '84403' => 'PT',
            '85610' => 'INR',
            '83528' => 'BNP',
            '84450' => 'troponina',
            '82565' => 'creatinina',
            '84520' => 'urea',
            '82310' => 'calcio',
            '84100' => 'fosfato',
            '84132' => 'potasio',
            '84235' => 'magnesio',
            '81002' => 'uroanálisis',
            '81000' => 'análisis orina',
            '87040' => 'cultivo sangre',
            '80500' => 'hemocultivo',
            '82951' => 'prueba tolerancia glucosa',

            // Thyroid Tests
            '84436' => 'T4 total',
            '84439' => 'T4 libre',
            '84443' => 'TSH',
            '84480' => 'T3 total',
            '84481' => 'T3 libre',

            // Urine Tests
            '84156' => 'proteína orina',
            '82044' => 'microalbúmina orina',

            // Imaging
            '71020' => 'radiografía tórax',
            '71021' => 'radiografía pecho',
            '93307' => 'ecocardiograma',
            '76700' => 'ecografía abdominal',
            '76705' => 'ecografía abdominal limitada',
            '76775' => 'ecografía renal',
            '76778' => 'ecografía trasplante renal',
            '73610' => 'radiografía cadera',
            '93000' => 'EKG',
            '93005' => 'interpretación EKG',

            // Obstetric Ultrasound
            '76805' => 'ecografía obstétrica',
            '76811' => 'ecografía obstétrica detallada',
            '76816' => 'ecografía seguimiento embarazo',
        ];

        $updated = 0;
        $notFound = 0;

        // Update CPT codes with aliases
        foreach ($commonAliases as $code => $alias) {
            $cpt = CptCode::where('code', $code)->first();

            if ($cpt) {
                $cpt->update(['alias' => $alias]);
                $updated++;
                echo "✓ CPT $code: alias = '$alias'\n";
            } else {
                $notFound++;
            }
        }

        echo "\n✓ Seeder completado\n";
        echo "  - Actualizados: $updated\n";
        echo "  - No encontrados: $notFound\n";
    }
}
