<?php

namespace App\Livewire\Survey;

use App\Models\Survey;
use Livewire\Component;
use Livewire\WithPagination;

class SurveyList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function delete($surveyId)
    {
        $survey = Survey::findOrFail($surveyId);
        $survey->delete();

        session()->flash('message.success', 'Encuesta eliminada exitosamente.');
    }

    public function render()
    {
        $surveys = Survey::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->with(['creator', 'questions'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.survey.survey-list', compact('surveys'));
    }
}
