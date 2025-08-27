<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
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

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Verificar que el usuario es un paciente
        if (! $user->hasRole('paciente')) {
            throw ValidationException::withMessages([
                'email' => ['Acceso no autorizado para pacientes.'],
            ]);
        }

        $patient = Patient::whereUserId($user->id)->first();

        if (! $patient) {
            throw ValidationException::withMessages([
                'email' => ['No se encontró información del paciente.'],
            ]);
        }

        $token = $user->createToken('patient-app')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
            ],
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'phone' => $patient->phone,
                'birth_date' => $patient->birth_date,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'regex:'.$this->getIdPattern($request->identifier_type)],
            'identifier_type' => 'required',
            'given_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
        ]);



        // Crear usuario
        $user = User::create([
            'first_name' => $request->given_name,
            'last_name' => $request->family_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('paciente');

        $patient = DB::table('patients')
            ->whereLike('identifier',$this->identifier.'%')
            ->orWhere('email',$request->email)
            ->get();

        if(!$patient){
            // Crear paciente
            $patient = Patient::create([
                'user_id' => $user->id,
                'identifier' =>$request->identifier,
                'identifier_type'=>$request->identifier_type,
                'given_name' => $request->given_name,
                'family_name' => $request->family_name,
                'name' => $request->given_name.' '.$request->family_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'whatsapp_phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'fhir_id'=> 'patient-'.Str::uuid(),
                'communication' => json_encode(['language' => 'es', 'preferred' => true]),
                'address'=>$request->address,
            ]);


        }else{
            $patient->fill($request->all());
            $patient->name = $request->given_name.' '.$request->family_name;
            $patient->whatsapp_phone = $request->phone;
            $patient->user_id = $user->id;
            $patient->save();
        }

        $token = $user->createToken('patient-app')->plainTextToken;

        return response()->json([
            'message' => 'Registro exitoso',
            'token' => $token,
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

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout exitoso',
        ]);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        $token = $user->createToken('patient-app')->plainTextToken;

        return response()->json([
            'message' => 'Token renovado',
            'token' => $token,
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $patient = Patient::where('email', $user->email)->first();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'patient' => $patient ? [
                'id' => $patient->id,
                'name' => $patient->name,
                'phone' => $patient->phone,
                'birth_date' => $patient->birth_date,
            ] : null,
        ]);
    }

    /**
     * Send a password reset email to the patient
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Verificar que el email corresponde a un usuario con rol paciente
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'No se encontró un usuario con este email.',
            ], 404);
        }

        if (!$user->hasRole('paciente')) {
            return response()->json([
                'message' => 'Este email no corresponde a un paciente.',
            ], 403);
        }

        // Verificar que existe el registro de paciente
        $patient = Patient::whereUserId($user->id)->first();
        if (!$patient) {
            return response()->json([
                'message' => 'No se encontró información del paciente.',
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
     * Reset the patient's password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verificar que el email corresponde a un usuario con rol paciente
        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->hasRole('paciente')) {
            return response()->json([
                'message' => 'Email no válido o no corresponde a un paciente.',
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

    private function getIdPattern($type)
    {
        switch ($type) {
            case 'CC': // Cédula de Ciudadanía (Panamá): 8-123-456 o PE-123-456
                return '/^[0-9]+-[0-9]+$/';
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
