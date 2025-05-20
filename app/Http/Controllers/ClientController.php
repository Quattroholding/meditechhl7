<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\FileService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    //
    public function index(){
        $model = Client::class;

        return view('clients.index',compact('model'));
    }

    public function create(){
        return view('clients.create');
    }

    public function store(Request $request){
        //dd($request->all(), $request->logo, $request->file('logo'));
        $validated = $request->validate([
            'name' => 'required',
            'long_name' => 'required',
            'ruc' => 'required',
            'email' => 'required',
            'dv' => 'required',
            'whatsapp' => 'required',
            'logo' => 'required',

        ]);

        $model = new Client();
        $model->dv = $request->dv;
        $model->ruc = $request->ruc;
        $model->long_name = $request->long_name;
        $model->name=$request->name;
        $model->email = $request->email;
        $model->whatsapp = $request->whatsapp;
        $model->active = 1;
        $model->logo = 'clients/logo_'.time();
        
        if($model->save()){
            //SE GUARDA EL ARCHIVO DEL LOGO EN LA TABLA DE ARCHIVOS
            $service = new FileService();
        //$filename = 'client_logo_'.$model->id;
            $file = [$request->file('logo')]; 
            $data['folder'] = 'clients';
            $data['type'] ='img';
            $data['name'] ='logo_'.time();
            $data['record_id']= $model->id;
            $model->logo = $service->guardarArchivos($file,$data);
            $request->session()->flash('message.success','Cliente registrado con éxito.');
        }else{
            $request->session()->flash('message.success','Hubo un error y no se pudo actualizar.');
        }

        return redirect(route('client.index'));
    }

    public function edit($id){

        $data = Client::find($id);

        return view('clients.edit',compact('data'));
    }

    public function update(Request $request,$id){

        $model = Client::find($id);
        $model->fill($request->all());

        if($model->save()){


            if($request->file('logo')){
                $service = new FileService();
                $filename = 'client_logo_'.$model->id;
                $model->logo = $service->uploadSingleFile($request->file('logo'),'clients',$filename);
                $model->save();
            }
            $request->session()->flash('message.success','Actualización co exito.');
        }else{
            $request->session()->flash('message.success','Hubo un error y no se pudo actualizar.');
        }

        return redirect(route('client.edit',$id));
    }

    public function destroy($id){

        $data = Client::find($id);
        $data->delete();

        return redirect(route('client.index'));
    }
}
