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
        return view('livewire.survey.response-detail');
    }
}
