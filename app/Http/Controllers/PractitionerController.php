<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\UserClient;
use App\Models\PractitionerQualification;
use App\Models\Practitioner;
use App\Models\MedicalSpeciality;
use App\Services\FileService;
use Illuminate\Support\Str;
use DateTime;
use Illuminate\Http\Request;

class PractitionerController extends Controller
{
    public function index(){
        $model = Practitioner::class;

        return view('practitioners.index',compact('model'));
    }

    public function create(){
        return view('practitioners.create');
    }

     public function edit($id){
        $data = Practitioner::find($id);
        return view('practitioners.edit', compact('data'));
    }
    public function store(Request $request){
        //dd($request->all());
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'id_type' => 'required',
            'id_number' => 'required',
            'email' => 'required',
            'gender' => 'required',
            'date' => 'required',
            'physical_address' => 'required',
            'phone' => 'required',
            'full_phone' => 'required',
            'image' => 'required',
            'clients' => 'required'

        ]);
        //VALIDAR SI EL CORREO YA EXISTE EN EL SISTEMA
        $email_validation = User::whereEmail($request->email)->first();
                if (!empty($email_validation)) {
            // El correo ya está registrado
            session()->flash('message.error', 'Este correo ya se encuentra registrado, por favor inicie sesión');
            return redirect(route('practitioner.create'));
        }
        //SE CREA EL USUARIO
        $model = new User();
        $model->last_name = $request->last_name;
        $model->first_name=$request->first_name;
        $model->email = $request->email;
        //------------HACER UN PASSWORD GENÉRICO PARA ENVIAR CORREO A USUARIO
        $model->password = 'test';
        //---------MANEJAR MULTIPLES CLIENTES - ARREGLAR
        $model->default_client_id =  $request->clients[0];
        if($model->save()){
        //SE ASOCIA EL USUARIO CON EL CLIENTE QUE SELECCIONÓ EN EL FORMULARIO
        $clients = $request->clients;
        foreach($clients as $client){
            $userclient = new UserClient();
            $userclient->user_id = $model->id;
            $userclient->client_id = $client;
            $userclient->save();
        }
        
        //SE GUARDA EL ARCHIVO DEL LOGO EN LA TABLA DE ARCHIVOS
        //SE ASIGNA EL ROL SEGÚN EL ID
            $model->assignRole('doctor');
        //SE CREA EL DOCTOR EN LA TABLA DE PRACTITIONER
            $practitioner = new Practitioner();
            //FORMATEAR FECHA PARA ALMACENARLA EN LA BASE DE DATOS
            $fecha = DateTime::createFromFormat('d/m/Y', $request->date);
            $fecha->setTime(0, 0, 0);
            $birthdate = $fecha->format('Y-m-d H:i:s');
            //ASIGNACIÓN DE DATOS AL MODELO
            $practitioner->fhir_id = 'practitioner-' . Str::uuid();
            $practitioner->user_id = $model->id;
            $practitioner->identifier = $request->id_number;
            $practitioner->name = 'Dr. '.$request->first_name.' '.$request->last_name;
            $practitioner->given_name = $request->first_name;
            $practitioner->family_name = $request->last_name;
            $practitioner->gender = $request->gender;
            $practitioner->birth_date = $birthdate;
            $practitioner->address = $request->physical_address;
            $practitioner->email = $request->email;
            $practitioner->phone = $request->full_phone;
            $practitioner->active = 1;
            if($practitioner->save()){  
            $request->session()->flash('message.success','Personal Médico registrado con éxito.');
            }else{
            foreach($clients as $client){
                $userclient = UserClient::find($client);
                $userclient->delete();
            }
            $user = User::find($model->id);
            $user->delete();

            $request->session()->flash('message.error','Hubo un error y no se pudo crear.');
        }
        //SE CREA EL REGISTRO EN PRACTITIONER_QUALIFICATIONS
           $qualifications = new PractitionerQualification();
           $qualifications->practitioner_id = $practitioner->id;
           //----------------------------MEJORAR PARA TRABAJAR CON MULTIPLES MEDICAL SPECIALITIES
           $qualifications->code = $request->medical_speciality[0];
           $qualifications->medical_speciality_id = $request->medical_speciality[0];
         //SE BUSCA EL NOMBRE DE MEDICAL SPECIALITY
           $medical_speciality_name =  MedicalSpeciality::find($request->medical_speciality[0]);

           $qualifications->display = $medical_speciality_name->name;
           if($qualifications->save()){
                $request->session()->flash('message.success','Personal Médico registrado con éxito.');
            }else{
            $doc = Practitioner::find($practitioner->id);
            $doc->delete();
            foreach($clients as $client){
                $userclient = UserClient::find($client);
                $userclient->delete();
            }

            $user = User::find($model->id);
            $user->delete();

            $request->session()->flash('message.error','Hubo un error y no se pudo crear.');
            return redirect(route('practitioner.create'));
        }
        //SE BUSCA EL REGISTRO PARA ASIGNAR EL NOMBRE DEL LOGO
            $user_profile = User::find($model->id);
        //SE GUARDA EL AVATAR EN LA TABLA DE ARCHIVOS
            $service = new FileService();
            $file = $request->file('image');
            $data['folder'] = 'users';
            $data['type'] ='img';
            $data['name'] ='practitioner_'.time();
            $data['record_id']= $model->id;
            $user_profile->profile_picture = $service->uploadSingleFile($file,'practitioners',$data['name']);
            if($user_profile->save()){
                $request->session()->flash('message.success','Usuario admin client registrado con éxito.');
            }else{
                $qual = PractitionerQualification::find($qualifications->id);
                $qual->delete();
                $doc = Practitioner::find($practitioner->id);
                $doc->delete();
                foreach($clients as $client){
                $userclient = UserClient::find($client);
                $userclient->delete();
                }

            $user = User::find($model->id);
            $user->delete();

                $request->session()->flash('message.error','Hubo un error y no se pudo crear el usuario administrador de la empresa.');
                return redirect(route('practitioner.create'));
            }
            if (!$user_profile->profile_picture) {
                session()->flash('message.error', 'Hubo un error al subir el logo.');
                return redirect(route('practitioner.create'));
            }
            $request->session()->flash('message.success','Personal Médico registrado con éxito.');
        }else{
            $request->session()->flash('message.error','Hubo un error y no se pudo crear.');
        }

        return redirect(route('practitioner.create'));

    }

    public function profile(Request $request,$id){
        $data = Practitioner::find($id);
        return view('practitioners.profile',compact('data'));
    }

    public function directory(){
        return view('practitioners.directory');
    }
}
