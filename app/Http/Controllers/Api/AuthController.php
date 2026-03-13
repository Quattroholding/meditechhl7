<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPractitionerRequest;
use App\Models\File;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\Recepy\RecepyDoctorProfile;
use App\Models\Recepy\RecepyPrescription;
use App\Models\Recepy\RecepyPrescriptionMedication;
use App\Models\User;
use App\Notifications\NewUserRegistrationNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password) || ! empty($user->inactive_at)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Verificar si el usuario está activo
        if ($user->validation_status=='pending') {
            throw ValidationException::withMessages([
                'email' => ['Su cuenta está pendiente de validación. Por favor espere a que nuestro equipo revise y apruebe sus documentos. Recibirá un correo electrónico cuando su cuenta sea activada.'],
            ]);
        }

        if ($user->validation_status=='rejected') {
            throw ValidationException::withMessages([
                'email' => ['Su solicitud ha sido rechazada , si cree que esto es un error , ponerse en contacto al correo business@meditecpty.com para mayor información.'],
            ]);
        }

        // Detectar el tipo de usuario y obtener información específica
        $userType = $this->getUserType($user);

        if (! $userType) {
            throw ValidationException::withMessages([
                'email' => ['Usuario sin rol válido para acceso móvil.'],
            ]);
        }

        // Obtener datos específicos según el tipo de usuario
        $specificData = $this->getUserSpecificData($user, $userType);

        if (! $specificData) {
            throw ValidationException::withMessages([
                'email' => ['No se encontró información completa del usuario.'],
            ]);
        }

        $tokenName = $userType === 'patient' ? 'patient-app' : 'practitioner-app';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'user_type' => $userType,
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'default_client_id' => $user->default_client_id,
            ],
            $userType => $specificData,
        ]);
    }

    public function register(Request $request)
    {
        // Validar el tipo de usuario
        $request->validate([
            'user_type' => 'nullable|in:patient,practitioner',
        ]);

        $userType = $request->user_type ?? 'patient';

        // Si es practitioner, usar el FormRequest con validación de archivos
        if ($userType === 'practitioner') {
            return $this->registerPractitioner(RegisterPractitionerRequest::createFrom($request));
        }

        // Registro de paciente (comportamiento original sin cambios)
        $request->validate([
            'email' => 'required|string|email|max:255',
            'identifier' => ['required', 'regex:'.$this->getIdPattern($request->identifier_type)],
            'identifier_type' => 'required',
            'given_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,unknown',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            $user = User::create([
                'first_name' => $request->given_name,
                'last_name' => $request->family_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'register_source' => 'app',
            ]);
        } else {
            $user->first_name = $request->given_name;
            $user->last_name = $request->family_name;
            $user->password = Hash::make($request->password);
            $user->save();
        }

        $user->assignRole('paciente');

        $patient = Patient::where('identifier', 'LIKE', $request->identifier.'%')
            ->orWhere('email', $request->email)
            ->first();

        if (! $patient) {
            $patient = Patient::create([
                'user_id' => $user->id,
                'identifier' => $request->identifier,
                'identifier_type' => $request->identifier_type,
                'given_name' => $request->given_name,
                'family_name' => $request->family_name,
                'name' => $request->given_name.' '.$request->family_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'whatsapp_phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'fhir_id' => 'patient-'.Str::uuid(),
                'communication' => json_encode(['language' => 'es', 'preferred' => true]),
                'address' => $request->address,
            ]);
        } else {
            $patient->update([
                'user_id' => $user->id,
                'given_name' => $request->given_name,
                'family_name' => $request->family_name,
                'name' => $request->given_name.' '.$request->family_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'whatsapp_phone' => $request->phone,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'marital_status' => $request->marital_status,
                'identifier_type' => $request->identifier_type,
                'identifier' => strtoupper($request->identifier),
            ]);
        }

        $token = $user->createToken('patient-app')->plainTextToken;

        return response()->json([
            'message' => 'Registro de paciente exitoso',
            'token' => $token,
            'user_type' => 'patient',
            'user' => [
                'id' => $user->id,
                'name' => $user->first_name.' '.$user->last_name,
                'email' => $user->email,
            ],
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'phone' => $patient->phone,
                'birth_date' => $patient->birth_date,
            ],
        ], 201);
    }

    /**
     * Register a new practitioner with document validation
     */
    private function registerPractitioner(RegisterPractitionerRequest $request)
    {
        DB::beginTransaction();

        try {
            // Verificar si existe un usuario desactivado con este email
            $existingUser = User::where('email', $request->email)
                ->whereNotNull('inactive_at')
                ->first();

            if ($existingUser) {
                // Reactivar usuario existente (como nuevo registro, sin datos anteriores)
                $user = $this->reactivateDeactivatedPractitioner($existingUser, $request);
            } else {
                // Crear nuevo usuario
                $user = $this->createNewPractitioner($request);
            }

            // Guardar archivos de documentación
            $this->storeVerificationDocuments($request, $user);

            // Commit de la transacción si todo salió bien
            DB::commit();

            // Notificar a administradores y validadores (fuera de la transacción)
            try {
                $this->notifyAdminsAndValidators($user);
            } catch (\Exception $notificationError) {
                Log::error('Error al enviar notificaciones de registro: '.$notificationError->getMessage());
            }

            return response()->json([
                'message' => 'Registro recibido exitosamente. Su cuenta está pendiente de validación. Recibirá un correo electrónico una vez que sus documentos sean revisados y aprobados.',
                'status' => 'pending_verification',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->first_name.' '.$user->last_name,
                    'email' => $user->email,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error en registro de practitioner: '.$e->getMessage(), [
                'email' => $request->email,
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Error al procesar el registro. Por favor, intenta nuevamente.',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor',
            ], 500);
        }
    }

    /**
     * Reactivate a deactivated practitioner (user registers again with same email)
     * Does NOT restore previous Recepy data - creates fresh profile
     */
    private function reactivateDeactivatedPractitioner(User $user, RegisterPractitionerRequest $request): User
    {
        $prefix = $request->gender == 'female' ? 'Dra. ' : 'Dr. ';

        // Reactivar usuario con nuevos datos (pendiente de validación)
        $user->update([
            'first_name' => $request->given_name,
            'last_name' => $request->family_name,
            'password' => Hash::make($request->password),
            'active' => false,
            'inactive_at' => null,
            'inactivate_by' => null,
            'validation_status' => 'pending',
            'validated_at' => null,
            'validated_by' => null,
            'rejection_reason' => null,
        ]);

        // Restaurar y actualizar practitioner (usando withTrashed para encontrar el soft-deleted)
        $practitioner = Practitioner::withTrashed()->where('user_id', $user->id)->first();

        if ($practitioner) {
            $practitioner->restore();
            $practitioner->update([
                'identifier' => strtoupper($request->identifier),
                'identifier_type' => $request->identifier_type,
                'given_name' => $request->given_name,
                'family_name' => $request->family_name,
                'name' => $prefix.$request->given_name.' '.$request->family_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'registry' => $request->registry,
                'active' => false,
            ]);
        } else {
            // Si por alguna razón no existe, crear uno nuevo
            Practitioner::create([
                'user_id' => $user->id,
                'identifier' => strtoupper($request->identifier),
                'identifier_type' => $request->identifier_type,
                'given_name' => $request->given_name,
                'family_name' => $request->family_name,
                'name' => $prefix.$request->given_name.' '.$request->family_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'registry' => $request->registry,
                'active' => false,
                'fhir_id' => 'practitioner-'.Str::uuid(),
            ]);
        }

        // NO restaurar datos de Recepy anteriores - crear perfil nuevo y fresco
        // Los datos anteriores quedan soft-deleted permanentemente
        RecepyDoctorProfile::create([
            'user_id' => $user->id,
            'email' => $request->email,
            'phone' => $request->phone,
            'medical_license_number' => $request->registry,
            'is_active' => false,
            'recepy_background_color' => 'ffffff',
        ]);

        return $user;
    }

    /**
     * Create a new practitioner user
     */
    private function createNewPractitioner(RegisterPractitionerRequest $request): User
    {
        $prefix = $request->gender == 'female' ? 'Dra. ' : 'Dr. ';

        // Crear usuario inactivo (active = 0) pendiente de validación
        $user = User::create([
            'first_name' => $request->given_name,
            'last_name' => $request->family_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'default_client_id' => 1,
            'register_source' => 'app',
            'active' => false,
            'validation_status' => 'pending',
        ]);

        // Asignar rol de doctor
        $user->assignRole('doctor');

        // Crear practitioner
        Practitioner::create([
            'user_id' => $user->id,
            'identifier' => strtoupper($request->identifier),
            'identifier_type' => $request->identifier_type,
            'given_name' => $request->given_name,
            'family_name' => $request->family_name,
            'name' => $prefix.$request->given_name.' '.$request->family_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'registry' => $request->registry,
            'active' => false,
            'fhir_id' => 'practitioner-'.Str::uuid(),
        ]);

        // Relacionar practitioner con client_id = 1
        $user->clients()->attach(1);

        // Crear RecepyDoctorProfile (inactivo)
        RecepyDoctorProfile::create([
            'user_id' => $user->id,
            'email' => $request->email,
            'phone' => $request->phone,
            'medical_license_number' => $request->registry,
            'is_active' => false,
            'recepy_background_color' => 'ffffff',
        ]);

        return $user;
    }

    /**
     * Notify administrators and validators about new user registration
     */
    private function notifyAdminsAndValidators(User $user): void
    {
        // Obtener todos los usuarios con rol admin o validador
        $adminsAndValidators = User::where('active', true)
            ->where(function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['validador','soporte']);
                });
            })
            ->get();

        // Enviar notificación a cada uno
        Notification::send($adminsAndValidators, new NewUserRegistrationNotification($user));
    }

    /**
     * Store verification documents (cedula and idoneidad) for practitioner registration
     */
    private function storeVerificationDocuments(RegisterPractitionerRequest $request, User $user): void
    {
        // Guardar archivo de cédula
        if ($request->hasFile('cedula_file')) {
            $cedulaFile = $request->file('cedula_file');
            $cedulaPath = $cedulaFile->store('verification_documents/cedulas', 'public');

            File::create([
                'table_name' => 'users',
                'record_id' => $user->id,
                'user_id' => $user->id,
                'path' => $cedulaPath,
                'name' => 'Cédula de Identidad - '.$user->full_name,
                'type' => 'cedula',
                'extention' => $cedulaFile->getClientOriginalExtension(),
            ]);
        }

        // Guardar archivo de idoneidad médica
        if ($request->hasFile('idoneidad_file')) {

            $idoneidadFile = $request->file('idoneidad_file');
            $idoneidadPath = $idoneidadFile->store('verification_documents/idoneidad', 'public');

            File::create([
                'table_name' => 'users',
                'record_id' => $user->id,
                'user_id' => $user->id,
                'path' => $idoneidadPath,
                'name' => 'Idoneidad Médica - '.$user->full_name,
                'type' => 'idoneidad',
                'extention' => $idoneidadFile->getClientOriginalExtension(),
            ]);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout exitoso',
        ]);
    }

    /**
     * Deactivate the authenticated user's account
     * Sets user active = false and soft deletes related practitioner and recepy data if exists
     */
    public function deactivateAccount(Request $request)
    {
        $user = $request->user();

        DB::beginTransaction();

        try {
            // Deactivate user account
            $user->update([
                'active' => false,
                'inactive_at' => now(),
                'inactivate_by' => $user->id,
            ]);

            // Soft delete practitioner if exists
            if ($user->practitioner) {
                $user->practitioner->delete();
            }

            // Soft delete Recepy data if exists
            $doctorProfiles = RecepyDoctorProfile::where('user_id', $user->id)->get();
            foreach ($doctorProfiles as $profile) {
                // Get all prescriptions for this doctor profile
                $prescriptions = RecepyPrescription::where('doctor_profile_id', $profile->id)->get();
                foreach ($prescriptions as $prescription) {
                    // Soft delete all medications for this prescription
                    RecepyPrescriptionMedication::where('prescription_id', $prescription->id)->delete();
                    // Soft delete the prescription
                    $prescription->delete();
                }
                // Soft delete the doctor profile
                $profile->delete();
            }

            // Revoke all user tokens
            $user->tokens()->delete();

            DB::commit();

            return response()->json([
                'message' => 'Su cuenta ha sido desactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al desactivar cuenta: '.$e->getMessage(), [
                'user_id' => $user->id,
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Error al desactivar la cuenta. Por favor, intente nuevamente.',
            ], 500);
        }
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $userType = $this->getUserType($user);

        if (! $userType) {
            return response()->json([
                'message' => 'Usuario sin rol válido.',
            ], 403);
        }

        $user->tokens()->delete();

        $tokenName = $userType === 'patient' ? 'patient-app' : 'practitioner-app';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Token renovado',
            'token' => $token,
            'user_type' => $userType,
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $userType = $this->getUserType($user);

        if (! $userType) {
            return response()->json([
                'message' => 'Usuario sin rol válido.',
            ], 403);
        }

        $specificData = $this->getUserSpecificData($user, $userType);

        return response()->json([
            'user_type' => $userType,
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
            ],
            $userType => $specificData,
        ]);
    }

    /**
     * Send a password reset email to the user (patient or practitioner)
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Verificar que el email corresponde a un usuario válido
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'No se encontró un usuario con este email.',
            ], 404);
        }

        // Verificar que es un usuario con rol válido para móvil
        $userType = $this->getUserType($user);
        if (! $userType) {
            return response()->json([
                'message' => 'Este email no corresponde a un usuario válido.',
            ], 403);
        }

        // Verificar que existe el registro específico (patient o practitioner)
        $specificData = $this->getUserSpecificData($user, $userType);
        if (! $specificData) {
            return response()->json([
                'message' => 'No se encontró información completa del usuario.',
            ], 404);
        }

        // Enviar el email de reset de contraseña
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Se ha enviado un enlace de restablecimiento de contraseña a tu email.',
            ]);
        }

        return response()->json([
            'message' => 'No se pudo enviar el enlace de restablecimiento. Intenta nuevamente.',
        ], 500);
    }

    /**
     * Reset the user's password (patient or practitioner)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verificar que el email corresponde a un usuario válido
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Email no válido.',
            ], 403);
        }

        // Verificar que es un usuario con rol válido para móvil
        $userType = $this->getUserType($user);
        if (! $userType) {
            return response()->json([
                'message' => 'Usuario sin rol válido.',
            ], 403);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Tu contraseña ha sido restablecida exitosamente.',
            ]);
        }

        return response()->json([
            'message' => 'No se pudo restablecer la contraseña. El enlace puede haber expirado.',
        ], 400);
    }

    /**
     * Detect user type based on roles
     */
    private function getUserType(User $user): ?string
    {
        if ($user->hasRole('paciente')) {
            return 'patient';
        } elseif ($user->hasRole('doctor')) {
            return 'practitioner';
        }

        return null;
    }

    /**
     * Get user-specific data based on type
     */
    private function getUserSpecificData(User $user, string $userType): ?array
    {
        switch ($userType) {
            case 'patient':
                $patient = Patient::where('user_id', $user->id)->first();
                if (! $patient) {
                    return null;
                }

                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'phone' => $patient->phone,
                    'birth_date' => $patient->birth_date,
                    'gender' => $patient->gender,
                    'identifier' => $patient->identifier,
                    'identifier_type' => $patient->identifier_type,
                ];

            case 'practitioner':
                $practitioner = Practitioner::where('user_id', $user->id)->first();
                if (! $practitioner) {
                    return null;
                }

                // Get all clients this practitioner has access to
                $clients = $user->clients()->select('clients.id', 'clients.name', 'clients.whatsapp', 'clients.long_name')->get();

                return [
                    'id' => $practitioner->id,
                    'name' => $practitioner->name,
                    'phone' => $practitioner->phone,
                    'birth_date' => $practitioner->birth_date,
                    'gender' => $practitioner->gender,
                    'identifier' => $practitioner->identifier,
                    'registry' => $practitioner->registry,
                    'active' => $practitioner->active,
                    'clients' => $clients->map(function ($client) {
                        return [
                            'id' => $client->id,
                            'name' => $client->name,
                            'phone' => $client->whatsapp,
                            'long_name' => $client->long_name,
                        ];
                    })->toArray(),
                    'clients_count' => $clients->count(),
                ];

            default:
                return null;
        }
    }

    private function getIdPattern($type)
    {
        switch ($type) {
            case 'CC': // Cédula de Ciudadanía (Panamá): 8-123-456 o PE-123-456
                return '/^[0-9]+-[0-9]+-[0-9]+$/';
            case 'CE': // Cédula Extranjera: Similar a CC
                return '/^[A-Z]+-[0-9]+-[0-9]+$/';
            case 'PA': // Pasaporte: N1234567
                return '/^[A-Z0-9-]{5,20}$/';
            case 'PT': // Permiso Temporal: Formato flexible
                return '/^[A-Z0-9-]{8,15}$/';
            case 'SS': // Seguro Social: XXX-XX-XXXX
                return '/^\d{3}-?\d{2}-?\d{4}$/';
            default:
                return '/^[A-Z0-9-]{5,20}$/'; // Universal para cualquier tipo
        }
    }
}
