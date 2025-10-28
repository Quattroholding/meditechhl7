<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SurveyQuestion;
use App\Models\SurveyQuestionResponse;
use App\Models\SurveyResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SurveyController extends Controller
{
    /**
     * Get the next unanswered question for a survey response
     * Used for WhatsApp integration - sends one question at a time
     */
    public function getNextQuestion(string $surveyResponseId): JsonResponse
    {
        try {
            // Find the survey response by ID or token
            $surveyResponse = SurveyResponse::where('id', $surveyResponseId)
                ->orWhere('token', $surveyResponseId)
                ->first();

            if (! $surveyResponse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Encuesta no encontrada.',
                ], 404);
            }

            // Check if survey is already completed
            if ($surveyResponse->isCompleted()) {
                return response()->json([
                    'success' => true,
                    'completed' => true,
                    'message' => 'La encuesta ya ha sido completada. ¡Gracias por su participación!',
                    'survey_response_id' => $surveyResponse->id,
                ], 200);
            }

            // Get all questions for this survey ordered by 'order'
            $allQuestions = SurveyQuestion::where('survey_id', $surveyResponse->survey_id)
                ->orderBy('order')
                ->get();

            if ($allQuestions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron preguntas para esta encuesta.',
                ], 404);
            }

            // Get IDs of already answered questions
            $answeredQuestionIds = SurveyQuestionResponse::where('survey_response_id', $surveyResponse->id)
                ->pluck('survey_question_id')
                ->toArray();

            // Find the first unanswered question
            $nextQuestion = $allQuestions->first(function ($question) use ($answeredQuestionIds) {
                return ! in_array($question->id, $answeredQuestionIds);
            });

            // If no unanswered question found, survey is complete
            if (! $nextQuestion) {
                // Mark survey as completed
                $surveyResponse->update([
                    'status' => 'completed',
                    'submitted_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'completed' => true,
                    'message' => '¡Encuesta completada! Gracias por su tiempo y respuestas.',
                    'survey_response_id' => $surveyResponse->id,
                    'total_questions' => $allQuestions->count(),
                    'answered_questions' => count($answeredQuestionIds),
                ], 200);
            }

            // Calculate progress
            $totalQuestions = $allQuestions->count();
            $answeredCount = count($answeredQuestionIds);
            $progress = $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100, 2) : 0;

            // Format the question for WhatsApp
            $formattedQuestion = $this->formatQuestionForWhatsApp($nextQuestion, $answeredCount + 1, $totalQuestions);

            return response()->json([
                'success' => true,
                'completed' => false,
                'survey_response_id' => $surveyResponse->id,
                'question' => [
                    'id' => $nextQuestion->id,
                    'question_text' => $nextQuestion->question_text,
                    'question_type' => $nextQuestion->question_type,
                    'options' => $nextQuestion->options,
                    'is_required' => $nextQuestion->is_required,
                    'order' => $nextQuestion->order,
                    'formatted_text' => $formattedQuestion,
                ],
                'progress' => [
                    'current_question' => $answeredCount + 1,
                    'total_questions' => $totalQuestions,
                    'percentage' => $progress,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error getting next survey question', [
                'survey_response_id' => $surveyResponseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la siguiente pregunta.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Save a question response
     * Used for WhatsApp integration - saves one answer at a time
     */
    public function saveQuestionResponse(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'survey_response_id' => 'required',
                'survey_question_id' => 'required|exists:survey_questions,id',
                'answer_text' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Find the survey response by ID or token
            $surveyResponse = SurveyResponse::where('id', $request->survey_response_id)
                ->orWhere('token', $request->survey_response_id)
                ->first();

            if (! $surveyResponse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Encuesta no encontrada.',
                ], 404);
            }

            // Check if survey is already completed
            if ($surveyResponse->isCompleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La encuesta ya ha sido completada.',
                ], 400);
            }

            // Get the question
            $question = SurveyQuestion::find($request->survey_question_id);

            if (! $question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pregunta no encontrada.',
                ], 404);
            }

            // Verify the question belongs to the survey
            if ($question->survey_id !== $surveyResponse->survey_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'La pregunta no pertenece a esta encuesta.',
                ], 400);
            }

            // Validate answer based on question type
            $validationResult = $this->validateAnswer($question, $request->answer_text);
            if (! $validationResult['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validationResult['message'],
                ], 422);
            }

            // Check if question was already answered
            $existingResponse = SurveyQuestionResponse::where('survey_response_id', $surveyResponse->id)
                ->where('survey_question_id', $question->id)
                ->first();

            if ($existingResponse) {
                // Update existing response
                $existingResponse->update([
                    'answer_text' => $request->answer_text,
                    'answer_data' => $request->answer_data ?? null,
                ]);

                $questionResponse = $existingResponse;
            } else {
                // Create new response
                $questionResponse = SurveyQuestionResponse::create([
                    'survey_response_id' => $surveyResponse->id,
                    'survey_question_id' => $question->id,
                    'answer_text' => $request->answer_text,
                    'answer_data' => $request->answer_data ?? null,
                ]);
            }

            // Get progress
            $totalQuestions = SurveyQuestion::where('survey_id', $surveyResponse->survey_id)->count();
            $answeredQuestions = SurveyQuestionResponse::where('survey_response_id', $surveyResponse->id)
                ->distinct('survey_question_id')
                ->count();

            $progress = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 2) : 0;

            // Check if all questions are answered
            $allAnswered = $answeredQuestions >= $totalQuestions;

            if ($allAnswered) {
                $surveyResponse->update([
                    'status' => 'completed',
                    'submitted_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Respuesta guardada exitosamente.',
                'question_response_id' => $questionResponse->id,
                'all_questions_answered' => $allAnswered,
                'progress' => [
                    'answered_questions' => $answeredQuestions,
                    'total_questions' => $totalQuestions,
                    'percentage' => $progress,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error saving survey question response', [
                'survey_response_id' => $request->survey_response_id,
                'survey_question_id' => $request->survey_question_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la respuesta.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get survey progress
     */
    public function getSurveyProgress(string $surveyResponseId): JsonResponse
    {
        try {
            // Find the survey response by ID or token
            $surveyResponse = SurveyResponse::where('id', $surveyResponseId)
                ->orWhere('token', $surveyResponseId)
                ->with('survey')
                ->first();

            if (! $surveyResponse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Encuesta no encontrada.',
                ], 404);
            }

            // Get total questions
            $totalQuestions = SurveyQuestion::where('survey_id', $surveyResponse->survey_id)->count();

            // Get answered questions count
            $answeredQuestions = SurveyQuestionResponse::where('survey_response_id', $surveyResponse->id)
                ->distinct('survey_question_id')
                ->count();

            // Calculate progress
            $progress = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 2) : 0;

            // Get list of answered question IDs
            $answeredQuestionIds = SurveyQuestionResponse::where('survey_response_id', $surveyResponse->id)
                ->pluck('survey_question_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'survey_response_id' => $surveyResponse->id,
                'survey' => [
                    'id' => $surveyResponse->survey->id,
                    'title' => $surveyResponse->survey->title,
                    'description' => $surveyResponse->survey->description,
                ],
                'status' => $surveyResponse->status,
                'completed' => $surveyResponse->isCompleted(),
                'submitted_at' => $surveyResponse->submitted_at?->format('Y-m-d H:i:s'),
                'progress' => [
                    'answered_questions' => $answeredQuestions,
                    'total_questions' => $totalQuestions,
                    'percentage' => $progress,
                    'remaining_questions' => $totalQuestions - $answeredQuestions,
                ],
                'answered_question_ids' => $answeredQuestionIds,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error getting survey progress', [
                'survey_response_id' => $surveyResponseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el progreso de la encuesta.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Format question for WhatsApp display
     */
    private function formatQuestionForWhatsApp(SurveyQuestion $question, int $currentNumber, int $totalQuestions): string
    {
        $text = "*Pregunta {$currentNumber} de {$totalQuestions}*\n\n";
        $text .= $question->question_text."\n";

        if ($question->question_type === 'rating' && $question->options) {
            $text .= "\n*Opciones:*\n";
            foreach ($question->options as $key => $value) {
                $text .= "{$key}. {$value}\n";
            }
            $text .= "\nResponde con el número (1-5)";
        } elseif ($question->question_type === 'text') {
            $text .= "\nResponde con tu respuesta (texto breve)";
        } elseif ($question->question_type === 'textarea') {
            $text .= "\nResponde con tu respuesta (puedes escribir varias líneas)";
        }

        if ($question->is_required) {
            $text .= ' *';
        }

        return $text;
    }

    /**
     * Validate answer based on question type
     */
    private function validateAnswer(SurveyQuestion $question, string $answer): array
    {
        // Check if required
        if ($question->is_required && empty(trim($answer))) {
            return [
                'valid' => false,
                'message' => 'Esta pregunta es obligatoria.',
            ];
        }

        // Validate based on question type
        if ($question->question_type === 'rating') {
            if (! is_numeric($answer)) {
                return [
                    'valid' => false,
                    'message' => 'La respuesta debe ser un número.',
                ];
            }

            $numericAnswer = (int) $answer;

            // Check if answer is in the valid range (typically 1-5)
            if ($question->options) {
                $validKeys = array_keys($question->options);
                if (! in_array((string) $numericAnswer, $validKeys, true)) {
                    return [
                        'valid' => false,
                        'message' => 'La respuesta debe ser uno de los números válidos: '.implode(', ', $validKeys),
                    ];
                }
            } elseif ($numericAnswer < 1 || $numericAnswer > 5) {
                return [
                    'valid' => false,
                    'message' => 'La respuesta debe estar entre 1 y 5.',
                ];
            }
        }

        return [
            'valid' => true,
            'message' => 'Respuesta válida.',
        ];
    }
}
