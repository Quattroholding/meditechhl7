<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\EncounterQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EncounterQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $options_esp = [                    
            "0" => "Nada", 
            "1" => "Menos de 1 de cada 5 veces", 
            "2" => "Menos de la mitad de las veces", 
            "3" => "Cerca de la mitad de las veces", 
            "4" => "Más de la mitad de las time", 
            "5" => "Casi siempre"];
        $options_eng = [
            "0" => "Not at all", 
            "1" => "Less than 1 in 5 times", 
            "2" => "Less than half the time", 
            "3" => "About half the time", 
            "4" => "More than half the time", 
            "5" => "Almost always"
        ];
        $questions = [
            [
                'medical_speciality_id' => 42,
                'question_esp' => 'Vaciado incompleto',
                'question_eng' => 'Incomplete Emptying',
                'options_esp' => json_encode($options_esp, JSON_FORCE_OBJECT),
                'options_eng' => json_encode($options_eng, JSON_FORCE_OBJECT),
                'deleted_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'description_esp' => '¿Con qué frecuencia ha tenido la sensación de no vaciar la vejiga?',
                'description_eng' => 'How often have you had the sensation of not emptying your bladder?'
            ],
            [
                'medical_speciality_id' => 42,
                'question_esp' => 'Frecuencia',
                'question_eng' => 'Frequency',
                'options_esp' => json_encode($options_esp, JSON_FORCE_OBJECT),
                'options_eng' => json_encode($options_eng, JSON_FORCE_OBJECT),
                'deleted_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'description_esp' => '¿Con qué frecuencia ha tenido que orinar menos de cada dos horas?',
                'description_eng' => 'How often have you had to urinate less than every two hours?'
            ],
            [
                'medical_speciality_id' => 42,
                'question_esp' => 'Intermitencia',
                'question_eng' => 'Intermittency',
                'options_esp' => json_encode($options_esp, JSON_FORCE_OBJECT),
                'options_eng' => json_encode($options_eng, JSON_FORCE_OBJECT),
                'deleted_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'description_esp' => '¿Con qué frecuencia ha notado que se detuvo y comenzó de nuevo varias veces al orinar?',
                'description_eng' => 'How often have you found you stopped and started again several times when you urinated?'
            ],
            [
                'medical_speciality_id' => 42,
                'question_esp' => 'Urgencia',
                'question_eng' => 'Urgency',
                'options_esp' => json_encode($options_esp, JSON_FORCE_OBJECT),
                'options_eng' => json_encode($options_eng, JSON_FORCE_OBJECT),
                'deleted_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'description_esp' => '¿Con qué frecuencia le ha resultado difícil posponer la micción?',
                'description_eng' => 'How often have you found it difficult to postpone urination?'
            ],
            [
                'medical_speciality_id' => 42,
                'question_esp' => 'Flujo Débil',
                'question_eng' => 'Weak stream',
                'options_esp' => json_encode($options_esp, JSON_FORCE_OBJECT),
                'options_eng' => json_encode($options_eng, JSON_FORCE_OBJECT),
                'deleted_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'description_esp' => '¿Con qué frecuencia ha tenido un chorro de orina débil?',
                'description_eng' => 'How often have you had a weak urinary stream?'
            ],
            [
                'medical_speciality_id' => 42,
                'question_esp' => 'Tirante',
                'question_eng' => 'Straining',
                'options_esp' => json_encode($options_esp, JSON_FORCE_OBJECT),
                'options_eng' => json_encode($options_eng, JSON_FORCE_OBJECT),
                'deleted_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'description_esp' => '¿Con qué frecuencia ha tenido que esforzarse para empezar a orinar?',
                'description_eng' => 'How often have you had to strain to start urination?'
            ],
            [
                'medical_speciality_id' => 42,
                'question_esp' => 'Noturno',
                'question_eng' => 'Nocturia',
                'options_esp' => json_encode([
                    "0" => "Nunca", 
                    "1" => "1 vez", 
                    "2" => "2 Veces", 
                    "3" => "3 Veces", 
                    "4" => "4 Veces", 
                    "5" => "5 Veces o mas"
                ], JSON_FORCE_OBJECT),
                'options_eng' => json_encode([
                    "0" => "None", 
                    "1" => "1 Time", 
                    "2" => "2 Times", 
                    "3" => "3 Times", 
                    "4" => "4 Times", 
                    "5" => "5 Times or more"
                ], JSON_FORCE_OBJECT),
                'deleted_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'description_esp' => '¿Cuántas veces suele levantarse por la noche para orinar?',
                'description_eng' => 'How many times do you typically get up at night to urinate?'
            ],
            [
                'medical_speciality_id' => 42,
                'question_esp' => '¿Si tuviera que pasar el resto de su vida con su condición urinaria tal como está ahora, ¿cómo se sentiría al respecto?',
                'question_eng' => 'If you were to spend the rest of your life with your urinary condition just the way it is now, how would you feel about that?',
                'options_esp' => json_encode([
                    "0" => "Encantado", 
                    "1" => "Complacido", 
                    "2" => "Más satisfecho", 
                    "3" => "Mixto: igualmente satisfecho/insatisfecho", 
                    "4" => "Más bien insatisfecho", 
                    "5" => "Descontento", 
                    "6" => "Terrible"
                ], JSON_FORCE_OBJECT),
                'options_eng' => json_encode([
                    "0" => "Delighted", 
                    "1" => "Pleased", 
                    "2" => "Mostly satisfied", 
                    "3" => "Mixed: Equally satisfied /dissatisfied", 
                    "4" => "Mostly dissatisfied", 
                    "5" => "Unhappy", 
                    "6" => "Terrible"
                ], JSON_FORCE_OBJECT),
                'deleted_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'description_esp' => 'Evaluación de la calidad de vida por síntomas urinarios',
                'description_eng' => 'Assessment of quality of life due to urinary symptoms'
            ],
            ];

            foreach ($questions as $question) {
                EncounterQuestion::create(
                $question //crear con todos los datos
                );
        }
    }
}
