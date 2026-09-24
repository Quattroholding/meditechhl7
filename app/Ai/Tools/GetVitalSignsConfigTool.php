<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetVitalSignsConfigTool implements Tool
{
    public function name(): string
    {
        return 'get_vital_signs_config';
    }

    public function description(): Stringable|string
    {
        return 'Get list of valid vital signs with their LOINC codes and normal ranges.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Stringable|string
    {
        try {
            $vitalSigns = DB::table('clinical_observation_types')
                ->where('category', '=', 'vital_sign')
                ->where('is_active', '=', true)
                ->select('id', 'name', 'loinc_code', 'unit', 'normal_min', 'normal_max', 'description')
                ->orderBy('name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'loinc_code' => $item->loinc_code,
                        'unit' => $item->unit,
                        'normal_range' => $item->normal_min && $item->normal_max
                            ? "{$item->normal_min} - {$item->normal_max} {$item->unit}"
                            : 'Variable',
                        'description' => $item->description,
                    ];
                })
                ->all();

            return json_encode([
                'success' => true,
                'total_count' => count($vitalSigns),
                'vital_signs' => $vitalSigns,
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Failed to retrieve vital signs configuration: '.$e->getMessage(),
                'vital_signs' => [],
            ]);
        }
    }
}
