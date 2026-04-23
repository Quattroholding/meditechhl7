<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\EncounterSpecialityQuestion;
use App\Models\EncounterSpecialityQuestionAnswer;
use Livewire\Component;

class SpecialityQuestions extends Component
{
    public $encounter_id;

    public $medical_specialty_id;

    public $questions = [];

    public $answers = [];

    public $quality_of_life;

    public $savedQualityOfLife = false;

    public $ipss_score = 0;

    public function mount($encounter_id, $medical_specialty_id)
    {
        $this->encounter_id = $encounter_id;
        $this->medical_specialty_id = $medical_specialty_id;
        $this->loadQuestions();
        $this->loadAnswers();
        $this->calculateScore();
    }

    public function loadQuestions()
    {
        // Cargar las 7 preguntas del IPSS desde la tabla
        $this->questions = EncounterSpecialityQuestion::where('medical_speciality_id', $this->medical_specialty_id)
            ->whereIn('question_esp', [
                'Vaciado incompleto',
                'Frecuencia',
                'Intermitencia',
                'Urgencia',
                'Flujo Débil',
                'Tirante',
                'Nocturno',
            ])
            ->orderBy('id')
            ->get();
    }

    public function loadAnswers()
    {
        $savedAnswers = EncounterSpecialityQuestionAnswer::where('encounter_id', $this->encounter_id)
            ->whereIn('encounter_speciality_question_id', $this->questions->pluck('id'))
            ->get()
            ->keyBy('encounter_speciality_question_id');

        foreach ($this->questions as $question) {
            $this->answers[$question->id] = $savedAnswers->get($question->id)?->answer ?? null;
        }

        // Cargar calidad de vida
        $qolQuestion = EncounterSpecialityQuestion::where('medical_speciality_id', $this->medical_specialty_id)
            ->where('question_esp', 'LIKE', '%Si tuviera que pasar el resto%')
            ->first();

        if ($qolQuestion) {
            $qolAnswer = EncounterSpecialityQuestionAnswer::where('encounter_id', $this->encounter_id)
                ->where('encounter_speciality_question_id', $qolQuestion->id)
                ->first();

            $this->quality_of_life = $qolAnswer?->answer;
        }
    }

    public function saveAnswer($questionId, $value)
    {
        $this->answers[$questionId] = $value;
        // dd(Encounter::findOrFail($this->encounter_id)->appointment_id);
        EncounterSpecialityQuestionAnswer::updateOrCreate(
            [
                'encounter_id' => $this->encounter_id,
                'appointment_id' => Encounter::findOrFail($this->encounter_id)->appointment_id,
                'encounter_speciality_question_id' => $questionId,
            ],
            [
                'answer' => $value,
                'created_by' => auth()->id(),
            ]
        );

        $this->calculateScore();
    }

    public function saveQualityOfLife()
    {
        $qolQuestion = EncounterSpecialityQuestion::where('medical_speciality_id', $this->medical_specialty_id)
            ->where('question_esp', 'LIKE', '%Si tuviera que pasar el resto%')
            ->first();

        if ($qolQuestion) {
            EncounterSpecialityQuestionAnswer::updateOrCreate(
                [
                    'encounter_id' => $this->encounter_id,
                    'encounter_speciality_question_id' => $qolQuestion->id,
                ],
                [
                    'answer' => $this->quality_of_life,
                    'created_by' => auth()->id(),
                ]
            );
        }

        $this->savedQualityOfLife = true;
        $this->dispatch('saved');

        // Reset el indicador después de 2 segundos
        $this->js('setTimeout(() => { $wire.savedQualityOfLife = false }, 2000)');
    }

    public function calculateScore()
    {
        $this->ipss_score = 0;

        foreach ($this->answers as $questionId => $answer) {
            if ($answer !== null) {
                // Parsear el valor numérico del JSON
                $options = json_decode($this->questions->firstWhere('id', $questionId)->options_esp, true);

                // Encontrar el índice de la opción seleccionada
                $index = array_search($answer, $options);
                if ($index !== false) {
                    $this->ipss_score += $index;
                }
            }
        }
    }

    public function getScoreSeverity()
    {
        if ($this->ipss_score <= 7) {
            return ['level' => 'Mild', 'color' => 'bg-green-500'];
        } elseif ($this->ipss_score <= 19) {
            return ['level' => 'Moderate', 'color' => 'bg-yellow-500'];
        } else {
            return ['level' => 'Severe', 'color' => 'bg-red-500'];
        }
    }

    public function render()
    {
        return view('livewire.consultation.speciality-questions');
    }
}
