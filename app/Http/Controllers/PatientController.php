<?php

namespace App\Http\Controllers;

use App\Mail\PatientWelcomeMail;
use App\Models\Client;
use App\Models\Patient;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Faker\Factory as Faker;

class PatientController extends Controller
{
    public function index(){
        $model = Patient::class;

        return view('patients.index',compact('model'));
    }

    public function create(){
        return view('patients.create');
    }

    public function store(Request $request){
        //dd($request->all());
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
            'password' => "required",
            'image' => "required",
        ]);
        // Verificar si el correo ya está registrado
        $email_validation = User::where('email', $request->email)->first();
        if (!empty($email_validation)) {
            // El correo ya está registrado
            session()->flash('message', 'Este correo ya se encuentra registrado, por favor inicie sesión');
            return back();
        }
        //DEBO CREAR UN PASSWORD GENÉRICO
        $model = new User();
        $model->first_name = $request->first_name;
        $model->last_name = $request->last_name;
        $model->email = $request->email;
        $model->password = $request->password;
        $model->save();

        // Asignar rol de paciente
        $model->assignRole('paciente');

        $patient = new Patient();
        $patient->given_name = $request->first_name;
        $patient->family_name = $request->last_name;
        $patient->email = $request->email;
        $patient->phone = $request->full_phone;
        $patient->name = $request->first_name .' '. $request->last_name;
        $patient->user_id = $model->id;
        $patient->fhir_id = 'patient-' . Str::uuid();
        //IDENTIFIER ES ID
        $patient->identifier = $request->id_number;
    }
    public function store_public(Request $request){

        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            //'full_phone' => 'required',
            'password' => "required",
            'terms_and_privacy' => "required"
        ]);

        // Verificar si el correo ya está registrado
        $email_validation = User::where('email', $request->email)->first();

        if (!empty($email_validation)) {
            // El correo ya está registrado
            session()->flash('message', 'Este correo ya se encuentra registrado, por favor inicie sesión');
            return redirect('/login');
        }

        $model = new User();
        $model->first_name = $request->first_name;
        $model->last_name = $request->last_name;
        $model->email = $request->email;
        $model->password = $request->password;
        $model->save();

        // Asignar rol de paciente
        $model->assignRole('paciente');

        $patient = new Patient();
        $patient->given_name = $request->first_name;
        $patient->family_name = $request->last_name;
        $patient->email = $request->email;
        $patient->phone = $request->full_phone;
        $patient->name = $request->first_name .' '. $request->last_name;
        $patient->user_id = $model->id;
        $patient->fhir_id = 'patient-' . Str::uuid();
        //$patient->identifier = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        if($patient->save()){

            session()->flash('message', 'Se ha registrado exitósamente el paciente');

            $registrationData=[
                'username'=>$model->email,
                'password'=>$request->password,
            ];

            //Mail::to($model)->send(new PatientWelcomeMail($patient,$client,$registrationData));
            $client = Client::find(1);
            Mail::to('rgasperi@smartcarebilling.com')->send(new PatientWelcomeMail($patient,$client,$registrationData,'patient'));

            $credentials = ([
                'email' => $model->email,
                'password' => $request->password,
            ]);
             if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $route=route('patient.dashboard');
                return redirect()->intended($route);
            }
        }

        session()->flash('message', 'Ha habido un error con el registro del paciente');

        return back();
    }

    public function profile(Request $request,$id){
        return view('patients.profile',compact('id'));
    }

    public function edit($id){

        $data = Patient::find($id);

        return view('patients.edit',compact('data'));
    }

    public function update(Request $request,$id){

        $model = Patient::find($id);
        $model->fill($request->all());

        if($model->save()){


            if($request->file('image')){
                $service = new FileService();
                $data['record_id'] = $model->id;
                $data['folder'] = 'patients';
                $data['type']='avatar';
                $service->guardarArchivos([$request->file('image')],$data);
            }
            $request->session()->flash('message.success','Actualización co exito.');
        }else{
            $request->session()->flash('message.success','Hubo un error y no se pudo actualizar.');
        }

        return redirect(route('patient.edit',array($id)));
    }

    public function destroy($id){

        $data = Patient::find($id);
        $data->delete();

        return redirect(route('patient.index'));
    }

    public function medicalHistory(Request $request,$id){

        return view('patients.medicalHistory.index',compact('id'));
    }

    public function check($idNumber){
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
            $client = UserClient::where('user_id',$patient->identifier)->first();
            $user_client= new PatientClinic();
            $user_client->patient_id = $patient->identifier;
            $user_client->client_id = $client->client_id;
            $user_client->save();

             return response()->json(['message' => 'Paciente asociado']);
        }
        return response()->json(['error' => 'Paciente no encontrado'], 404);
    }
}
