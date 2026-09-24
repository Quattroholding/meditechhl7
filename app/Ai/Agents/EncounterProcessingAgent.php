<?php

namespace App\Ai\Agents;

use App\Ai\Tools\CreateEncounterDataTool;
use App\Ai\Tools\GetVitalSignsConfigTool;
use App\Ai\Tools\SearchCptTool;
use App\Ai\Tools\SearchIcd10Tool;
use App\Ai\Tools\SearchMedicationTool;
use App\Models\Encounter;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::Anthropic)]
#[Model('claude-opus-4-5-20251101')]
class EncounterProcessingAgent implements Agent, HasTools
{
    use Promptable;

    public function __construct(
        private int $encounterId,
        private string $transcription,
    ) {}

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'EOT'
You are a medical AI assistant specialized in processing clinical voice transcriptions and extracting structured SOAP (Subjective, Objective, Assessment, Plan) documentation.

Your task is to analyze a medical transcription and systematically populate an encounter with structured medical data using the provided tools.

## CRITICAL: Response Format Instructions

You MUST respond with ONLY valid JSON. Do NOT include any markdown, explanations, or text outside of the JSON structure.

## Processing Guidelines

### SUBJECTIVE (S)
- Extract the chief complaint/reason for visit
- Identify present illness details (description, location, severity, duration, onset, progression, associated symptoms)
- Note any general observations about the patient's presentation

### OBJECTIVE (O)
- Extract vital signs (blood pressure, heart rate, temperature, respiratory rate, oxygen saturation, weight, height)
- Identify physical examination findings for specific body systems
- Use the vital signs configuration tool to map findings to valid LOINC codes

### ASSESSMENT (A)
- Identify all diagnoses mentioned or implied
- Use the ICD-10 search tool to find correct diagnostic codes
- Prioritize conditions by clinical significance

### PLAN (P)
- Extract medications prescribed with dosage, frequency, and duration
- Identify laboratory tests, imaging studies, and procedures ordered
- Use the CPT search tool to map services to proper codes
- Include clinical reasoning for each plan item

## Tool Usage

Use the following tools systematically:
1. **search_icd10_codes**: Find diagnostic codes for identified conditions
2. **search_cpt_codes**: Find codes for laboratory, imaging, and procedures
3. **search_medications**: Verify medication names and available dosages
4. **get_vital_signs_config**: Get valid vital sign types with LOINC codes
5. **create_encounter_data**: Save all extracted data to the encounter

## Data Structure for create_encounter_data

### Subjective Section:
```json
{
  "encounter_id": <ID>,
  "section": "subjective",
  "data": {
    "reason": "Chief complaint",
    "present_illness": [
      {
        "description": "Text description",
        "locations": ["body part"],
        "severity": "mild|moderate|severe",
        "duration": "3 days",
        "onset": "sudden|gradual",
        "progression": "improving|stable|worsening"
      }
    ],
    "general_notes": "Additional notes"
  }
}
```

### Objective Section:
```json
{
  "encounter_id": <ID>,
  "section": "objective",
  "data": {
    "vital_signs": [
      {
        "description": "Blood pressure",
        "value": "150/95",
        "unit": "mmHg"
      },
      {
        "description": "Heart rate",
        "value": 72,
        "unit": "bpm"
      }
    ]
  }
}
```

### Assessment Section:
```json
{
  "encounter_id": <ID>,
  "section": "assessment",
  "data": {
    "diagnoses": [
      {
        "icd10_code": "I10",
        "note": "Essential hypertension"
      }
    ]
  }
}
```

### Plan Section:
```json
{
  "encounter_id": <ID>,
  "section": "plan",
  "data": {
    "medications": [
      {
        "medication_id": 64,
        "medication_name": "Losartán",
        "dosage_text": "50 mg",
        "frequency": "Cada 12 horas",
        "quantity": 60,
        "duration": "30",
        "duration_type": "dias",
        "notes": "Reason for medication"
      }
    ],
    "service_requests": [
      {
        "service_type": "laboratory",
        "cpt_code": "85027",
        "description": "Hemograma completo",
        "quantity": 1
      }
    ]
  }
}
```

## CRITICAL: Using the Encounter ID

The encounter ID is: {encounter_id}
YOU MUST pass this exact ID in EVERY call to create_encounter_data tool.

Example tool call structure:
{
  "type": "use_tool",
  "tool_name": "create_encounter_data",
  "tool_input": {
    "encounter_id": {encounter_id},
    "section": "subjective",
    "data": { ... }
  }
}

## Important Notes

- ALWAYS include encounter_id={encounter_id} in every create_encounter_data tool call
- Always search for codes before recording data to ensure accuracy
- If a code cannot be found, record the description anyway and note it in warnings
- Blood pressure should be sent as string "systolic/diastolic" (e.g., "150/95"), which will be automatically split
- Do NOT make up medication_ids - if you cannot find the exact medication, still create it with medication_name field filled
- Always call create_encounter_data for EACH section (subjective, objective, assessment, plan) separately
- Multi-tenancy: All data operations automatically respect the current user's client context
- Be thorough but efficient - extract all clinically relevant information
- DO NOT ADD ANY TEXT OR MARKDOWN OUTSIDE THE JSON RESPONSE
EOT;
    }

    /**
     * Get the tools available to the agent.
     */
    public function tools(): iterable
    {
        return [
            new SearchIcd10Tool,
            new SearchCptTool,
            new SearchMedicationTool,
            new GetVitalSignsConfigTool,
            new CreateEncounterDataTool,
        ];
    }

    /**
     * Process the transcription and return structured results.
     */
    public function process(): array
    {
        try {
            Log::info('EncounterProcessingAgent: Starting processing', [
                'encounter_id' => $this->encounterId,
                'transcription_length' => strlen($this->transcription),
            ]);

            // Verify encounter exists
            $encounter = Encounter::findOrFail($this->encounterId);
            Log::info('EncounterProcessingAgent: Encounter loaded', [
                'encounter_id' => $encounter->id,
                'patient_id' => $encounter->patient_id,
            ]);

            // Call Claude with the transcription
            $prompt = <<<EOT
ENCOUNTER ID: {$this->encounterId}

Process this medical transcription and extract SOAP documentation:

{$this->transcription}

Remember: You MUST pass encounter_id={$this->encounterId} in every create_encounter_data tool call.
EOT;

            $result = $this->prompt(
                prompt: $prompt,
                provider: 'anthropic',
                model: 'claude-opus-4-5-20251101',
            );

            Log::info('EncounterProcessingAgent: Processing completed', [
                'encounter_id' => $this->encounterId,
                'response_length' => strlen($result),
            ]);

            // Parse the response - try to extract JSON if there's extra text
            $parsedResult = $this->extractJsonFromResponse($result);

            return array_merge($parsedResult, [
                'encounter_id' => $this->encounterId,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('EncounterProcessingAgent: Processing failed', [
                'encounter_id' => $this->encounterId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'processing_status' => 'failed',
                'error' => $e->getMessage(),
                'encounter_id' => $this->encounterId,
                'timestamp' => now()->toIso8601String(),
            ];
        }
    }

    /**
     * Extract JSON from response, handling cases where there's extra text.
     */
    private function extractJsonFromResponse(string $response): array
    {
        // First, try parsing as-is
        $parsed = json_decode($response, true);
        if (is_array($parsed)) {
            return $parsed;
        }

        // Try to find JSON object in the response
        if (preg_match('/\{[\s\S]*}/', $response, $matches)) {
            $parsed = json_decode($matches[0], true);
            if (is_array($parsed)) {
                return $parsed;
            }
        }

        // If we couldn't extract JSON, return empty array
        Log::warning('Failed to extract JSON from Agent response', [
            'response_length' => strlen($response),
            'first_200_chars' => substr($response, 0, 200),
        ]);

        return [];
    }
}
