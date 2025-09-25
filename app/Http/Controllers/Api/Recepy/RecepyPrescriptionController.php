<?php

namespace App\Http\Controllers\Api\Recepy;

use App\Http\Controllers\Controller;
use App\Models\Recepy\RecepyPrescription;
use App\Models\Recepy\RecepyDoctorProfile;
use App\Services\PrescriptionPdfService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RecepyPrescriptionController extends Controller
{
    /**
     * Verificar si una prescripción pertenece al usuario autenticado
     */
    private function prescriptionBelongsToUser($prescription): bool
    {
        if (!$prescription) {
            return false;
        }

        return RecepyDoctorProfile::where('id', $prescription->doctor_profile_id)
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->exists();
    }
    public function index(Request $request): JsonResponse
    {
        // Solo mostrar prescripciones de los perfiles del doctor autenticado
        $userDoctorProfileIds = RecepyDoctorProfile::where('user_id', auth()->id())
            ->where('is_active', true)
            ->pluck('id');

        if ($userDoctorProfileIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'data' => [],
                    'current_page' => 1,
                    'total' => 0
                ]
            ]);
        }

        $query = RecepyPrescription::with(['doctorProfile.user', 'medications'])
            ->whereIn('doctor_profile_id', $userDoctorProfileIds);

        // Filter by doctor profile if provided (but still within user's profiles)
        if ($request->has('doctor_profile_id')) {
            $requestedProfileId = $request->doctor_profile_id;
            // Verificar que el profile solicitado pertenece al usuario
            if ($userDoctorProfileIds->contains($requestedProfileId)) {
                $query->where('doctor_profile_id', $requestedProfileId);
            } else {
                // Si solicita un perfil que no le pertenece, devolver vacío
                return response()->json([
                    'success' => true,
                    'data' => [
                        'data' => [],
                        'current_page' => 1,
                        'total' => 0
                    ]
                ]);
            }
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range if provided
        if ($request->has('date_from')) {
            $query->where('prescription_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('prescription_date', '<=', $request->date_to);
        }

        // Search by patient name or prescription number
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('prescription_number', 'like', "%{$search}%");
            });
        }

        $prescriptions = $query->orderBy('prescription_date', 'desc')
                              ->paginate(5);

        return response()->json([
            'success' => true,
            'data' => $prescriptions
        ]);
    }

    public function show($id): JsonResponse
    {
        $prescription = RecepyPrescription::with([
            'doctorProfile.user',
            'activeMedications'
        ])->find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Verificar que la prescripción pertenece al usuario autenticado
        if (!$this->prescriptionBelongsToUser($prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $prescription
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'doctor_profile_id' => 'required|exists:recepy_doctor_profiles,id',
            'patient_name' => 'required|string|max:255',
            'patient_document' => 'nullable|string|max:50',
            'patient_birth_date' => 'nullable|date',
            'patient_gender' => 'nullable|in:M,F,O',
            'patient_address' => 'nullable|string',
            'patient_phone' => 'nullable|string|max:20',
            'diagnosis' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'prescription_date' => 'required|date',
            'prescription_number' => 'nullable|string|unique:recepy_prescriptions,prescription_number',
            'medications' => 'required|array|min:1',
            'medications.*.medication_name' => 'required|string',
            'medications.*.presentation' => 'nullable|string',
            'medications.*.concentration' => 'nullable|string',
            'medications.*.dosage' => 'required|string',
            'medications.*.frequency' => 'required|string',
            'medications.*.duration' => 'nullable|string',
            'medications.*.instructions' => 'required|string',
            'medications.*.quantity' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que el doctor_profile_id pertenece al usuario autenticado
        $doctorProfile = RecepyDoctorProfile::where('id', $request->doctor_profile_id)
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (!$doctorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de doctor no válido'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $prescriptionData = $validator->validated();
            $medications = $prescriptionData['medications'];
            unset($prescriptionData['medications']);

            // Create prescription
            $prescription = RecepyPrescription::create($prescriptionData);

            // Create medications
            foreach ($medications as $index => $medicationData) {
                $medicationData['prescription_id'] = $prescription->id;
                $medicationData['line_order'] = $index + 1;
                $prescription->medications()->create($medicationData);
            }

            DB::commit();

            $prescription->load(['doctorProfile.user', 'activeMedications']);

            return response()->json([
                'success' => true,
                'message' => 'Receta creada exitosamente',
                'data' => $prescription
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la receta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $prescription = RecepyPrescription::find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Verificar que la prescripción pertenece al usuario autenticado
        if (!$this->prescriptionBelongsToUser($prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'patient_name' => 'sometimes|required|string|max:255',
            'patient_document' => 'nullable|string|max:50',
            'patient_birth_date' => 'nullable|date',
            'patient_gender' => 'nullable|in:M,F,O',
            'patient_address' => 'nullable|string',
            'patient_phone' => 'nullable|string|max:20',
            'diagnosis' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'prescription_date' => 'sometimes|required|date',
            'status' => 'sometimes|in:active,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $prescription->update($validator->validated());
        $prescription->load(['doctorProfile.user', 'activeMedications']);

        return response()->json([
            'success' => true,
            'message' => 'Receta actualizada exitosamente',
            'data' => $prescription
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $prescription = RecepyPrescription::find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Verificar que la prescripción pertenece al usuario autenticado
        if (!$this->prescriptionBelongsToUser($prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        $prescription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Receta eliminada exitosamente'
        ]);
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $prescription = RecepyPrescription::find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Verificar que la prescripción pertenece al usuario autenticado
        if (!$this->prescriptionBelongsToUser($prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Estado inválido'
            ], 422);
        }

        $prescription->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de receta actualizado exitosamente',
            'data' => $prescription
        ]);
    }

    public function getByDoctorProfile($doctorProfileId): JsonResponse
    {
        // Verificar que el doctor_profile_id pertenece al usuario autenticado
        $doctorProfile = RecepyDoctorProfile::where('id', $doctorProfileId)
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (!$doctorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de doctor no encontrado'
            ], 404);
        }

        $prescriptions = RecepyPrescription::with(['medications'])
            ->where('doctor_profile_id', $doctorProfileId)
            ->orderBy('prescription_date', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $prescriptions
        ]);
    }

    public function downloadPdf($id, PrescriptionPdfService $pdfService)
    {
        $prescription = RecepyPrescription::find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Verificar que la prescripción pertenece al usuario autenticado
        if (!$this->prescriptionBelongsToUser($prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        try {

            if(request()->has('view')) return view('pdf.prescription', [
                'prescription' => $prescription
            ]);

            return $pdfService->unstreamPdf($prescription);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function streamPdf($id, PrescriptionPdfService $pdfService)
    {
        $prescription = RecepyPrescription::find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Verificar que la prescripción pertenece al usuario autenticado
        if (!$this->prescriptionBelongsToUser($prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        try {
            return $pdfService->streamPdf($prescription);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generatePdfUrl($id, PrescriptionPdfService $pdfService): JsonResponse
    {
        $prescription = RecepyPrescription::find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Verificar que la prescripción pertenece al usuario autenticado
        if (!$this->prescriptionBelongsToUser($prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        try {
            // Save PDF to storage and get path
            $pdfPath = $pdfService->savePdf($prescription);

            // Generate URL for the saved PDF
            $pdfUrl = asset('storage/' . $pdfPath);

            return response()->json([
                'success' => true,
                'data' => [
                    'pdf_url' => $pdfUrl,
                    'pdf_path' => $pdfPath,
                    'filename' => basename($pdfPath)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    // PDF methods with doctor profile ID parameter
    public function downloadPdfWithProfile($id, Request $request, PrescriptionPdfService $pdfService)
    {
        $prescription = RecepyPrescription::find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Verificar que la prescripción pertenece al usuario autenticado
        if (!$this->prescriptionBelongsToUser($prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Validate doctor profile ID parameter
        $validator = Validator::make($request->all(), [
            'doctor_profile_id' => 'required|integer|exists:recepy_doctor_profiles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que el doctor_profile_id pertenece al usuario autenticado
        $doctorProfile = RecepyDoctorProfile::where('id', $request->doctor_profile_id)
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (!$doctorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de doctor no válido'
            ], 403);
        }

        try {
            return $pdfService->downloadPdfWithProfile($prescription, $request->doctor_profile_id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function streamPdfWithProfile($id, Request $request, PrescriptionPdfService $pdfService)
    {
        $prescription = RecepyPrescription::find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Verificar que la prescripción pertenece al usuario autenticado
        if (!$this->prescriptionBelongsToUser($prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Validate doctor profile ID parameter
        $validator = Validator::make($request->all(), [
            'doctor_profile_id' => 'required|integer|exists:recepy_doctor_profiles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que el doctor_profile_id pertenece al usuario autenticado
        $doctorProfile = RecepyDoctorProfile::where('id', $request->doctor_profile_id)
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (!$doctorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de doctor no válido'
            ], 403);
        }

        try {
            return $pdfService->streamPdfWithProfile($prescription, $request->doctor_profile_id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generatePdfUrlWithProfile($id, Request $request, PrescriptionPdfService $pdfService): JsonResponse
    {
        $prescription = RecepyPrescription::find($id);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Verificar que la prescripción pertenece al usuario autenticado
        if (!$this->prescriptionBelongsToUser($prescription)) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        // Validate doctor profile ID parameter
        $validator = Validator::make($request->all(), [
            'doctor_profile_id' => 'required|integer|exists:recepy_doctor_profiles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar que el doctor_profile_id pertenece al usuario autenticado
        $doctorProfile = RecepyDoctorProfile::where('id', $request->doctor_profile_id)
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        if (!$doctorProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de doctor no válido'
            ], 403);
        }

        try {
            // Save PDF to storage and get path
            $pdfPath = $pdfService->savePdfWithProfile($prescription, $request->doctor_profile_id);

            // Generate URL for the saved PDF
            $pdfUrl = asset('storage/' . $pdfPath);

            return response()->json([
                'success' => true,
                'data' => [
                    'pdf_url' => $pdfUrl,
                    'pdf_path' => $pdfPath,
                    'filename' => basename($pdfPath)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
