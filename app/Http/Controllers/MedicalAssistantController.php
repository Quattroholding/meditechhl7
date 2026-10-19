<?php

namespace App\Http\Controllers;

use App\Services\ClaudeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MedicalAssistantController extends Controller
{
    public function __construct(
        private ClaudeService $claude
    ) {}

    public function analyzeSymptoms(Request $request)
    {
        try {
            $symptoms = $request->input('symptoms');

            $analysis = $this->claude->ask(
                "Analiza estos síntomas y proporciona posibles diagnósticos diferenciales: {$symptoms}",
                [
                    'system' => 'Eres un asistente médico. Proporciona información educativa pero recuerda que no reemplazas la consulta médica.',
                    'temperature' => 0.3,
                ]
            );

            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);
        } catch (\Exception $e) {
            Log::error('Error analyzing symptoms with Claude', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al analizar los síntomas',
            ], 500);
        }
    }
}