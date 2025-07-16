<?php

namespace App\Livewire\Survey;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use Livewire\Component;

class QuestionBuilder extends Component
{
    public $surveyId;
    public $questionId = null;
    public $question_text = '';
    public $question_type = 'text';
    public $is_required = false;
    public $options = [];
    public $newOption = '';
    public $editingQuestion = false;

    protected $listeners = ['survey-updated' => 'refreshQuestions'];

    public function mount($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    public function addOption()
    {
        if (trim($this->newOption) !== '') {
            $this->options[] = trim($this->newOption);
            $this->newOption = '';
        }
    }

    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }

    public function editQuestion($questionId)
    {
        $question = SurveyQuestion::findOrFail($questionId);
        $this->questionId = $questionId;
        $this->question_text = $question->question_text;
        $this->question_type = $question->question_type;
        $this->is_required = $question->is_required;
        $this->options = $question->options ?? [];
        $this->editingQuestion = true;
    }

    public function cancelEdit()
    {
        $this->resetForm();
    }

    public function deleteQuestion($questionId)
    {
        SurveyQuestion::findOrFail($questionId)->delete();
        session()->flash('message', 'Pregunta eliminada exitosamente.');
    }

    public function saveQuestion()
    {
        $this->validate([
            'question_text' => 'required|string|max:500',
            'question_type' => 'required|in:text,textarea,select,radio,checkbox,rating,number',
        ]);

        $data = [
            'survey_id' => $this->surveyId,
            'question_text' => $this->question_text,
            'question_type' => $this->question_type,
            'is_required' => $this->is_required,
            'options' => in_array($this->question_type, ['select', 'radio', 'checkbox']) ? $this->options : null,
        ];

        if ($this->editingQuestion && $this->questionId) {
            $question = SurveyQuestion::findOrFail($this->questionId);
            $question->update($data);
            session()->flash('message', 'Pregunta actualizada exitosamente.');
        } else {
            $maxOrder = SurveyQuestion::where('survey_id', $this->surveyId)->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
            SurveyQuestion::create($data);
            session()->flash('message', 'Pregunta agregada exitosamente.');
        }

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->questionId = null;
        $this->question_text = '';
        $this->question_type = 'text';
        $this->is_required = false;
        $this->options = [];
        $this->newOption = '';
        $this->editingQuestion = false;
    }

    public function refreshQuestions()
    {
        $this->render();
    }

    public function render()
    {
        $survey = Survey::with(['questions' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($this->surveyId);

        $questionTypes = [
            'text' => 'Texto corto',
            'textarea' => 'Texto largo',
            'select' => 'Lista desplegable',
            'radio' => 'Opción única',
            'checkbox' => 'Opción múltiple',
            'rating' => 'Calificación (1-5)',
            'number' => 'Número',
        ];

        return view('livewire.survey.question-builder', compact('survey', 'questionTypes'));
    }
}
