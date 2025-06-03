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

    public function store(Request $request){
        dd($request->all());
       //dd($request->all(), $request->clients[0]);
        //$medical_speciality_name =  MedicalSpeciality::whereId($request->medical_speciality)->pluck('name')->first();
        //dd($request->medical_speciality,$medical_speciality_name);
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'clients' => 'required',
            'email' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required',
            'avatar' => 'required',
            'rol' => 'required'
        ]);

        //SE CREA EL USUARIO
        $model = new User();
        //$model->profile_picture = 'clients/avatar_'.time();
        $model->last_name = $request->last_name;
        $model->first_name=$request->first_name;
        $model->email = $request->email;
        $model->password = $request->password;
        $model->default_client_id =  $request->clients[0];

        if($model->save()){

        //SE ASOCIA EL USUARIO CON EL CLIENTE QUE SELECCIONÓ EN EL FORMULARIO
        /*$userclient = new UserClient();
        $userclient->user_id = $model->id;
        $userclient->client_id = $request->clients[0];
        $userclient->save();*/
        $clients = $request->clients;
        foreach($clients as $client){
            $userclient = new UserClient();
            $userclient->user_id = $model->id;
            $userclient->client_id = $client;
            $userclient->save();
        }
        //SE GUARDA EL ARCHIVO DEL LOGO EN LA TABLA DE ARCHIVOS
        //SE ASIGNA EL ROL SEGÚN EL ID
            $rol = Rol::find($request->rol);
            $model->assignRole($rol->name);
        //SE CREA EL DOCTOR EN LA TABLA DE PRACTITIONER
            $practitioner = new Practitioner();
            //LLamada a Faker para crear el número de identifier
            $faker = Faker::create();

            //FORMATEAR FECHA PARA ALMACENARLA EN LA BASE DE DATOS
            $fecha = DateTime::createFromFormat('d/m/Y', $request->birth_date);
            $fecha->setTime(0, 0, 0);
            $birthdate = $fecha->format('Y-m-d H:i:s');
            //ASIGNACIÓN DE DATOS AL MODELO
            $practitioner->fhir_id = 'practitioner-' . Str::uuid();
            $practitioner->user_id = $model->id;
            $practitioner->identifier = $faker->unique()->numerify('DOC#######');
            $practitioner->name = ($request->rol == 2) ? 'Dr. '.$request->first_name.' '.$request->last_name : $request->first_name.' '.$request->last_name;
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
        if($request->rol == 2){
           $qualifications = new PractitionerQualification();
           $qualifications->practitioner_id = $practitioner->id;
           $qualifications->code = $request->medical_speciality;
           $qualifications->medical_speciality_id = $request->medical_speciality;
         //SE BUSCA EL NOMBRE DE MEDICAL SPECIALITY
           $medical_speciality_name =  MedicalSpeciality::find($request->medical_speciality)->first();

           $qualifications->display = $medical_speciality_name->name;
           $qualifications->save();}

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
        }

        return redirect(route('client.index'));

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
