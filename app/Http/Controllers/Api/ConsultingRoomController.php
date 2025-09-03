<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreConsultingRoomRequest;
use App\Http\Requests\Api\UpdateConsultingRoomRequest;
use App\Http\Resources\Api\ConsultingRoomResource;
use App\Models\ConsultingRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConsultingRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ConsultingRoom::with(['branch.client']);

        // Filter by branch_id
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by client_id through branch relationship
        if ($request->has('client_id')) {
            $query->whereHas('branch', function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            });
        }

        // Additional filters
        if ($request->has('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->has('floor')) {
            $query->where('floor', $request->floor);
        }

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        // Search across multiple fields
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('number', 'like', '%'.$search.'%')
                    ->orWhere('floor', 'like', '%'.$search.'%');
            });
        }

        $consultingRooms = $query->latest()->paginate($request->input('per_page', 15));

        return ConsultingRoomResource::collection($consultingRooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConsultingRoomRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        // Set default values
        $validatedData['active'] = $validatedData['active'] ?? true;

        $consultingRoom = ConsultingRoom::create($validatedData);

        $consultingRoom->load(['branch.client']);

        return response()->json([
            'success' => true,
            'message' => 'Consulting room created successfully.',
            'data' => new ConsultingRoomResource($consultingRoom),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ConsultingRoom $consultingRoom): JsonResponse
    {
        // Optional client filtering for security through branch
        if ($request->has('client_id') && $consultingRoom->branch->client_id != $request->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Consulting room not found or access denied.',
            ], 404);
        }

        $consultingRoom->load(['branch.client']);

        return response()->json([
            'success' => true,
            'data' => new ConsultingRoomResource($consultingRoom),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConsultingRoomRequest $request, ConsultingRoom $consultingRoom): JsonResponse
    {
        $validatedData = $request->validated();

        // Optional client filtering for security through branch
        if ($request->has('client_id') && $consultingRoom->branch->client_id != $request->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Consulting room not found or access denied.',
            ], 404);
        }

        $consultingRoom->update($validatedData);

        $consultingRoom->load(['branch.client']);

        return response()->json([
            'success' => true,
            'message' => 'Consulting room updated successfully.',
            'data' => new ConsultingRoomResource($consultingRoom),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ConsultingRoom $consultingRoom): JsonResponse
    {
        // Optional client filtering for security through branch
        if ($request->has('client_id') && $consultingRoom->branch->client_id != $request->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Consulting room not found or access denied.',
            ], 404);
        }

        $consultingRoom->delete();

        return response()->json([
            'success' => true,
            'message' => 'Consulting room deleted successfully.',
        ]);
    }

    /**
     * Get consulting rooms by branch
     */
    public function getByBranch(Request $request, int $branchId): AnonymousResourceCollection
    {
        $query = ConsultingRoom::with(['branch.client'])
            ->where('branch_id', $branchId)
            ->where('active', true);

        // Additional filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('number', 'like', '%'.$search.'%')
                    ->orWhere('floor', 'like', '%'.$search.'%');
            });
        }

        $consultingRooms = $query->latest()->paginate($request->input('per_page', 15));

        return ConsultingRoomResource::collection($consultingRooms);
    }

    /**
     * Get consulting rooms by client
     */
    public function getByClient(Request $request, int $clientId): AnonymousResourceCollection
    {
        $query = ConsultingRoom::with(['branch.client'])
            ->whereHas('branch', function ($q) use ($clientId) {
                $q->where('client_id', $clientId);
            })
            ->where('active', true);

        // Additional filters
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('number', 'like', '%'.$search.'%')
                    ->orWhere('floor', 'like', '%'.$search.'%');
            });
        }

        $consultingRooms = $query->latest()->paginate($request->input('per_page', 15));

        return ConsultingRoomResource::collection($consultingRooms);
    }
}
