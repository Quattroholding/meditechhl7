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

    }

    public function profile(Request $request,$id){
        $data = Practitioner::find($id);
        return view('practitioners.profile',compact('data'));
    }
}
