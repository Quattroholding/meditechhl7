<?php

namespace App\Http\Controllers\Api\Recepy;

use App\Http\Controllers\Controller;
use App\Models\Recepy\RecepyDoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class RecepyDoctorProfileController extends Controller
{
    public function index(): JsonResponse
    {
        $profiles = RecepyDoctorProfile::with('user')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $profiles
        ]);
    }

    public function show($id): JsonResponse
    {
        $profile = RecepyDoctorProfile::with(['user', 'prescriptions'])
            ->find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de doctor no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:recepy_doctor_profiles,user_id',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'speciality' => 'nullable|string|max:100',
            'facility' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'medical_license_number' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'seal' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('recepy/logos', 'public');
        }

        if ($request->hasFile('signature')) {
            $data['signature'] = $request->file('signature')->store('recepy/signatures', 'public');
        }

        if ($request->hasFile('seal')) {
            $data['seal'] = $request->file('seal')->store('recepy/seals', 'public');
        }

        $profile = RecepyDoctorProfile::create($data);
        $profile->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Perfil de doctor creado exitosamente',
            'data' => $profile
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $profile = RecepyDoctorProfile::find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de doctor no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'medical_license_number' => 'nullable|string',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'seal' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Handle file uploads and delete old files
        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $data['logo'] = $request->file('logo')->store('recepy/logos', 'public');
        }

        if ($request->hasFile('signature')) {
            if ($profile->signature) {
                Storage::disk('public')->delete($profile->signature);
            }
            $data['signature'] = $request->file('signature')->store('recepy/signatures', 'public');
        }

        if ($request->hasFile('seal')) {
            if ($profile->seal) {
                Storage::disk('public')->delete($profile->seal);
            }
            $data['seal'] = $request->file('seal')->store('recepy/seals', 'public');
        }

        $profile->update($data);
        $profile->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Perfil de doctor actualizado exitosamente',
            'data' => $profile
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $profile = RecepyDoctorProfile::find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de doctor no encontrado'
            ], 404);
        }

        // Delete associated files
        if ($profile->logo) {
            Storage::disk('public')->delete($profile->logo);
        }
        if ($profile->signature) {
            Storage::disk('public')->delete($profile->signature);
        }
        if ($profile->seal) {
            Storage::disk('public')->delete($profile->seal);
        }

        $profile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Perfil de doctor eliminado exitosamente'
        ]);
    }

    public function getByUser($userId): JsonResponse
    {
        $profile = RecepyDoctorProfile::with('user')
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de doctor no encontrado para este usuario'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }
}
