<?php

namespace App\Enums;

enum LoincCode: string
{
    // Complete Blood Count (CBC) - Hemograma Completo
    case WBC = '6690-2';
    case RBC = '789-8';
    case HEMOGLOBIN = '718-7';
    case HEMATOCRIT = '4544-3';
    case MCV = '787-2';
    case MCH = '785-6';
    case MCHC = '786-4';
    case RDW = '788-0';
    case PLATELETS = '777-3';
    case MPV = '32623-1';

    // Differential - Absolute counts (Recuento absoluto)
    case NEUTROPHILS_ABS = '751-8';
    case LYMPHOCYTES_ABS = '731-0';
    case MONOCYTES_ABS = '742-7';
    case EOSINOPHILS_ABS = '711-2';
    case BASOPHILS_ABS = '704-7';

    // Differential - Percentages (Porcentajes)
    case NEUTROPHILS_PCT = '770-8';
    case LYMPHOCYTES_PCT = '736-9';
    case MONOCYTES_PCT = '5905-5';
    case EOSINOPHILS_PCT = '713-8';
    case BASOPHILS_PCT = '706-2';

    public function label(): string
    {
        return match ($this) {
            self::WBC => 'Glóbulos Blancos (WBC)',
            self::RBC => 'Glóbulos Rojos (RBC)',
            self::HEMOGLOBIN => 'Hemoglobina (Hb)',
            self::HEMATOCRIT => 'Hematocrito (Hct)',
            self::MCV => 'Vol. Corp. Medio (MCV)',
            self::MCH => 'Hb Corp. Media (MCH)',
            self::MCHC => 'Conc. Hb Corp. Media (MCHC)',
            self::RDW => 'Amplitud Dist. Eritrocitaria (RDW)',
            self::PLATELETS => 'Plaquetas (PLT)',
            self::MPV => 'Vol. Plaquetario Medio (MPV)',
            self::NEUTROPHILS_ABS => 'Neutrófilos',
            self::LYMPHOCYTES_ABS => 'Linfocitos',
            self::MONOCYTES_ABS => 'Monocitos',
            self::EOSINOPHILS_ABS => 'Eosinófilos',
            self::BASOPHILS_ABS => 'Basófilos',
            self::NEUTROPHILS_PCT => 'Neutrófilos %',
            self::LYMPHOCYTES_PCT => 'Linfocitos %',
            self::MONOCYTES_PCT => 'Monocitos %',
            self::EOSINOPHILS_PCT => 'Eosinófilos %',
            self::BASOPHILS_PCT => 'Basófilos %',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::WBC => 'WBC',
            self::RBC => 'RBC',
            self::HEMOGLOBIN => 'Hemoglobina',
            self::HEMATOCRIT => 'Hematocrito',
            self::MCV => 'MCV',
            self::MCH => 'MCH',
            self::MCHC => 'MCHC',
            self::RDW => 'RDW',
            self::PLATELETS => 'Plaquetas',
            self::MPV => 'MPV',
            self::NEUTROPHILS_ABS => 'Neutrófilos',
            self::LYMPHOCYTES_ABS => 'Linfocitos',
            self::MONOCYTES_ABS => 'Monocitos',
            self::EOSINOPHILS_ABS => 'Eosinófilos',
            self::BASOPHILS_ABS => 'Basófilos',
            self::NEUTROPHILS_PCT => 'Neutrófilos %',
            self::LYMPHOCYTES_PCT => 'Linfocitos %',
            self::MONOCYTES_PCT => 'Monocitos %',
            self::EOSINOPHILS_PCT => 'Eosinófilos %',
            self::BASOPHILS_PCT => 'Basófilos %',
        };
    }

    public function category(): string
    {
        return match ($this) {
            self::WBC, self::RBC, self::HEMOGLOBIN, self::HEMATOCRIT => 'Conteo Principal',
            self::MCV, self::MCH, self::MCHC, self::RDW => 'Índices Eritrocitarios',
            self::PLATELETS, self::MPV => 'Plaquetas',
            self::NEUTROPHILS_ABS, self::LYMPHOCYTES_ABS, self::MONOCYTES_ABS,
            self::EOSINOPHILS_ABS, self::BASOPHILS_ABS => 'Diferencial Absoluto',
            self::NEUTROPHILS_PCT, self::LYMPHOCYTES_PCT, self::MONOCYTES_PCT,
            self::EOSINOPHILS_PCT, self::BASOPHILS_PCT => 'Diferencial Porcentual',
        };
    }

    public static function fromCode(string $code): ?self
    {
        return self::tryFrom($code);
    }

    public static function getLabel(string $code): string
    {
        $loincCode = self::fromCode($code);

        return $loincCode?->label() ?? $code;
    }

    public static function getShortLabel(string $code): string
    {
        $loincCode = self::fromCode($code);

        return $loincCode?->shortLabel() ?? $code;
    }
}
