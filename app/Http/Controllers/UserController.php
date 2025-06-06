<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Patient;
use App\Models\User;
use App\Models\Rol;
use App\Models\UserClient;
use App\Models\PractitionerQualification;
use App\Http\Requests\UserFormRequest;
use App\Models\Practitioner;
use App\Models\MedicalSpeciality;
use App\Services\FileService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;

use Faker\Factory as Faker;


class UserController extends Controller
{
    public function index(){

        $model = User::class;

        return view('users.index',compact('model'));
    }

    public function create(){

        $selected_clients = UserClient::whereUserId(auth()->user()->id)->pluck('client_id')->toArray();

        if(auth()->user()->can('clientes')){
            $clients = Client::pluck('name','id')->toArray();
        }else{
            $clients = Client::whereIn('id',UserClient::whereUserId(auth()->user()->id)->pluck('client_id'))->pluck('name','id')->toArray();
        }
        return view('users.create',compact('clients','selected_clients'));
    }

    public function store(UserFormRequest $request){
        //dd($request->all());
        //SE CREA EL USUARIO
        //dd(empty($request->clients));
        $model = new User();
        //$model->profile_picture = 'clients/avatar_'.time();
        $model->last_name = $request->last_name;
        $model->first_name=$request->first_name;
        $model->email = $request->email;
        $model->password = $request->password;
        $model->default_client_id =  $request->clients[0] ?? 1;

        if($model->save()){

        //SE ASOCIA EL USUARIO CON EL CLIENTE QUE SELECCIONÓ EN EL FORMULARIO
        
        if($request->has('clients')){
            $clients = $request->clients;
            foreach($clients as $client){
                if($client!=null){
                $userclient = new UserClient();
                $userclient->user_id = $model->id;
                $userclient->client_id = $client;
                $userclient->save();}
            }}
         //SE BUSCA EL REGISTRO PARA ASIGNAR EL NOMBRE DEL LOGO
            $user_profile = User::find($model->id);
        //SE GUARDA EL AVATAR EN LA TABLA DE ARCHIVOS
            $service = new FileService();
            $file = $request->file('avatar');
            $data['folder'] = 'users';
            $data['type'] ='img';
            $data['name'] ='user_'.time();
            $data['record_id']= $model->id;
            $user_profile->profile_picture = $service->uploadSingleFile($file,'users',$data['name']);
            if($user_profile->save()){
                $request->session()->flash('message.success','Usuario registrado con éxito.');
            }else{
                foreach($clients as $client){
                $userclient = UserClient::whereClientId($client)->whereUserId($model->id)->first();
                if($userclient)
                $userclient->delete();
                }

            $user = User::find($model->id);
            $user->delete();

                $request->session()->flash('message.error','Hubo un error y no se pudo la foto perfil del usuario.');
                return redirect(route('user.create'));
            }
            if (!$user_profile->profile_picture) {
                session()->flash('message.error', 'Hubo un error al subir el logo.');
                return redirect(route('user.create'));
            }
        
        //SE GUARDA EL ARCHIVO DEL LOGO EN LA TABLA DE ARCHIVOS
    //FORMATEAR FECHA PARA ALMACENARLA EN LA BASE DE DATOS
    if($request->birth_date){
            $fecha = DateTime::createFromFormat('d/m/Y', $request->birth_date);
            $fecha->setTime(0, 0, 0);
            $birthdate = $fecha->format('Y-m-d H:i:s');}
        //SE ASIGNA EL ROL SEGÚN EL ID
            $rol = Rol::find($request->rol);
            $model->assignRole($rol->name);
        switch($request->rol){
            case '2':
                //SE CREA EL DOCTOR EN LA TABLA DE PRACTITIONER
                $practitioner = new Practitioner();
                //ASIGNACIÓN DE DATOS AL MODELO
                $practitioner->fhir_id = 'practitioner-' . Str::uuid();
                $practitioner->user_id = $model->id;
                $practitioner->identifier = $request->id_number;
                $practitioner->name = 'Dr.'.$request->first_name.' '.$request->last_name;
                $practitioner->given_name = $request->first_name;
                $practitioner->family_name = $request->last_name;
                $practitioner->gender = $request->gender;
                $practitioner->birth_date = $birthdate;
                $practitioner->address = $request->address;
                $practitioner->email = $request->email;
                $practitioner->phone = $request->phone;
                $practitioner->active = 1;
                $practitioner->save();
                //SE CREA EL REGISTRO EN PRACTITIONER_QUALIFICATIONS
                $medical_specialties = $request->medical_speciality;
                foreach($medical_specialties as $speciality){
                    $qualifications = new PractitionerQualification();
                    $qualifications->practitioner_id = $practitioner->id;
                    $qualifications->code = $speciality;
                    $qualifications->medical_speciality_id = $speciality;
                    //SE BUSCA EL NOMBRE DE MEDICAL SPECIALITY
                    $medical_speciality_name =  MedicalSpeciality::find($speciality)->first();
                    $qualifications->display = $medical_speciality_name->name;
                    $qualifications->save();
            }
                $request->session()->flash('message.success','Personal Médico registrado con éxito.');
                break;
            case '3':
                break;
            case '4':
                $patient = new Patient();
                $patient->given_name = $request->first_name;
                $patient->family_name = $request->last_name;
                $patient->email = $request->email;
                $patient->phone = $request->full_phone;
                $patient->name = $request->first_name .' '. $request->last_name;
                $patient->user_id = $model->id;
                $patient->gender = $request->gender;
                $patient->fhir_id = 'patient-' . Str::uuid();
                $patient->identifier_type = $request->id_type;
                $patient->identifier = $request->id_number;  
                $patient->birth_date = $birthdate;  
                $patient->address = $request->address;
                $patient->marital_status = $request->marital_status;
                $patient->save();
                $request->session()->flash('message.success','Paciente registrado con éxito.');
                break;
        }
        
       /*    
        //SE GUARDA EL AVATAR EN LA TABLA DE ARCHIVOS
            $service = new FileService();
            $file = [$request->file('avatar')];
            $data['folder'] = 'users';
            $data['type'] ='img';
            $data['name'] ='avatar_'.time();
            $data['record_id']= $model->id;
            $service->guardarArchivos($file,$data);
            $request->session()->flash('message.success','Personal Médico registrado con éxito.');
        }else{
            $request->session()->flash('message.success','Hubo un error y no se pudo crear.');
        }*/
        $request->session()->flash('message.success','Usuario '.$rol->name.' registrado con éxito.');
        }else{
            $request->session()->flash('message.success','Hubo un error y no se pudo crear.');
        }
        return redirect(route('user.create'));

    }

    public function edit($id){

        $data = User::find($id);

        return view('users.edit',compact('data'));
    }

    public function update(Request $request,$id){
        $user = User::find($id);
        $fields = $request->except('id');
        if(empty($request->password))
            unset($fields['password']);
        $user->fill($fields);

        if($request->file('avatar')){
            $service = new FileService();
            $filename = 'profile_picture_'.$user->id;
            $user->profile_picture = $service->uploadSingleFile($request->file('avatar'),'users',$filename);
        }

        if($user->save()){
            $request->session()->flash('message.suucess','Actualizado con exito');
        }else{
            $request->session()->flash('message.error','¡Error!, este usuario no se puede actualizar.');
        }

        return redirect(route('user.edit',$id));
    }

    public function destroy($id){

        if(auth()->user()->id ==$id){
            session()->flash('message.error','Este usuario no se puede borrar.');
            return redirect()->back();
        }
        $data = User::find($id);
        $data->delete();

        return redirect(route('user.index'));
    }
}
