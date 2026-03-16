<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HemoScreenStandaloneResult;
use App\Notifications\HemoScreenResultReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HemoScreenStandaloneController extends Controller
{
    /**
     * Store HemoScreen results for standalone users
     *
     * This endpoint receives CBC test results from HemoScreen devices
     * for standalone users who only want to view lab results without
     * using full SAMI clinic management features.
     */
    public function __invoke(Request $request)
    {
        return DB::transaction(function () use ($request) {

            // 1. Validate token mode = 'standalone'
            $token = $request->api_token;

            if (! $token || ! $token->isStandaloneMode()) {
                Log::warning('Invalid token mode for standalone endpoint', [
                    'token_name' => $token?->name,
                    'token_mode' => $token?->mode,
                    'endpoint' => 'hemoscreen-standalone',
                ]);

                return response()->json([
                    'error' => 'Invalid token mode',
                    'message' => 'Este endpoint requiere un token en modo standalone',
                ], 403);
            }

            // 2. Get authenticated practitioner from token
            $practitioner = $request->authenticated_practitioner;

            if (! $practitioner) {
                return response()->json([
                    'error' => 'Token sin practitioner',
                    'message' => 'Este endpoint requiere un token asociado a un practitioner',
                ], 403);
            }

            // 3. Validate practitioner is standalone
            if (! $practitioner->isStandalone()) {
                return response()->json([
                    'error' => 'Practitioner not configured for standalone mode',
                    'message' => 'El practitioner no está configurado en modo standalone',
                ], 403);
            }

            // 4. Validate request payload (accept both 'loinc' and 'code' for compatibility)
            $validated = $request->validate([
                'control_id' => 'required|string|max:100',
                'message_type' => 'required|string|max:50',
                'device_serial' => 'nullable|string|max:100',
                'patient_identifier' => 'nullable|string|max:255',
                'observations' => 'required|array|min:1',
                'observations.*.loinc' => 'required_without:observations.*.code|string',
                'observations.*.code' => 'required_without:observations.*.loinc|string',
                'observations.*.value' => 'required',
                'observations.*.unit' => 'required|string',
                'observations.*.name' => 'nullable|string',
            ]);

            // Normalize observations: convert 'loinc' to 'code' if needed
            foreach ($validated['observations'] as $index => $obs) {
                if (isset($obs['loinc']) && ! isset($obs['code'])) {
                    $validated['observations'][$index]['code'] = $obs['loinc'];
                    unset($validated['observations'][$index]['loinc']);
                }
                // Set default name if not provided
                if (! isset($validated['observations'][$index]['name'])) {
                    $validated['observations'][$index]['name'] = $validated['observations'][$index]['code'];
                }
            }

            // 5. Load user relationship
            $practitioner->load('user');

            if (! $practitioner->user || ! $practitioner->user->default_client_id) {
                return response()->json([
                    'error' => 'Configuración incompleta',
                    'message' => 'El practitioner no tiene un usuario o cliente por defecto configurado',
                ], 500);
            }

            $clientId = $practitioner->user->default_client_id;

            // 6. Check for duplicate message_control_id
            $existing = HemoScreenStandaloneResult::where('scb_id', $clientId)
                ->where('message_control_id', $validated['control_id'])
                ->first();

            if ($existing) {
                Log::info('Duplicate HemoScreen standalone message ignored', [
                    'message_control_id' => $validated['control_id'],
                    'existing_result_id' => $existing->id,
                    'practitioner_id' => $practitioner->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Duplicate message (previously processed)',
                    'result_id' => $existing->id,
                ], 200);
            }

            // 7. Create HemoScreenStandaloneResult record
            $result = HemoScreenStandaloneResult::create([
                'user_id' => $practitioner->user_id,
                'practitioner_id' => $practitioner->id,
                'scb_id' => $clientId,
                'device_serial' => $validated['device_serial'] ?? null,
                'patient_identifier' => $validated['patient_identifier'] ?? null,
                'message_control_id' => $validated['control_id'],
                'message_type' => $validated['message_type'],
                'panel_code' => '85025', // Default CBC code
                'panel_name' => 'Complete Blood Count',
                'observations' => $validated['observations'],
                'raw_message' => $request->all(),
                'test_performed_at' => now(),
            ]);

            Log::info('HemoScreen standalone result stored', [
                'result_id' => $result->id,
                'practitioner_id' => $practitioner->id,
                'device_serial' => $validated['device_serial'],
                'observations_count' => count($validated['observations']),
            ]);

            // 8. Send email notification
            try {
                $practitioner->user->notify(new HemoScreenResultReceivedNotification($result));
            } catch (\Exception $e) {
                Log::error('Failed to send HemoScreen result notification', [
                    'result_id' => $result->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the request if notification fails
            }

            // 9. Return success response
            return response()->json([
                'success' => true,
                'message' => 'Results stored successfully',
                'result_id' => $result->id,
                'observations_count' => count($validated['observations']),
                'test_performed_at' => $result->test_performed_at->toIso8601String(),
            ], 201);
        });
    }
}
