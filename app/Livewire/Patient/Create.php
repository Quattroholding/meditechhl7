<?php

namespace App\Livewire\Patient;

use App\Mail\PatientWelcomeMail;
use App\Models\Client;
use App\Models\Country;
use App\Models\Patient;
use App\Models\PatientClient;
use App\Models\PatientRelationship;
use App\Models\State;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $client_id;

    public $patient_id;

    public $patients = [];

    public $id_type = 'CC';

    public $id_number;

    public $first_name;

    public $last_name;

    public $email;

    public $gender;

    public $birthdate;

    public $physical_address;

    public $phone;

    public $blood_type;

    public $password;

    public $marital_status;

    public $patientExists = false;

    public $patientDontExists = true;

    // Dependent patient fields
    public $is_dependent = false;

    public $primary_patient_id;

    public $relationship_type;

    public $copy_insurance = false;

    public $is_emergency_contact = false;

    public $primary_patients = [];

    // Country and State fields
    public $country_id;

    public $state_id;

    public $countries = [];

    public $states = [];

    // File upload fields
    public $archivos = [];

    protected $rules = [
        'id_number' => 'required',
        'id_type' => 'required',
        'first_name' => 'required',
        'last_name' => 'required',
        'gender' => 'required',
        'birthdate' => 'required',
        'physical_address' => 'required',
        'marital_status' => 'required',
        'email' => 'required|unique:'.Patient::class,
        'phone' => 'required',
        // 'blood_type' => 'required',
        'primary_patient_id' => 'required_if:is_dependent,true',
        'relationship_type' => 'required_if:is_dependent,true',
    ];

    public function rules()
    {
        return [
            'id_number' => ['required', 'regex:'.$this->getIdPattern()],
            'id_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'birthdate' => 'required',
            'physical_address' => 'required',
            'marital_status' => 'required',
            'email' => 'required|unique:'.Patient::class,
            'phone' => 'required',
            'primary_patient_id' => 'required_if:is_dependent,true',
            'relationship_type' => 'required_if:is_dependent,true',
            'archivos.*' => 'nullable|file|max:1024', // 1MB max por archivo
        ];
    }

    protected $messages = [
        // 'patient_id.required' => 'Debe seleccionar un paciente.',
        'id_type.required' => 'El tipo de documento es obligatorio',
        'id_number.required' => 'El numero de documento es obligatorio.',
        'id_number.regex' => 'El formato del número de documento no es válido.',
        'first_name.required' => 'El nombre es obligatorio',
        'last_name.required' => 'El apellido es obligatorio',
        'marital_status.required' => 'El estado civil es obligatorio',
        'primary_patient_id.required_if' => 'Debe seleccionar el paciente principal',
        'relationship_type.required_if' => 'Debe seleccionar el tipo de relación',
    ];

    public function mount()
    {
        $this->countries = Country::all()->toArray();
    }

    public function render()
    {
        $this->client_id = 1;
        if (auth()->user()->getCurrentClient()) {
            $this->client_id = auth()->user()->getCurrentClient()->id;
        }

        // Load primary patients for dependent selection
        $this->loadPrimaryPatients();

        return view('livewire.patient.create');
    }

    public function updatedCountryId()
    {
        if ($this->country_id) {
            $this->states = State::where('country_id', $this->country_id)->get()->toArray();
            $this->state_id = null;
        } else {
            $this->states = [];
            $this->state_id = null;
        }
    }

    public function loadPrimaryPatients()
    {
        if ($this->is_dependent) {
            // Only load patients from the current client to avoid 403 errors
            $this->primary_patients = Patient::select('id', 'name', 'identifier')
                ->whereHas('clients', function ($query) {
                    $query->where('clients.id', $this->client_id);
                })
                ->orderBy('name')
                ->get()
                ->toArray();
        }
    }

    public function updatedIsDependent()
    {
        if (! $this->is_dependent) {
            $this->primary_patient_id = null;
            $this->relationship_type = null;
            $this->copy_insurance = false;
            $this->is_emergency_contact = false;
            $this->primary_patients = [];
        } else {
            $this->loadPrimaryPatients();
        }
    }

    public function updatedIdNumber()
    {

        $this->patientDontExists = true;
        $this->patientExists = false;
        $this->patients = [];
        $query = DB::table('patients')
            ->whereRaw("(identifier ='".$this->id_number."' or identifier ='".$this->id_number."-SELF' or identifier ='".$this->id_number."-SPOUSE' or identifier ='".$this->id_number."-CHILD' or identifier ='".$this->id_number."-CHILDDISAB')")
            ->get();

        if ($query->count() > 0) {
            $this->patientDontExists = false;
            $this->patientExists = true;
            $this->patients = $query->pluck('id');

        }
    }

    public function asociar()
    {
        foreach ($this->patients as $patient_id) {
            $pc = PatientClient::whereClientId($this->client_id)->wherePatientId($patient_id)->first();

            if (! $pc) {
                PatientClient::create([
                    'client_id' => $this->client_id,
                    'patient_id' => $patient_id,
                ]);
            }
        }

        session()->flash('message.success', 'Paciente asociado exitosamente.');

        $this->id_number = null;
        $this->patientExists = false;
    }

    public function savePatient()
    {

        $this->validate();

        // Verificar si el correo ya está registrado
        $email_validation = User::where('email', $this->email)->first();
        if (! empty($email_validation)) {
            // El correo ya está registrado
            session()->flash('message.error', 'Este correo ya se encuentra registrado.');

            return back();
        }

        $this->password = Str::password(8);

        // DEBO CREAR UN PASSWORD GENÉRICO
        $model = new User;
        $model->first_name = $this->first_name;
        $model->last_name = $this->last_name;
        $model->email = strtolower($this->email);
        $model->password = $this->password;
        $model->whatsapp_phone = $this->phone;
        $model->save();

        // Asignar rol de paciente
        $model->assignRole('paciente');

        $patient = new Patient;
        $patient->given_name = $this->first_name;
        $patient->family_name = $this->last_name;
        $patient->email = $model->email;
        $patient->phone = $this->phone;
        $patient->whatsapp_phone = $this->phone;
        $patient->name = $this->first_name.' '.$this->last_name;
        $patient->user_id = $model->id;
        $patient->gender = $this->gender;
        $patient->birth_date = $this->birthdate;
        $patient->fhir_id = 'patient-'.Str::uuid();
        $patient->communication = json_encode(['language' => 'es', 'preferred' => true]);
        $patient->address = $this->physical_address;
        $patient->marital_status = $this->marital_status;
        $patient->blood_type = $this->blood_type;
        $patient->country_id = $this->country_id;
        $patient->state_id = $this->state_id;
        // IDENTIFIER ES ID
        $patient->identifier_type = $this->id_type;
        $patient->identifier = strtoupper($this->id_number);

        if ($patient->save()) {
            // Create patient-client relationship
            PatientClient::create([
                'client_id' => $this->client_id,
                'patient_id' => $patient->id,
            ]);

            // Handle dependent patient logic
            if ($this->is_dependent && $this->primary_patient_id) {
                $this->createPatientRelationship($patient);

                if ($this->copy_insurance) {
                    $this->copyInsuranceFromPrimary($patient);
                }
            }

            // Guardar archivos si existen
            if (count($this->archivos) > 0) {
                $fileService = new FileService;
                $data = [
                    'folder' => 'patients',
                    'record_id' => $patient->id,
                    'type' => 'document',
                ];
                $fileService->guardarArchivos($this->archivos, $data);
            }

            $this->resetForm();
            $client = Client::find($this->client_id);
            session()->flash('message.success', 'Paciente registrado exitosamente.');
            $this->dispatch('showToastr',
                type: 'success',
                message: 'Paciente registrado exitosamente.',
            );
            $registrationData = [
                'username' => $model->email,
                'password' => $this->password,
            ];

            // Mail::to($model)->send(new PatientWelcomeMail($patient,$client,$registrationData));
            Mail::to('rgasperi@smartcarebilling.com')->send(new PatientWelcomeMail($patient, $client, $registrationData));

        } else {
            session()->flash('message.error', 'Hubo un error y no se pudo actualizar el paciente.');
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Hubo un error y no se pudo actualizar el paciente.',
            );
        }

    }

    public function resetForm()
    {
        $this->patientExists = false;
        $this->patientDontExists = false;
        $this->id_number = '';
        $this->first_name = '';
        $this->last_name = '';
        $this->email = '';
        $this->gender = '';
        $this->birthdate = '';
        $this->physical_address = '';
        $this->phone = '';
        $this->blood_type = '';
        $this->marital_status = '';
        $this->is_dependent = false;
        $this->primary_patient_id = null;
        $this->relationship_type = null;
        $this->copy_insurance = false;
        $this->is_emergency_contact = false;
        $this->primary_patients = [];
        $this->country_id = null;
        $this->state_id = null;
        $this->states = [];
        $this->archivos = [];
    }

    private function getIdPattern()
    {
        switch ($this->id_type) {
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

    public function getIdPlaceholder()
    {
        switch ($this->id_type) {
            case 'CC':
                return 'Ej: 8-123456 ';
            case 'CE':
                return 'Ej: E-8-123456 o PE-123456';
            case 'PA':
                return 'Ej: PA1234567';
            case 'PT':
                return 'Ej: PT-12345678';
            case 'SS':
                return 'Ej: 123-45-6789';
            default:
                return 'Ingrese el número de documento';
        }
    }

    private function createPatientRelationship(Patient $patient): void
    {
        $primaryPatient = Patient::find($this->primary_patient_id);

        if ($primaryPatient) {
            PatientRelationship::create([
                'fhir_id' => 'rel-'.uniqid(),
                'patient_id' => $this->primary_patient_id,
                'related_patient_id' => $patient->id,
                'relationship_code' => $this->relationship_type,
                'relationship_display' => PatientRelationship::RELATIONSHIP_CODES[$this->relationship_type] ?? $this->relationship_type,
                'is_emergency_contact' => $this->is_emergency_contact,
                'is_insurance_subscriber' => $this->copy_insurance,
                'effective_date' => now(),
                'is_active' => true,
                'notes' => 'Created during patient registration as dependent',
            ]);

            // Create reverse relationship for certain types
            $reverseRelationshipMap = [
                'CHILD' => 'PARENT',
                'PARENT' => 'CHILD',
                'SPOUSE' => 'SPOUSE',
                'SIBLING' => 'SIBLING',
            ];

            if (isset($reverseRelationshipMap[$this->relationship_type])) {
                PatientRelationship::create([
                    'fhir_id' => 'rel-'.uniqid(),
                    'patient_id' => $patient->id,
                    'related_patient_id' => $this->primary_patient_id,
                    'relationship_code' => $reverseRelationshipMap[$this->relationship_type],
                    'relationship_display' => PatientRelationship::RELATIONSHIP_CODES[$reverseRelationshipMap[$this->relationship_type]] ?? $reverseRelationshipMap[$this->relationship_type],
                    'effective_date' => now(),
                    'is_active' => true,
                    'notes' => 'Auto-created reverse relationship',
                ]);
            }
        }
    }

    private function copyInsuranceFromPrimary(Patient $patient): void
    {
        $primaryPatient = Patient::find($this->primary_patient_id);

        if ($primaryPatient) {
            $primaryInsurance = $primaryPatient->primaryInsurance;

            if ($primaryInsurance) {
                // Determine relationship for insurance
                $insuranceRelationship = $this->getInsuranceRelationship($this->relationship_type);

                $patient->insurancePolicies()->create([
                    'insurance_company_id' => $primaryInsurance->insurance_company_id,
                    'policy_number' => $primaryInsurance->policy_number,
                    'group_number' => $primaryInsurance->group_number,
                    'subscriber_id' => $primaryInsurance->subscriber_id,
                    'subscriber_name' => $primaryPatient->name,
                    'subscriber_patient_id' => $this->primary_patient_id,
                    'relationship_to_subscriber' => $insuranceRelationship,
                    'effective_date' => $primaryInsurance->effective_date,
                    'expiration_date' => $primaryInsurance->expiration_date,
                    'priority' => 'primary',
                    'coverage_percentage' => $primaryInsurance->coverage_percentage,
                    'copay_amount' => $primaryInsurance->copay_amount,
                    'deductible_amount' => $primaryInsurance->deductible_amount,
                    'deductible_remaining' => $primaryInsurance->deductible_amount,
                    'out_of_pocket_max' => $primaryInsurance->out_of_pocket_max,
                    'out_of_pocket_remaining' => $primaryInsurance->out_of_pocket_max,
                    'is_active' => true,
                    'coverage_details' => $primaryInsurance->coverage_details,
                    'notes' => 'Copied from primary patient: '.$primaryPatient->name,
                ]);
            }
        }
    }

    private function getInsuranceRelationship(string $relationshipCode): string
    {
        $relationshipMap = [
            'CHILD' => 'child',
            'SPOUSE' => 'spouse',
            'PARENT' => 'parent',
            'SIBLING' => 'other',
            'DOMPART' => 'spouse',
        ];

        return $relationshipMap[$relationshipCode] ?? 'other';
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
}
