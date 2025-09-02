<?php

namespace App\Livewire\Survey;

use App\Models\Survey;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SurveyBuilder extends Component
{
    public $surveyId;

    public $title = '';

    public $description = '';

    public $status = 'draft';

    public $is_active = true;

    public function mount($surveyId = null)
    {
        if ($surveyId) {
            $this->surveyId = $surveyId;
            $this->loadSurvey();
        }
    }

    public function loadSurvey()
    {
        $survey = Survey::findOrFail($this->surveyId);
        $this->title = $survey->title;
        $this->description = $survey->description;
        $this->status = $survey->status;
        $this->is_active = $survey->is_active;
    }

    public function saveSurvey()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,inactive',
        ]);

        if ($this->surveyId) {
            $survey = Survey::findOrFail($this->surveyId);
            $survey->update([
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'is_active' => $this->is_active,
            ]);
        } else {
            $survey = Survey::create([
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'is_active' => $this->is_active,
                'client_id' => Auth::user()->getCurrentClient()->id,
                'created_by' => Auth::id(),
            ]);
            $this->surveyId = $survey->id;
        }

        session()->flash('message.success', 'Encuesta guardada exitosamente.');
        $this->dispatch('survey-updated');
    }

    public function render()
    {
        $survey = $this->surveyId ? Survey::with(['questions' => function ($query) {
            $query->orderBy('order');
        }])->find($this->surveyId) : null;

        return view('livewire.survey.survey-builder', compact('survey'));
    }
}
