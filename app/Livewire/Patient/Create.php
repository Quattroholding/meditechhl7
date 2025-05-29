<?php

namespace App\Livewire\Patient;

use App\Mail\PatientWelcomeMail;
use App\Models\Client;
use App\Models\Patient;
use App\Models\PatientClient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{

    public $client_id;
    public $patient_id;
    public $id_type='CC';
    public $id_number;
    public $first_name;
    public $last_name;
    public $email;
    public $gender;
    public $birthdate;
    public $physical_address;
    public $billing_address;
    public $phone;
    public $blood_type;
    public $password;
    public $marital_status;
    public $patientExists=false;
    public $patientDontExists=false;

    protected $rules = [
        'id_number' => 'required',
        'id_type' => 'required',
        'first_name' => 'required',
        'last_name' => 'required',
        'gender' => 'required',
        'birthdate' => 'required',
        'physical_address' => 'required',
        'billing_address' => 'required',
        'email' => 'required|unique:'.Patient::class,
        'phone' => 'required',
        'blood_type' => 'required',
    ];

    protected $messages = [
        // 'patient_id.required' => 'Debe seleccionar un paciente.',
        'id_type.required' => 'El tipo de documento es requerido.',
        'id_number.required' => 'El numero de documento es requerido.',
        'first_name.required' => 'El nombre es requerido.',
        'last_name.required' => 'El apellido es requerido.'
    ];

    public function render()
    {
        if(auth()->user()->clients()->first()) $this->client_id = auth()->user()->clients()->first()->id;
        return view('livewire.patient.create');
    }

    public function updatedIdNumber(){

        $this->patientExists=false;
        $this->patient_id =null;
        $query = DB::table('patients')->whereIdentifier($this->id_number)->first();
        if($query){
            $this->patientExists=true;
            $this->patient_id = $query->id;
        }else if(strlen($this->id_number)>5){
            $this->patientDontExists=true;
        }
    }

    public function asociar(){

        $pc = PatientClient::whereClientId($this->client_id)->wherePatientId($this->patient_id)->first();

        if(!$pc){
            PatientClient::create([
                'client_id'=>$this->client_id,
                'patient_id'=>$this->patient_id
            ]);

            session()->flash('message', 'Paciente asociado exitosamente.');
        }else{
            session()->flash('message', 'Este paciente ya se encuentra asociado a su cuenta.');
        }

        $this->id_number=null;
        $this->patientExists=false;
    }

    public function savePatient(){

        //$this->validate();

        // Verificar si el correo ya está registrado
        $email_validation = User::where('email', $this->email)->first();
        if (!empty($email_validation)) {
            // El correo ya está registrado
            session()->flash('message', 'Este correo ya se encuentra registrado, por favor inicie sesión');
            return back();
        }

        $this->password = Str::password(8);

        //DEBO CREAR UN PASSWORD GENÉRICO
        $model = new User();
        $model->first_name = $this->first_name;
        $model->last_name = $this->last_name;
        $model->email = strtolower($this->email);
        $model->password = $this->password;
        $model->save();

        // Asignar rol de paciente
        $model->assignRole('paciente');

        $patient = new Patient();
        $patient->given_name = $this->first_name;
        $patient->family_name = $this->last_name;
        $patient->email = $model->email;
        $patient->phone = $this->phone;
        $patient->name = $this->first_name .' '. $this->last_name;
        $patient->user_id = $model->id;
        $patient->birth_date =$this->birthdate;
        $patient->fhir_id = 'patient-' . Str::uuid();
        $patient->communication = json_encode(['language'=>'es','preferred'=>true]);
        $patient->address = $this->physical_address;
        $patient->billing_address = $this->billing_address;
        $patient->marital_status = $this->marital_status;
        $patient->blood_type = $this->blood_type;
        //IDENTIFIER ES ID
        $patient->identifier_type = $this->id_type;
        $patient->identifier = strtoupper($this->id_number);

        if($patient->save()){
            $this->resetForm();
            PatientClient::create([
                'client_id'=>$this->client_id,
                'patient_id'=>$patient->id,
            ]);
            $client = Client::find($this->client_id);
            session()->flash('message', 'Paciente registrado exitosamente.');

            $registrationData=[
                'username'=>$this->email,
                'password'=>$this->password,
            ];

            //Mail::to($model)->send(new PatientWelcomeMail($patient,$client,$registrationData));
            Mail::to('rgasperi@smarcarebilling.com')->send(new PatientWelcomeMail($patient,$client,$registrationData));

        }else{
            session()->flash('error', 'Hubo un error y no se pudo actualizar el paciente.');
        }

    }

    public function resetForm(){
        $this->patientExists=false;
        $this->patientDontExists=false;
        $this->id_number='';
        $this->first_name='';
        $this->last_name='';
        $this->email='';
        $this->gender='';
        $this->birthdate='';
        $this->physical_address='';
        $this->billing_address='';
        $this->phone='';
        $this->blood_type='';
        $this->marital_status='';
    }
}
