<?php

namespace App\Http\Controllers;

use App\Mail\PatientWelcomeMail;
use App\Models\Client;
use App\Models\Country;
use App\Models\Patient;
use App\Models\PatientRelationship;
use App\Models\State;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PatientController extends Controller
{
    public function index()
    {
        $model = Patient::class;

        return view('patients.index', compact('model'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'id_number' => 'required',
            'id_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'birthdate' => 'required',
            'physical_address' => 'required',
            'billing_address' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'full_phone' => 'required',
            'blood_type' => 'required',
            'password' => 'required',
            'image' => 'required',
        ]);
        // Verificar si el correo ya está registrado
        $email_validation = User::where('email', $request->email)->first();
        if (! empty($email_validation)) {
            // El correo ya está registrado
            session()->flash('message', 'Este correo ya se encuentra registrado, por favor inicie sesión');

            return back();
        }
        // DEBO CREAR UN PASSWORD GENÉRICO
        $model = new User;
        $model->first_name = $request->first_name;
        $model->last_name = $request->last_name;
        $model->email = $request->email;
        $model->password = $request->password;
        $model->save();

        // Asignar rol de paciente
        $model->assignRole('paciente');

        $patient = new Patient;
        $patient->given_name = $request->first_name;
        $patient->family_name = $request->last_name;
        $patient->email = $request->email;
        $patient->phone = $request->full_phone;
        $patient->name = $request->first_name.' '.$request->last_name;
        $patient->user_id = $model->id;
        $patient->fhir_id = 'patient-'.Str::uuid();
        // IDENTIFIER ES ID
        $patient->identifier = $request->id_number;
    }

    public function store_public(Request $request)
    {

        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'full_phone' => 'required',
            'password' => 'required',
            'terms_and_privacy' => 'required|accepted',
        ]);

        // Verificar si el correo ya está registrado
        $email_validation = User::where('email', $request->email)->first();

        if (! empty($email_validation)) {
            // El correo ya está registrado
            session()->flash('message.error', 'Este correo ya se encuentra registrado, por favor inicie sesión');

            return redirect('/login');
        }

        $model = new User;
        $model->first_name = $request->first_name;
        $model->last_name = $request->last_name;
        $model->email = $request->email;
        $model->password = $request->password;
        $model->first_login_at = now();

        if ($model->save()) {
            $request->session()->flash('message.success', 'Se ha creado el usuario con éxito.');
        } else {
            $request->session()->flash('message.error', 'Hubo un error y no se puso completar con la creación de este usuario');

            return redirect(route('patient.register'));
        }

        // Asignar rol de paciente
        $model->assignRole('paciente');

        $patient = new Patient;
        $patient->given_name = $request->first_name;
        $patient->family_name = $request->last_name;
        $patient->email = $request->email;
        $patient->phone = $request->full_phone;
        $patient->name = $request->first_name.' '.$request->last_name;
        $patient->user_id = $model->id;
        $patient->fhir_id = 'patient-'.Str::uuid();
        // $patient->identifier = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        if ($patient->save()) {

            session()->flash('message', 'Se ha registrado exitosamente el paciente');

            $registrationData = [
                'username' => $model->email,
                'password' => $request->password,
            ];

            // Mail::to($model)->send(new PatientWelcomeMail($patient,$client,$registrationData));
            $client = Client::find(1);
            $email = $patient->email;
            if (config('mail.testing_mode')) {
                $email = config('mail.testing_patient_email');
            }

            Mail::to($email)->send(new PatientWelcomeMail($patient, $client, $registrationData, 'patient'));

            $credentials = ([
                'email' => $model->email,
                'password' => $request->password,
            ]);
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $route = route('patient.profile', $patient->id);

                return redirect()->intended($route);
            }
        } else {
            $user_created = User::find($model->id);
            $user_created->delete();
            session()->flash('message', 'Ha habido un error con el registro del paciente');

            return redirect(route('patient.register'));
        }
    }

    public function profile(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        return view('patients.profile', compact('patient'));
    }

    public function edit($id)
    {

        $data = Patient::findOrFail($id);
        if (! $data) {
            return view('Pages.error-403');
        }

        $client_id = 1;
        if (auth()->user()->getCurrentClient()) {
            $client_id = auth()->user()->getCurrentClient()->id;
        }

        // Load existing relationships
        $relationships = $data->relationships()->with('relatedPatient')->get();
        $currentRelationship = $relationships->first();

        // Load all patients for dependency selection (excluding current patient)
        $availablePatients = Patient::where('id', '!=', $id)
            ->whereHas('clients', function ($query) use ($client_id) {
                $query->where('clients.id', $client_id);
            })
            ->select('id', 'name', 'identifier')
            ->orderBy('name')
            ->get();

        // Load countries and states
        $countries = Country::all();
        $states = [];
        if ($data->country_id) {
            $states = State::where('country_id', $data->country_id)->get();
        }

        return view('patients.edit', compact('data', 'relationships', 'currentRelationship', 'availablePatients', 'countries', 'states'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'identifier' => 'required',
                'identifier_type' => 'required',
                'given_name' => 'required',
                'family_name' => 'required',
                'gender' => 'required',
                'birth_date' => 'required',
                'address' => 'required',
                'marital_status' => 'required',
                // 'billing_address' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'blood_type' => 'nullable',
                'contact_name' => 'nullable|string|max:255',
                'contact_email' => 'nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:255',
            ]);


        $model = Patient::findOrFail($id);
        $model->fill($request->except('birth_date', 'id_type', 'phone'));
        $model->name = $request->given_name.' '.$request->family_name;
        $model->identifier_type = $request->id_type ?? $request->identifier_type;
        $model->phone = $request->full_phone ?? $request->phone;
        $model->birth_date = substr($request->birth_date, 6, 4).'-'.substr($request->birth_date, 3, 2).'-'.substr($request->birth_date, 0, 2);
        $model->country_id = $request->country_id;
        $model->state_id = $request->state_id;

        $user = User::findOrFail($model->user_id);
        $user->first_name = $request->given_name;
        $user->last_name = $request->family_name;
        $user->email = $request->email;

        if ($user->save()) {
            if ($model->save()) {
                // Handle relationship updates
                $this->updatePatientRelationships($request, $model);

                if ($request->file('image')) {
                    $service = new FileService;
                    $data['record_id'] = $model->id;
                    $data['folder'] = 'patients';
                    $data['type'] = 'avatar';
                    $service->guardarArchivos([$request->file('image')], $data);
                }
                session()->flash('message.success', 'Actualización con exito.');
            }
        } else {
            session()->flash('message.success', 'Hubo un error y no se pudo actualizar.');
        }

            if ($request->has('redirect')) {
                return redirect($request->redirect);
            }



        } catch (ValidationException $e) {
            // Log validation errors for debugging
            \Log::error('Patient Update Validation Failed', [
                'patient_id' => $id,
                'errors' => $e->errors(),
                'input' => $request->except(['password', 'image']),
            ]);

            // Re-throw the exception to let Laravel handle the redirect with errors
            session()->flash('message.error',$e->getMessage());
        }

        return redirect(route('patient.edit', [$id]));
    }

    public function show($id)
    {
        return view('patients.show', compact('id'));
    }

    private function updatePatientRelationships(Request $request, Patient $patient): void
    {
        // Delete existing relationships if no longer dependent
        if (! $request->has('is_dependent') || ! $request->is_dependent) {
            $patient->relationships()->delete();

            return;
        }

        // Validate required fields for dependent
        if (! $request->primary_patient_id || ! $request->relationship_type) {
            return;
        }

        // Delete existing relationships first
        $patient->relationships()->delete();

        // Create new relationship
        $primaryPatient = Patient::find($request->primary_patient_id);
        if ($primaryPatient) {
            PatientRelationship::create([
                'fhir_id' => 'rel-'.uniqid(),
                'patient_id' => $request->primary_patient_id,
                'related_patient_id' => $patient->id,
                'relationship_code' => $request->relationship_type,
                'relationship_display' => PatientRelationship::RELATIONSHIP_CODES[$request->relationship_type] ?? $request->relationship_type,
                'is_emergency_contact' => $request->has('is_emergency_contact'),
                'is_insurance_subscriber' => false,
                'effective_date' => now(),
                'is_active' => true,
                'notes' => 'Updated during patient edit',
            ]);

            // Create reverse relationship
            $reverseRelationshipMap = [
                'CHILD' => 'PARENT',
                'PARENT' => 'CHILD',
                'SPOUSE' => 'SPOUSE',
                'SIBLING' => 'SIBLING',
            ];

            if (isset($reverseRelationshipMap[$request->relationship_type])) {
                PatientRelationship::create([
                    'fhir_id' => 'rel-'.uniqid(),
                    'patient_id' => $patient->id,
                    'related_patient_id' => $request->primary_patient_id,
                    'relationship_code' => $reverseRelationshipMap[$request->relationship_type],
                    'relationship_display' => PatientRelationship::RELATIONSHIP_CODES[$reverseRelationshipMap[$request->relationship_type]] ?? $reverseRelationshipMap[$request->relationship_type],
                    'effective_date' => now(),
                    'is_active' => true,
                    'notes' => 'Auto-created reverse relationship during edit',
                ]);
            }
        }
    }

    public function getRelationshipOptions(): array
    {
        return [
            'CHILD' => 'Hijo/a',
            'SPOUSE' => 'Cónyuge',
            'PARENT' => 'Padre/Madre',
            'SIBLING' => 'Hermano/a',
            'DOMPART' => 'Pareja',
            'GRANDCHILD' => 'Nieto/a',
            'GRANDPRN' => 'Abuelo/a',
        ];
    }

    public function destroy($id)
    {

        $data = Patient::find($id);
        $data->delete();

        return redirect(route('patient.index'));
    }

    public function medicalHistory(Request $request, $id)
    {

        $patient = Patient::findOrFail($id);

        return view('patients.medicalHistory.index', compact('patient'));
    }

    public function check($idNumber)
    {
        $exists = Patient::where('identifier', $idNumber)->exists();

        return response()->json(['exists' => $exists]);

    }

    public function associate(Request $request)
    {
        $idNumber = $request->input('id_number');
        $patient = Patient::where('identifier', $idNumber)->first();

        // Asociar paciente a la clínica del usuario logueado
        if ($patient) {
            /*$clinicId = auth()->user()->clinic_id;
            $patient->clinics()->syncWithoutDetaching([$clinicId]);*/
            /* $client = UserClient::where('user_id',$patient->identifier)->first(); */
            $user_client = new PatientClinic;
            $user_client->patient_id = $patient->identifier;
            $user_client->client_id = auth()->user()->getCurrentClient()->id;
            $user_client->save();

            return response()->json(['message' => 'Paciente asociado']);
        }

        return response()->json(['error' => 'Paciente no encontrado'], 404);
    }

    public function insurances($id)
    {

        $patient = Patient::findOrFail($id);

        return view('patients.insurances', compact('patient'));
    }
}
