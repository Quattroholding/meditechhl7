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

            // Imaging
            '71020' => 'radiografía tórax',
            '71021' => 'radiografía pecho',
            '93307' => 'ecocardiograma',
            '76700' => 'ecografía abdominal',
            '73610' => 'radiografía cadera',
            '93000' => 'EKG',
            '93005' => 'interpretación EKG',
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
