<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Practitioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PractitionerAuthorizationController extends Controller
{
    /**
     * Update prescription authorization for authenticated practitioner
     */
    public function updatePrescriptionAuthorization(Request $request)
    {
        $validated = $request->validate([
            'prescription_authorization' => 'required|boolean',
            'prescription_authorization_terms' => 'required_if:prescription_authorization,true|string',
        ]);

        $user = Auth::user();

        // Verificar que el usuario tenga un practitioner asociado
        $practitioner = Practitioner::where('user_id', $user->id)->first();

        if (! $practitioner) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un médico asociado a este usuario.',
            ], 404);
        }

        // Solo permitir activar la autorización, no desactivarla por API
        if ($validated['prescription_authorization'] === true) {
            $practitioner->update([
                'prescription_authorization' => true,
                'prescription_authorization_date' => now(),
                'prescription_authorization_ip' => $request->ip(),
                'prescription_authorization_terms' => $validated['prescription_authorization_terms'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Autorización de prescripción actualizada exitosamente.',
                'data' => [
                    'prescription_authorization' => $practitioner->prescription_authorization,
                    'prescription_authorization_date' => $practitioner->prescription_authorization_date,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se puede desactivar la autorización desde esta API.',
        ], 400);
    }

    /**
     * Get current prescription authorization status
     */
    public function getPrescriptionAuthorizationStatus()
    {
        $user = Auth::user();

        $practitioner = Practitioner::where('user_id', $user->id)->first();

        if (! $practitioner) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un médico asociado a este usuario.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'prescription_authorization' => $practitioner->prescription_authorization,
                'prescription_authorization_date' => $practitioner->prescription_authorization_date,
                'has_signature' => $practitioner->signature() !== null,
                'has_seal' => $practitioner->seal() !== null,
            ],
        ]);
    }
}
