<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchMedicationTool implements Tool
{
    public function name(): string
    {
        return 'search_medications';
    }

    public function description(): Stringable|string
    {
        return 'Search for medications by generic or commercial name. Returns available dosages.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->required()->description('Medication name (generic or commercial)'),
            'limit' => $schema->integer()->default(5)->description('Maximum number of results to return'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $query = $request['query'] ?? '';
        $limit = $request['limit'] ?? 5;

        if (empty($query) || strlen($query) < 2) {
            return json_encode([
                'success' => false,
                'error' => 'Query must be at least 2 characters long',
                'results' => [],
            ]);
        }

        try {
            $results = DB::table('medications')
                ->where('generic_name', 'like', "%{$query}%")
                ->orWhere('display', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%")
                ->where('status', '=', 'active')
                ->select('id', 'generic_name', 'display', 'code', 'form', 'manufacturer')
                ->limit($limit)
                ->get()
                ->map(function ($item) use ($query) {
                    $score = 100;
                    if (stripos($item->generic_name, $query) === 0 || stripos($item->display, $query) === 0) {
                        $score = 100;
                    } elseif (stripos($item->generic_name, $query) !== false || stripos($item->display, $query) !== false) {
                        $score = 85;
                    }

                    return [
                        'id' => $item->id,
                        'generic_name' => $item->generic_name,
                        'display_name' => $item->display,
                        'code' => $item->code,
                        'form' => $item->form,
                        'manufacturer' => $item->manufacturer,
                        'full_description' => "{$item->generic_name} - {$item->display} ({$item->form})",
                        'relevance_score' => $score,
                    ];
                })
                ->sortByDesc('relevance_score')
                ->values()
                ->all();

            return json_encode([
                'success' => true,
                'query' => $query,
                'total_found' => count($results),
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'error' => 'Database query failed: '.$e->getMessage(),
                'results' => [],
            ]);
        }
    }
}
