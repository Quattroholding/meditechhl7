<?php

namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\User;
use App\Models\UserClient;
use App\Models\PractitionerQualification;
use App\Models\Practitioner;
use App\Models\MedicalSpeciality;
use App\Services\FileService;
use Illuminate\Support\Str;
use DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PractitionerController extends Controller
{
    public function index(){
        $model = Practitioner::class;

        return view('practitioners.index',compact('model'));
    }

    public function create(){
        $clients = Client::whereIn('id',auth()->user()->clients->pluck('id'))->pluck('id','name')->toArray();
        return view('practitioners.create',compact('clients'));
    }

     public function edit($id){
        $data = Practitioner::find($id);
        //dd($data->specialties);
        $practitioner_clients = $data->user->clients->pluck('id')->toArray();
        $specialties = $data->qualifications->pluck('medical_speciality_id')->toArray();

        //dd($qualifications, $practitioner_clients);
        return view('practitioners.edit', compact('data', 'practitioner_clients', 'specialties'));
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
        /*foreach($clients as $client){
            $userclient = new UserClient();
            $userclient->user_id = $model->id;
            $userclient->client_id = $client;
            $userclient->save();
        }*/
        $sync=[];
        foreach($clients as $client){
            $sync[$client]=array('client_id' => $client, 'created_at' => Carbon::now()->format('Y-m-d H:i:s'), 'updated_at' => Carbon::now()->format('Y-m-d H:i:s'), 'user_id' => $model->id);
        }
        $model->clients()->sync($sync);

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
                    $userclient = UserClient::whereClientId($client)->whereUserId($model->id)->first();
                    if ($userclient) {
                        $userclient->delete();
                    }
                }
                $user = User::find($model->id);
                $user->delete();

                $request->session()->flash('message.error','Hubo un error y no se pudo crear.');
        }
        //SE CREA EL REGISTRO EN PRACTITIONER_QUALIFICATIONS
           /*$qualifications = new PractitionerQualification();
           $qualifications->practitioner_id = $practitioner->id;S
           $qualifications->code = $request->medical_speciality[0];
           $qualifications->medical_speciality_id = $request->medical_speciality[0];
         //SE BUSCA EL NOMBRE DE MEDICAL SPECIALITY
           $medical_speciality_name =  MedicalSpeciality::find($request->medical_speciality[0]);

           $qualifications->display = $medical_speciality_name->name;*/
           $specialties = $request->medical_speciality;
            $syn=[];
            foreach($specialties as $speciality){
            $medical_speciality_name =  MedicalSpeciality::find($speciality);
            $syn[$speciality]=array('code' => $speciality, 'medical_speciality_id' => $speciality, 'created_at' => Carbon::now()->format('Y-m-d H:i:s'), 'updated_at' => Carbon::now()->format('Y-m-d H:i:s'), 'practitioner_id' => $practitioner->id, 'display' => $medical_speciality_name->name);
            }
            $practitioner->specialties()->sync($syn);
           /*if($qualifications->save()){
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
        }*/
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
                $userclient = UserClient::whereClientId($client)->whereUserId($model->id)->first();
                if ($userclient) {
                    $userclient->delete();
                }
                }

                $user = User::find($model->id);
                $user->delete();

                $request->session()->flash('message.error','Hubo un error y no se pudo almacenar la imagen.');
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

    public function update(Request $request,$id){

        //FORMATEAR FECHA PARA ALMACENARLA EN LA BASE DE DATOS
        $fecha = DateTime::createFromFormat('d/m/Y', $request->date);
        $fecha->setTime(0, 0, 0);
        $birthdate = $fecha->format('Y-m-d H:i:s');

        $practitioner = Practitioner::find($id);
        $user_id = $practitioner->user->id;
        $practitioner->identifier = $request->id_number;
        $practitioner->name = 'Dr. '.$request->first_name.' '.$request->last_name;
        $practitioner->given_name = $request->first_name;
        $practitioner->family_name = $request->last_name;
        $practitioner->gender = $request->gender;
        $practitioner->birth_date = $birthdate;
        $practitioner->address = $request->physical_address;
        $practitioner->email = $request->email;
        $practitioner->phone = $request->full_phone;

        if($practitioner->save()){

        //SE ASOCIA EL USUARIO CON EL CLIENTE QUE SELECCIONÓ EN EL FORMULARIO
            $clients = $request->clients;
           /* foreach($clients as $client){
                $existing_clients = UserClient::whereCode($speciality)->whereUserId($user_id)->first();
                if(!$existing_clients){
                $userclient = new UserClient();
                $userclient->user_id = $user_id;
                $userclient->client_id = $client;
                $userclient->save();}
            }*/
            $sync=[];
            foreach($clients as $client){
            $sync[$client]=array('client_id' => $client, 'created_at' => Carbon::now()->format('Y-m-d H:i:s'), 'updated_at' => Carbon::now()->format('Y-m-d H:i:s'), 'user_id' => $user_id);
            }
            $practitioner->user->clients()->sync($sync);

            //SE ASOCIA LAS ESPECIALIDADES DEL USUARIO QUE SELECCIONÓ EN EL FORMULARIO
            $specialties = $request->medical_speciality;
            /*foreach($specialties as $speciality){
                $existing_spepeciality = PractitionerQualification::whereCode($speciality)->wherePractitionerId($practitioner->id)->first();
                if(!$existing_spepeciality){
                $qualification = new PractitionerQualification();
                $qualification->practitioner_id = $practitioner->id;
                $qualification->code = $speciality;
                $qualification->medical_speciality_id = $speciality;
                $medical_speciality_name =  MedicalSpeciality::find($speciality);
                $qualification->display = $medical_speciality_name->name;
                $qualification->save();}
            }*/
            $syn=[];
            foreach($specialties as $speciality){
            $medical_speciality_name =  MedicalSpeciality::find($speciality);
            $syn[$speciality]=array('code' => $speciality, 'medical_speciality_id' => $speciality, 'created_at' => Carbon::now()->format('Y-m-d H:i:s'), 'updated_at' => Carbon::now()->format('Y-m-d H:i:s'), 'practitioner_id' => $practitioner->id, 'display' => $medical_speciality_name->name);
            }
            $practitioner->specialties()->sync($syn);

            if($request->file('image')){
                $service = new FileService();
                $data['record_id'] = $practitioner->id;
                $data['folder'] = 'patients';
                $data['type']='avatar';
                $service->guardarArchivos([$request->file('image')],$data);
            }
            $request->session()->flash('message.success','Actualización con éxito.');
        }else{
            $request->session()->flash('message.success','Hubo un error y no se pudo actualizar.');
        }

        return redirect(route('practitioner.edit',array($id)));
    }


    public function profile(Request $request,$id){
        $data = Practitioner::find($id);
        return view('practitioners.profile',compact('data'));
    }

    public function directory(){
        return view('practitioners.directory');
    }
}
