<?php

namespace App\Http\Controllers;

use App\Models\Practitioner;
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
        dd($request->all());
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

        ]);
    }

    public function profile(Request $request,$id){
        $data = Practitioner::find($id);
        return view('practitioners.profile',compact('data'));
    }

    public function directory(){
        return view('practitioners.directory');
    }
}
