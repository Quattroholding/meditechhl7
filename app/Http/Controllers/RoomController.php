<?php

namespace App\Http\Controllers;

use App\Models\ConsultingRoom;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(){
        $model = ConsultingRoom::class;

        return view('clients.rooms.index',compact('model'));
    }
    public function create(){
        return view('clients.rooms.create');
    }

    public function store(Request $request){
        //dd($request->all());
        $validated = $request->validate([
            "branch_id" => "required",
            "name" => "required",
            "number" => "required",
            "floor" => "required"
        ]);
        $model = new ConsultingRoom();
        $model->branch_id = $request->branch_id;
        $model->name = $request->name;
        $model->number = $request->number;
        $model->floor = $request->floor;

        if($model->save()){
            $request->session()->flash('message.success','Consultorio registrado con éxito.');
        }else{
            $request->session()->flash('message.success','Hubo un error y no se pudo crear el consultorio.');
        }

        return redirect(route('client.index'));
    }

    public function edit($id){

        $data = ConsultingRoom::find($id);

        return view('clients.rooms.edit',compact('data'));
    }

    public function update(Request $request,$id){

        $validated = $request->validate([
            "branch_id" => "required",
            "name" => "required",
            "number" => "required",
            "floor" => "required"
        ]);
        $model = ConsultingRoom::findOrFail($id);
        $model->branch_id = $request->branch_id;
        $model->name = $request->name;
        $model->number = $request->number;
        $model->floor = $request->floor;

        if($model->save()){
            $request->session()->flash('message.success','Consultorio actualizado con éxito.');
        }else{
            $request->session()->flash('message.success','Hubo un error y no se pudo crear el consultorio.');
        }

        return redirect(route('client.room.edit',$id));
    }

    public function destroy($id){

        $data = ConsultingRoom::find($id);
        $data->delete();

        return redirect(route('clients.index'));
    }
}
