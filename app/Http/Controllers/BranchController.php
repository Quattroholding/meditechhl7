<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{

    public function create(){
        return view('clients.branchs.create');
    }

    public function store(Request $request){
        //dd($request->all());
        $validated = $request->validate([
            "client_id" => "required",
            "name" => "required",
            "phone" => "required",
            "full_phone" => "required",
            "address" => "required",
            "type" => "required"
        ]);

        $model = new Branch();
        $model->client_id = $request->client_id;
        $model->name = $request->name;
        $model->phone = $request->full_phone;
        $model->address = $request->address;
        $model->type = $request->type;
        $model->active = 1;

        if($model->save()){
            $request->session()->flash('message.success','Sucursal registrada con éxito.');
        }else{
            $request->session()->flash('message.success','Hubo un error y no se pudo crear la sucursal.');
        }

        return redirect(route('client.index'));
    }

    public function edit($id){

        $data = Branch::find($id);

        return view('clients.branchs.edit',compact('data'));
    }

    public function update(Request $request,$id){

    }

    public function destroy($id){

        $data = Branch::find($id);
        $data->delete();

        return redirect(route('clients.index'));
    }
}
