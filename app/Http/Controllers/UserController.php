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
use Illuminate\Support\Facades\DB;
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

        try{
            DB::beginTransaction();

            $model = new User();
            $model->fill($request->all());
            $model->default_client_id =  $request->clients[0] ?? 1;

            if($model->save()){

                //SE ASIGNA EL ROL SEGÚN EL ID
                $rol = Rol::find($request->rol);
                $model->assignRole($rol->name);
                //SE ASOCIA EL USUARIO CON EL CLIENTE QUE SELECCIONÓ EN EL FORMULARIO

                if($request->has('clients')){
                    $model->clients()->sync($request->get('clients'));
                }

                if($request->file('avatar')){
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
                }

                DB::commit();

                $request->session()->flash('message.success','Usuario '.$rol->name.' registrado con éxito.');

                return redirect(route('user.index'));
            }
            else{
                $request->session()->flash('message.success','Hubo un error y no se pudo crear.');
            }
        }catch (\Exception $e){
            DB::rollBack();
            $request->session()->flash('message.error',$e->getMessage());
        }

        return redirect(route('user.create'));
    }

    public function edit($id){

        $data = User::find($id);

        if(auth()->user()->hasRole('asistente') && $id<>auth()->user()->id){
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        return view('users.edit',compact('data'));
    }

    public function update(Request $request,$id){
        $user = User::find($id);
        $fields = $request->except('id');
        if(empty($request->password))
            unset($fields['password']);
        $user->fill($fields);

        if($request->has('clients')){
            $user->clients()->sync($request->get('clients'));
        }

        if($request->file('avatar')){
            $service = new FileService();
            $filename = 'profile_picture_'.$user->id;
            $user->profile_picture = $service->uploadSingleFile($request->file('avatar'),'users',$filename);
        }
        $user->default_client_id =  $request->clients[0] ?? 1;
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

    public function changeClient($client_id){


        session(['client' => Client::find($client_id)]);

        session()->flash('message.succes','Client Actualizado con exito.');

        return back();
    }
}
