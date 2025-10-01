<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Verificar que el usuario tenga un practitioner asociado (sin scope)
        $practitioner = DB::table('practitioners')->where('user_id', auth()->id())->first();

        if (! $practitioner) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un médico asociado a este usuario.',
            ], 404);
        }

        // Solo permitir activar la autorización, no desactivarla por API
        if ($validated['prescription_authorization'] === true) {
            DB::table('practitioners')
                ->where('id', $practitioner->id)
                ->update([
                    'prescription_authorization' => true,
                    'prescription_authorization_date' => now(),
                    'prescription_authorization_ip' => $request->ip(),
                    'prescription_authorization_terms' => $validated['prescription_authorization_terms'],
                    'updated_at' => now(),
                ]);

            // Recargar el practitioner para obtener los datos actualizados
            $practitioner = DB::table('practitioners')->where('id', $practitioner->id)->first();

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
        // Buscar practitioner sin scope
        $practitioner = DB::table('practitioners')->where('user_id', auth()->id())->first();

        if (! $practitioner) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un médico asociado a este usuario.',
            ], 404);
        }

        // Verificar firma y sello desde la tabla files
        $hasSignature = DB::table('files')
            ->where('fileable_type', 'App\Models\Practitioner')
            ->where('fileable_id', $practitioner->id)
            ->where('type', 'signature')
            ->exists();

        $hasSeal = DB::table('files')
            ->where('fileable_type', 'App\Models\Practitioner')
            ->where('fileable_id', $practitioner->id)
            ->where('type', 'seal')
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'prescription_authorization' => (bool) $practitioner->prescription_authorization,
                'prescription_authorization_date' => $practitioner->prescription_authorization_date,
                'has_signature' => $hasSignature,
                'has_seal' => $hasSeal,
            ],
        ]);
    }
}
