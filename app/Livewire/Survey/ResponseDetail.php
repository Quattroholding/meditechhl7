<?php

namespace App\Livewire\Survey;

use App\Models\SurveyResponse;
use Livewire\Component;

class ResponseDetail extends Component
{
    public SurveyResponse $response;

    public function mount(SurveyResponse $response)
    {
        $this->response = $response->load(['survey.questions', 'patient', 'practitioner']);
    }

    public function render()
    {
        $globalRating = $this->calculateGlobalRating();

        return view('livewire.survey.response-detail', compact('globalRating'));
    }

    private function calculateGlobalRating(): array
    {
        $survey = $this->response->survey;
        $completedResponses = $survey->responses()
            ->where('status', 'completed')
            ->get();

        if ($completedResponses->isEmpty()) {
            return [
                'average' => 0,
                'total' => 0,
            ];
        }

        $ratingQuestions = $survey->questions()
            ->where('question_type', 'rating')
            ->get();

        if ($ratingQuestions->isEmpty()) {
            return [
                'average' => 0,
                'total' => 0,
            ];
        }

        $ratings = [];

        foreach ($completedResponses as $response) {
            foreach ($ratingQuestions as $question) {
                $rating = $response->responses[$question->id] ?? null;
                if ($rating !== null && $rating !== '') {
                    $rating = (int) $rating;
                    if ($rating >= 1 && $rating <= 5) {
                        $ratings[] = $rating;
                    }
                }
            }
        }

        $average = count($ratings) > 0 ? array_sum($ratings) / count($ratings) : 0;

        return [
            'average' => round($average, 2),
            'total' => count($ratings),
        ];
    }
}
