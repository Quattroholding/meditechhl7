<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMedicineRequest;
use App\Http\Requests\Api\UpdateMedicineRequest;
use App\Http\Resources\Api\MedicineResource;
use App\Models\Medicine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Medicine::with(['client', 'user', 'components']);

        // Filter by client_id (required for client-specific medicines)
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Additional filters
        if ($request->has('generic_name')) {
            $query->where('generic_name', 'like', '%'.$request->generic_name.'%');
        }

        if ($request->has('type')) {
            $query->where('type', 'like', '%'.$request->type.'%');
        }

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->has('narcotic')) {
            $query->where('narcotic', $request->boolean('narcotic'));
        }

        if ($request->has('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        // Search across multiple fields
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('generic_name', 'like', '%'.$search.'%')
                    ->orWhere('home_name', 'like', '%'.$search.'%')
                    ->orWhere('ndc_code', 'like', '%'.$search.'%')
                    ->orWhere('type', 'like', '%'.$search.'%');
            });
        }

        $medicines = $query->latest()->paginate($request->input('per_page', 15));

        return MedicineResource::collection($medicines);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicineRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        // Set user_id from authenticated user
        $validatedData['user_id'] = auth()->id();

        // Set default values
        $validatedData['active'] = $validatedData['active'] ?? true;
        $validatedData['narcotic'] = $validatedData['narcotic'] ?? false;
        $validatedData['source'] = 'api';

        $medicine = Medicine::create($validatedData);

        $medicine->load(['client', 'user', 'components']);

        return response()->json([
            'success' => true,
            'message' => 'Medicine created successfully.',
            'data' => new MedicineResource($medicine),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Medicine $medicine): JsonResponse
    {
        // Optional client filtering for security
        if ($request->has('client_id') && $medicine->client_id != $request->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found or access denied.',
            ], 404);
        }

        $medicine->load(['client', 'user', 'components']);

        return response()->json([
            'success' => true,
            'data' => new MedicineResource($medicine),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicineRequest $request, Medicine $medicine): JsonResponse
    {
        $validatedData = $request->validated();

        // Optional client filtering for security
        if ($request->has('client_id') && $medicine->client_id != $request->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found or access denied.',
            ], 404);
        }

        $medicine->update($validatedData);

        $medicine->load(['client', 'user', 'components']);

        return response()->json([
            'success' => true,
            'message' => 'Medicine updated successfully.',
            'data' => new MedicineResource($medicine),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Medicine $medicine): JsonResponse
    {
        // Optional client filtering for security
        if ($request->has('client_id') && $medicine->client_id != $request->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found or access denied.',
            ], 404);
        }

        // Check if medicine is being used in medication requests
        if ($medicine->medicationRequests()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete medicine. It is being used in medication requests.',
            ], 422);
        }

        $medicine->delete();

        return response()->json([
            'success' => true,
            'message' => 'Medicine deleted successfully.',
        ]);
    }

    /**
     * Get medicines by client
     */
    public function getByClient(Request $request, int $clientId): AnonymousResourceCollection
    {
        $query = Medicine::with(['client', 'user', 'components'])
            ->where('client_id', $clientId)
            ->where('active', true);

        // Additional filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('generic_name', 'like', '%'.$search.'%')
                    ->orWhere('home_name', 'like', '%'.$search.'%')
                    ->orWhere('ndc_code', 'like', '%'.$search.'%')
                    ->orWhere('type', 'like', '%'.$search.'%');
            });
        }

        $medicines = $query->latest()->paginate($request->input('per_page', 15));

        return MedicineResource::collection($medicines);
    }
}
