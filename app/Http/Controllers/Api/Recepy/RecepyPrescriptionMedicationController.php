<?php

namespace App\Http\Controllers\Api\Recepy;

use App\Http\Controllers\Controller;
use App\Models\Recepy\RecepyPrescriptionMedication;
use App\Models\Recepy\RecepyPrescription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RecepyPrescriptionMedicationController extends Controller
{
    public function index($prescriptionId): JsonResponse
    {
        $prescription = RecepyPrescription::find($prescriptionId);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        $medications = $prescription->medications()
            ->orderBy('line_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $medications
        ]);
    }

    public function show($prescriptionId, $medicationId): JsonResponse
    {
        $medication = RecepyPrescriptionMedication::where('prescription_id', $prescriptionId)
            ->where('id', $medicationId)
            ->with('prescription')
            ->first();

        if (!$medication) {
            return response()->json([
                'success' => false,
                'message' => 'Medicamento no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $medication
        ]);
    }

    public function store(Request $request, $prescriptionId): JsonResponse
    {
        $prescription = RecepyPrescription::find($prescriptionId);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'medication_name' => 'required|string|max:255',
            'presentation' => 'nullable|string|max:255',
            'concentration' => 'nullable|string|max:255',
            'dosage' => 'required|string|max:255',
            'frequency' => 'required|string|max:255',
            'duration' => 'nullable|string|max:255',
            'instructions' => 'required|string',
            'quantity' => 'nullable|integer|min:1',
            'line_order' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['prescription_id'] = $prescriptionId;

        // If no line_order provided, set it as the next available
        if (!isset($data['line_order'])) {
            $lastOrder = $prescription->medications()->max('line_order') ?? 0;
            $data['line_order'] = $lastOrder + 1;
        }

        $medication = RecepyPrescriptionMedication::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Medicamento agregado exitosamente',
            'data' => $medication
        ], 201);
    }

    public function update(Request $request, $prescriptionId, $medicationId): JsonResponse
    {
        $medication = RecepyPrescriptionMedication::where('prescription_id', $prescriptionId)
            ->where('id', $medicationId)
            ->first();

        if (!$medication) {
            return response()->json([
                'success' => false,
                'message' => 'Medicamento no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'medication_name' => 'sometimes|required|string|max:255',
            'presentation' => 'nullable|string|max:255',
            'concentration' => 'nullable|string|max:255',
            'dosage' => 'sometimes|required|string|max:255',
            'frequency' => 'sometimes|required|string|max:255',
            'duration' => 'nullable|string|max:255',
            'instructions' => 'sometimes|required|string',
            'quantity' => 'nullable|integer|min:1',
            'line_order' => 'sometimes|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $medication->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Medicamento actualizado exitosamente',
            'data' => $medication
        ]);
    }

    public function destroy($prescriptionId, $medicationId): JsonResponse
    {
        $medication = RecepyPrescriptionMedication::where('prescription_id', $prescriptionId)
            ->where('id', $medicationId)
            ->first();

        if (!$medication) {
            return response()->json([
                'success' => false,
                'message' => 'Medicamento no encontrado'
            ], 404);
        }

        $medication->delete();

        return response()->json([
            'success' => true,
            'message' => 'Medicamento eliminado exitosamente'
        ]);
    }

    public function updateOrder(Request $request, $prescriptionId): JsonResponse
    {
        $prescription = RecepyPrescription::find($prescriptionId);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'medications' => 'required|array',
            'medications.*.id' => 'required|exists:recepy_prescription_medications,id',
            'medications.*.line_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->medications as $medicationData) {
            RecepyPrescriptionMedication::where('id', $medicationData['id'])
                ->where('prescription_id', $prescriptionId)
                ->update(['line_order' => $medicationData['line_order']]);
        }

        $updatedMedications = $prescription->medications()
            ->orderBy('line_order')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Orden de medicamentos actualizado exitosamente',
            'data' => $updatedMedications
        ]);
    }

    public function toggleActive(Request $request, $prescriptionId, $medicationId): JsonResponse
    {
        $medication = RecepyPrescriptionMedication::where('prescription_id', $prescriptionId)
            ->where('id', $medicationId)
            ->first();

        if (!$medication) {
            return response()->json([
                'success' => false,
                'message' => 'Medicamento no encontrado'
            ], 404);
        }

        $medication->update(['is_active' => !$medication->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del medicamento actualizado exitosamente',
            'data' => $medication
        ]);
    }

    public function bulkUpdate(Request $request, $prescriptionId): JsonResponse
    {
        $prescription = RecepyPrescription::find($prescriptionId);

        if (!$prescription) {
            return response()->json([
                'success' => false,
                'message' => 'Receta no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'medications' => 'required|array',
            'medications.*.id' => 'sometimes|exists:recepy_prescription_medications,id',
            'medications.*.medication_name' => 'required|string|max:255',
            'medications.*.presentation' => 'nullable|string|max:255',
            'medications.*.concentration' => 'nullable|string|max:255',
            'medications.*.dosage' => 'required|string|max:255',
            'medications.*.frequency' => 'required|string|max:255',
            'medications.*.duration' => 'nullable|string|max:255',
            'medications.*.instructions' => 'required|string',
            'medications.*.quantity' => 'nullable|integer|min:1',
            'medications.*.line_order' => 'required|integer|min:1',
            'medications.*.is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete existing medications not in the update
        $existingIds = array_filter(array_column($request->medications, 'id'));
        if (!empty($existingIds)) {
            $prescription->medications()
                ->whereNotIn('id', $existingIds)
                ->delete();
        } else {
            $prescription->medications()->delete();
        }

        // Update or create medications
        foreach ($request->medications as $medicationData) {
            $medicationData['prescription_id'] = $prescriptionId;
            
            if (isset($medicationData['id'])) {
                RecepyPrescriptionMedication::updateOrCreate(
                    ['id' => $medicationData['id'], 'prescription_id' => $prescriptionId],
                    $medicationData
                );
            } else {
                RecepyPrescriptionMedication::create($medicationData);
            }
        }

        $updatedMedications = $prescription->medications()
            ->orderBy('line_order')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Medicamentos actualizados exitosamente',
            'data' => $updatedMedications
        ]);
    }
}