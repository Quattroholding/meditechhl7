<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicPatientRegistrationRequest;
use App\Mail\PatientWelcomeMail;
use App\Models\Client;
use App\Models\Country;
use App\Models\InsuranceCompany;
use App\Models\Patient;
use App\Models\PatientClient;
use App\Models\PatientInsurancePolicy;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PublicPatientRegistrationController extends Controller
{
    /**
     * Mostrar formulario de registro público de paciente
     */
    public function showForm(Client $client)
    {
        // Verificar que el cliente existe y está activo
        if ($client->active != 1) {
            abort(403, 'Esta clínica no está disponible para registro público.');
        }

        // Cargar relación de tema
        $client->load('theme');

        // Obtener países para el selector
        $countries = Country::all();

        // Obtener compañías de seguros activas del cliente
        $insuranceCompanies = InsuranceCompany::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('public.patient-register', compact('client', 'countries', 'insuranceCompanies'));
    }

    /**
     * Procesar el registro del paciente
     */
    public function store(PublicPatientRegistrationRequest $request, Client $client)
    {
        try {
            // Verificar que el cliente existe y está activo
            if ($client->active != 1) {
                return back()
                    ->withInput()
                    ->withErrors(['client' => 'Esta clínica no está disponible para registro público.']);
            }

            return DB::transaction(function () use ($request, $client) {
                // 1. Verificar que el email no existe en la tabla de usuarios
                $existingUser = User::where('email', $request->email)->first();
                if ($existingUser) {
                    return back()
                        ->withInput()
                        ->withErrors(['email' => 'Este correo electrónico ya está registrado en el sistema.']);
                }

                // 2. Generar contraseña automática
                $password = Str::password(8);

                // 3. Crear User con rol 'paciente'
                $user = new User;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = strtolower($request->email);
                $user->password = $password;
                $user->whatsapp_phone = $request->full_phone;
                $user->active = true;
                $user->save();

                // Asignar rol de paciente
                $user->assignRole('paciente');

                // 4. Crear Patient asociado al User
                $patient = new Patient;
                $patient->given_name = $request->first_name;
                $patient->family_name = $request->last_name;
                $patient->name = $request->first_name.' '.$request->last_name;
                $patient->email = $user->email;
                $patient->user_id = $user->id;

                // Información personal
                $patient->gender = $request->gender;
                $patient->birth_date = $request->birthdate;
                $patient->marital_status = $request->marital_status;
                $patient->blood_type = $request->blood_type;

                // Contacto
                $patient->phone = $request->full_phone;
                $patient->whatsapp_phone = $request->full_phone;
                $patient->address = $request->physical_address;

                // Contacto de emergencia
                $patient->contact_name = $request->contact_name;
                $patient->contact_email = $request->contact_email;
                $patient->contact_phone = $request->full_contact_phone;

                // Ubicación
                $patient->country_id = $request->country_id;
                $patient->state_id = $request->state_id;

                // Identificación
                $patient->identifier_type = $request->id_type;
                $patient->identifier = strtoupper($request->id_number);

                // FHIR
                $patient->fhir_id = 'patient-'.Str::uuid();
                $patient->communication = json_encode(['language' => 'es', 'preferred' => true]);

                $patient->save();

                // 5. Crear PatientClient (asociación cliente-paciente)
                PatientClient::create([
                    'client_id' => $client->id,
                    'patient_id' => $patient->id,
                ]);

                // 6. Crear póliza de seguro si se proporcionó información
                if ($request->has_insurance && $request->insurance_company_id) {
                    $insuranceData = [
                        'patient_id' => $patient->id,
                        'insurance_company_id' => $request->insurance_company_id,
                        'policy_number' => $request->policy_number,
                        'group_number' => $request->group_number,
                        'relationship_to_subscriber' => $request->relationship_to_subscriber ?? 'self',
                        'subscriber_name' => $request->relationship_to_subscriber === 'self'
                            ? $patient->name
                            : $request->subscriber_name,
                        'priority' => 'primary',
                        'is_active' => true,
                        'effective_date' => now(),
                    ];

                    // Si el titular es el mismo paciente, vincular subscriber_patient_id
                    if ($request->relationship_to_subscriber === 'self') {
                        $insuranceData['subscriber_patient_id'] = $patient->id;
                    }

                    PatientInsurancePolicy::create($insuranceData);

                    Log::info('Insurance policy created for patient', [
                        'patient_id' => $patient->id,
                        'insurance_company_id' => $request->insurance_company_id,
                        'policy_number' => $request->policy_number,
                    ]);
                }

                // 7. Enviar email de bienvenida con credenciales
                $registrationData = [
                    'username' => $user->email,
                    'password' => $password,
                ];

                if (config('mail.testing_mode')) {
                    Mail::to(config('mail.testing_patient_email'))->send(
                        new PatientWelcomeMail($patient, $client, $registrationData)
                    );
                } else {
                    Mail::to($user)->send(
                        new PatientWelcomeMail($patient, $client, $registrationData)
                    );
                }

                Log::info('Public patient registration successful', [
                    'client_id' => $client->id,
                    'patient_id' => $patient->id,
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);

                // 8. Redireccionar a success
                return redirect()->route('public.patient.register.success')
                    ->with('client_name', $client->name)
                    ->with('email', $user->email);
            });

        } catch (\Exception $e) {
            Log::error('Public patient registration failed', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Hubo un error al procesar tu registro. Por favor intenta nuevamente.']);
        }
    }

    /**
     * Página de confirmación de registro exitoso
     * Solo accesible después de completar el formulario
     */
    public function success()
    {
        // Verificar que venga del formulario (debe tener datos de sesión)
        if (! session()->has('client_name') || ! session()->has('email')) {
            Log::warning('Intento de acceso directo a página de success sin datos de sesión');

            // Si no viene del formulario, redirigir al inicio
            return redirect('/')->with('error', 'Acceso no autorizado');
        }

        $clientName = session('client_name');
        $email = session('email');

        Log::info('Mostrando página de success', [
            'client_name' => $clientName,
            'email' => $email,
        ]);

        // Limpiar la sesión después de obtener los datos
        // para evitar accesos múltiples a esta página
        session()->forget(['client_name', 'email']);

        return view('public.patient-registration-success', compact('clientName', 'email'));
    }
}
