<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchCptTool implements Tool
{
    public function name(): string
    {
        return 'search_cpt_codes';
    }

    public function description(): Stringable|string
    {
        return 'Search for CPT codes for medical services (laboratory, imaging, procedures). Returns matching codes.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->required()->description('Medical procedure/service description'),
            'type' => $schema->string()->description('Filter by service type: laboratory, imaging, or procedure (optional)'),
            'limit' => $schema->integer()->default(5)->description('Maximum number of results to return'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $query = $request['query'] ?? '';
        $type = $request['type'] ?? null;
        $limit = $request['limit'] ?? 5;

        if (empty($query) || strlen($query) < 2) {
            return json_encode([
                'success' => false,
                'error' => 'Query must be at least 2 characters long',
                'results' => [],
            ]);
        }

        try {
            $qb = DB::table('cpt_codes')
                ->where('description', 'like', "%{$query}%")
                ->orWhere('description_es', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%")
                ->orWhere('alias', 'like', "%{$query}%")
                ->where('active', '=', true);

            if ($type) {
                $qb->where('type', '=', $type);
            }

            $results = $qb
                ->select('id', 'code', 'description', 'description_es', 'type')
                ->limit($limit)
                ->get()
                ->map(function ($item) use ($query) {
                    $score = 100;
                    if (stripos($item->description, $query) === 0 || stripos($item->description_es, $query) === 0) {
                        $score = 100;
                    } elseif (stripos($item->description, $query) !== false || stripos($item->description_es, $query) !== false) {
                        $score = 75;
                    }

                    return [
                        'id' => $item->id,
                        'code' => $item->code,
                        'description' => $item->description,
                        'description_es' => $item->description_es,
                        'type' => $item->type,
                        'relevance_score' => $score,
                    ];
                })
                ->sortByDesc('relevance_score')
                ->values()
                ->all();

            return json_encode([
                'success' => true,
                'query' => $query,
                'filter_type' => $type,
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
