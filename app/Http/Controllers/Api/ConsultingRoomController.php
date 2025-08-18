<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConsultingRoom;
use Illuminate\Http\Request;

class ConsultingRoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = ConsultingRoom::when($request->search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })
            ->when($request->branch_id, function ($query, $branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->with(['branch'])
            ->orderBy('name')
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'description' => $room->description ?? '',
                    'branch' => [
                        'id' => $room->branch->id ?? null,
                        'name' => $room->branch->name ?? 'N/A',
                    ],
                ];
            });

        return response()->json([
            'consulting_rooms' => $rooms,
            'total' => $rooms->count(),
        ]);
    }
}
