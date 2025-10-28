<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSatisfactionSurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first client and admin user for the survey
        $client = Client::first();
        $adminUser = User::role('admin')->first() ?? User::first();

        if (! $client || ! $adminUser) {
            $this->command->warn('No se encontró un cliente o usuario. Por favor, cree uno primero.');

            return;
        }

        // Create the patient satisfaction survey
        $survey = Survey::create([
            'title' => 'Encuesta de Satisfacción del Paciente',
            'description' => 'Encuesta para medir la satisfacción del paciente desde el agendamiento de la cita hasta la finalización de la consulta médica.',
            'is_active' => true,
            'status' => 'active',
            'client_id' => $client->id,
            'created_by' => $adminUser->id,
            'trigger_point' => 'after_encounter',
        ]);

        // Question 1: Overall satisfaction with appointment scheduling
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿Qué tan satisfecho está con el proceso de agendamiento de su cita?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Muy insatisfecho',
                '2' => 'Insatisfecho',
                '3' => 'Neutral',
                '4' => 'Satisfecho',
                '5' => 'Muy satisfecho',
            ],
            'is_required' => true,
            'order' => 1,
        ]);

        // Question 2: Wait time before appointment
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿Cómo calificaría el tiempo de espera para obtener una cita?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Muy largo',
                '2' => 'Largo',
                '3' => 'Aceptable',
                '4' => 'Corto',
                '5' => 'Muy corto',
            ],
            'is_required' => true,
            'order' => 2,
        ]);

        // Question 3: Reception and check-in experience
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿Qué tan satisfecho está con la atención recibida en recepción?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Muy insatisfecho',
                '2' => 'Insatisfecho',
                '3' => 'Neutral',
                '4' => 'Satisfecho',
                '5' => 'Muy satisfecho',
            ],
            'is_required' => true,
            'order' => 3,
        ]);

        // Question 4: Wait time at clinic
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿Cómo calificaría el tiempo de espera en la clínica antes de ser atendido?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Muy largo',
                '2' => 'Largo',
                '3' => 'Aceptable',
                '4' => 'Corto',
                '5' => 'Muy corto',
            ],
            'is_required' => true,
            'order' => 4,
        ]);

        // Question 5: Practitioner rating - CRITICAL for ranking
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿Cómo calificaría la atención médica proporcionada por el profesional de salud?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Muy mala',
                '2' => 'Mala',
                '3' => 'Regular',
                '4' => 'Buena',
                '5' => 'Excelente',
            ],
            'is_required' => true,
            'order' => 5,
        ]);

        // Question 6: Practitioner communication
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿El profesional de salud explicó claramente su diagnóstico y tratamiento?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Nada claro',
                '2' => 'Poco claro',
                '3' => 'Moderadamente claro',
                '4' => 'Claro',
                '5' => 'Muy claro',
            ],
            'is_required' => true,
            'order' => 6,
        ]);

        // Question 7: Practitioner empathy
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿El profesional de salud mostró empatía y preocupación por su bienestar?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Nada',
                '2' => 'Poco',
                '3' => 'Moderadamente',
                '4' => 'Bastante',
                '5' => 'Mucho',
            ],
            'is_required' => true,
            'order' => 7,
        ]);

        // Question 8: Consultation time adequacy
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿El tiempo dedicado a su consulta fue adecuado?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Muy inadecuado',
                '2' => 'Inadecuado',
                '3' => 'Aceptable',
                '4' => 'Adecuado',
                '5' => 'Muy adecuado',
            ],
            'is_required' => true,
            'order' => 8,
        ]);

        // Question 9: Facility cleanliness
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿Cómo calificaría la limpieza y el orden de las instalaciones?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Muy sucia',
                '2' => 'Sucia',
                '3' => 'Aceptable',
                '4' => 'Limpia',
                '5' => 'Muy limpia',
            ],
            'is_required' => true,
            'order' => 9,
        ]);

        // Question 10: Privacy during consultation
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿Se respetó su privacidad durante la consulta?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Nada',
                '2' => 'Poco',
                '3' => 'Moderadamente',
                '4' => 'Bastante',
                '5' => 'Completamente',
            ],
            'is_required' => true,
            'order' => 10,
        ]);

        // Question 11: Would recommend practitioner
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿Recomendaría a este profesional de salud a familiares o amigos?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Definitivamente no',
                '2' => 'Probablemente no',
                '3' => 'No estoy seguro',
                '4' => 'Probablemente sí',
                '5' => 'Definitivamente sí',
            ],
            'is_required' => true,
            'order' => 11,
        ]);

        // Question 12: Overall experience rating
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => 'En general, ¿cómo calificaría su experiencia completa desde el agendamiento hasta la finalización de su consulta?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Muy mala',
                '2' => 'Mala',
                '3' => 'Regular',
                '4' => 'Buena',
                '5' => 'Excelente',
            ],
            'is_required' => true,
            'order' => 12,
        ]);

        // Question 13: Would return to clinic
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿Volvería a utilizar nuestros servicios médicos en el futuro?',
            'question_type' => 'rating',
            'options' => [
                '1' => 'Definitivamente no',
                '2' => 'Probablemente no',
                '3' => 'No estoy seguro',
                '4' => 'Probablemente sí',
                '5' => 'Definitivamente sí',
            ],
            'is_required' => true,
            'order' => 13,
        ]);

        // Question 14: Areas for improvement (open-ended)
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿Qué aspectos podríamos mejorar en nuestro servicio?',
            'question_type' => 'text',
            'options' => null,
            'is_required' => false,
            'order' => 14,
        ]);

        // Question 15: Additional comments (open-ended)
        SurveyQuestion::create([
            'survey_id' => $survey->id,
            'question_text' => '¿Tiene algún comentario adicional que desee compartir?',
            'question_type' => 'textarea',
            'options' => null,
            'is_required' => false,
            'order' => 15,
        ]);

        $this->command->info('Encuesta de Satisfacción del Paciente creada exitosamente con 15 preguntas.');
        $this->command->info('Trigger point configurado: after_encounter');
    }
}
