<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchIcd10Tool implements Tool
{
    public function name(): string
    {
        return 'search_icd10_codes';
    }

    public function description(): Stringable|string
    {
        return 'Search for ICD-10 medical codes by description. Returns matching codes with relevance scores.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->required()->description('Medical condition description (e.g., "arterial hypertension", "diabetes")'),
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
            $results = DB::table('icd10_codes')
                ->where('description', 'like', "%{$query}%")
                ->orWhere('description_es', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%")
                ->where('active', '=', true)
                ->select('code', 'description', 'description_es')
                ->limit($limit)
                ->get()
                ->map(function ($item) use ($query) {
                    // Calculate relevance score
                    $score = 50;
                    if (stripos($item->description, $query) === 0 || stripos($item->description_es, $query) === 0) {
                        $score = 100;
                    } elseif (stripos($item->description, $query) !== false || stripos($item->description_es, $query) !== false) {
                        $score = 75;
                    }

                    return [
                        'code' => $item->code,
                        'description' => $item->description,
                        'description_es' => $item->description_es,
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
