<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreBranchRequest;
use App\Http\Requests\Api\UpdateBranchRequest;
use App\Http\Resources\Api\BranchResource;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Branch::with(['client', 'consultingRooms']);
        
        // Filter by client_id (required for client-specific branches)
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        
        // Additional filters
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        if ($request->has('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }
        
        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        // Search across multiple fields
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%');
            });
        }

        $branches = $query->withCount('consultingRooms')->latest()->paginate($request->input('per_page', 15));
        
        return BranchResource::collection($branches);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBranchRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        
        // Set default values
        $validatedData['active'] = $validatedData['active'] ?? true;
        
        $branch = Branch::create($validatedData);
        
        $branch->load(['client', 'consultingRooms']);
        
        return response()->json([
            'success' => true,
            'message' => 'Branch created successfully.',
            'data' => new BranchResource($branch)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Branch $branch): JsonResponse
    {
        // Optional client filtering for security
        if ($request->has('client_id') && $branch->client_id != $request->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found or access denied.'
            ], 404);
        }
        
        $branch->load(['client', 'consultingRooms']);
        
        return response()->json([
            'success' => true,
            'data' => new BranchResource($branch)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBranchRequest $request, Branch $branch): JsonResponse
    {
        $validatedData = $request->validated();
        
        // Optional client filtering for security
        if ($request->has('client_id') && $branch->client_id != $request->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found or access denied.'
            ], 404);
        }
        
        $branch->update($validatedData);
        
        $branch->load(['client', 'consultingRooms']);
        
        return response()->json([
            'success' => true,
            'message' => 'Branch updated successfully.',
            'data' => new BranchResource($branch)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Branch $branch): JsonResponse
    {
        // Optional client filtering for security
        if ($request->has('client_id') && $branch->client_id != $request->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found or access denied.'
            ], 404);
        }
        
        // Check if branch has consulting rooms
        if ($branch->consultingRooms()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete branch. It has consulting rooms associated.'
            ], 422);
        }
        
        $branch->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Branch deleted successfully.'
        ]);
    }

    /**
     * Get branches by client
     */
    public function getByClient(Request $request, int $clientId): AnonymousResourceCollection
    {
        $query = Branch::with(['client', 'consultingRooms'])
            ->where('client_id', $clientId)
            ->where('active', true);
        
        // Additional filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%');
            });
        }

        $branches = $query->withCount('consultingRooms')->latest()->paginate($request->input('per_page', 15));
        
        return BranchResource::collection($branches);
    }
}
