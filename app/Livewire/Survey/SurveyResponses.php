<?php

namespace App\Livewire\Survey;

use App\Models\Survey;
use Livewire\Component;
use Livewire\WithPagination;

class SurveyResponses extends Component
{
    use WithPagination;

    public Survey $survey;

    public $search = '';

    public $statusFilter = 'completed';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount(Survey $survey)
    {
        $this->survey = $survey->load('questions');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $responses = $this->survey->responses()
            ->with(['patient', 'practitioner', 'encounter'])
            ->when($this->search, function ($query) {
                $query->whereHas('patient', function ($q) {
                    $q->where('first_name', 'like', '%'.$this->search.'%')
                        ->orWhere('last_name', 'like', '%'.$this->search.'%');
                })->orWhereHas('practitioner', function ($q) {
                    $q->where('first_name', 'like', '%'.$this->search.'%')
                        ->orWhere('last_name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        return view('livewire.survey.survey-responses', [
            'survey' => $this->survey,
            'responses' => $responses,
            'totalResponses' => $this->survey->responses()->count(),
            'completedResponses' => $this->survey->responses()->where('status', 'completed')->count(),
            'pendingResponses' => $this->survey->responses()->where('status', '!=', 'completed')->count(),
            'surveyStats' => $this->calculateSurveyStats(),
        ]);
    }

    private function calculateIndividualRating(mixed $response): array
    {
        $ratingQuestions = $this->survey->questions()
            ->where('question_type', 'rating')
            ->get();

        if ($ratingQuestions->isEmpty()) {
            return [
                'average' => 0,
                'total' => 0,
            ];
        }

        $ratings = [];

        foreach ($ratingQuestions as $question) {
            $rating = $response->responses[$question->id] ?? null;
            if ($rating !== null && $rating !== '') {
                $rating = (int) $rating;
                if ($rating >= 1 && $rating <= 5) {
                    $ratings[] = $rating;
                }
            }
        }

        $average = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : 0;

        return [
            'average' => round($average, 2),
            'total' => count($ratings),
        ];
    }

    private function calculateSurveyStats(): array
    {
        $completedResponses = $this->survey->responses()
            ->where('status', 'completed')
            ->get();

        if ($completedResponses->isEmpty()) {
            return [
                'averageRating' => 0,
                'totalRatings' => 0,
                'ratingDistribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'ratingPercentage' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'questionStats' => [],
            ];
        }

        $ratingQuestions = $this->survey->questions()
            ->where('question_type', 'rating')
            ->get();

        if ($ratingQuestions->isEmpty()) {
            return [
                'averageRating' => 0,
                'totalRatings' => 0,
                'ratingDistribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'ratingPercentage' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
                'questionStats' => [],
            ];
        }

        $ratings = [];
        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $questionStats = [];

        foreach ($ratingQuestions as $question) {
            $questionRatings = [];
            $questionDist = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

            foreach ($completedResponses as $response) {
                $rating = $response->responses[$question->id] ?? null;
                if ($rating !== null && $rating !== '') {
                    $rating = (int) $rating;
                    if ($rating >= 1 && $rating <= 5) {
                        $questionRatings[] = $rating;
                        $questionDist[$rating]++;
                        $ratings[] = $rating;
                        $distribution[$rating]++;
                    }
                }
            }

            $questionAverage = count($questionRatings) > 0 ? array_sum($questionRatings) / count($questionRatings) : 0;
            $questionPercentage = [];

            for ($i = 1; $i <= 5; $i++) {
                $questionPercentage[$i] = count($questionRatings) > 0 ? round(($questionDist[$i] / count($questionRatings)) * 100) : 0;
            }

            $questionStats[$question->id] = [
                'question' => $question->question_text,
                'averageRating' => round($questionAverage, 2),
                'totalRatings' => count($questionRatings),
                'distribution' => $questionDist,
                'percentage' => $questionPercentage,
            ];
        }

        $averageRating = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : 0;
        $percentage = [];
        $totalRatings = count($ratings);

        for ($i = 1; $i <= 5; $i++) {
            $percentage[$i] = $totalRatings > 0 ? round(($distribution[$i] / $totalRatings) * 100) : 0;
        }

        return [
            'averageRating' => round($averageRating, 2),
            'totalRatings' => $totalRatings,
            'ratingDistribution' => $distribution,
            'ratingPercentage' => $percentage,
            'questionStats' => $questionStats,
        ];
    }
}
