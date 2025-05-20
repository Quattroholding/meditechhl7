<?php

namespace App\Http\Controllers;

use App\Models\RapidAccess;
use App\Models\UserProcedure;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function consultationTemplate(){
         return view('settings.consultation.create');
    }

    public function rapidAccess(){
        return view('settings.rapidAccess.create');
    }

    public function cptUser(Request $request){
        return view('settings.cpts.create');
    }

    public function workingHourUser(Request $request){
        return view('settings.working_hour.create');
    }

    public function createUserProcedure(Request $request){
        return view('settings.procedures.create');
    }
}
