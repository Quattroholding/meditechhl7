<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMedicationRequestRequest;
use App\Http\Resources\Api\MedicationRequestResource;
use App\Models\MedicationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MedicationRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = MedicationRequest::with(['patient', 'practitioner', 'medicine', 'encounter']);

        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->has('practitioner_id')) {
            $query->where('practitioner_id', $request->practitioner_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('encounter_id')) {
            $query->where('encounter_id', $request->encounter_id);
        }

        $medicationRequests = $query->latest()->paginate(15);

        return MedicationRequestResource::collection($medicationRequests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicationRequestRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $medicationRequest = MedicationRequest::create($validatedData);

        $medicationRequest->load(['patient', 'practitioner', 'medicine', 'encounter']);

        return response()->json([
            'success' => true,
            'message' => 'Medication request created successfully.',
            'data' => new MedicationRequestResource($medicationRequest),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicationRequest $medicationRequest): JsonResponse
    {
        $medicationRequest->load(['patient', 'practitioner', 'medicine', 'encounter']);

        return response()->json([
            'success' => true,
            'data' => new MedicationRequestResource($medicationRequest),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMedicationRequestRequest $request, MedicationRequest $medicationRequest): JsonResponse
    {
        $validatedData = $request->validated();

        $medicationRequest->update($validatedData);

        $medicationRequest->load(['patient', 'practitioner', 'medicine', 'encounter']);

        return response()->json([
            'success' => true,
            'message' => 'Medication request updated successfully.',
            'data' => new MedicationRequestResource($medicationRequest),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicationRequest $medicationRequest): JsonResponse
    {
        $medicationRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Medication request deleted successfully.',
        ]);
    }
}
