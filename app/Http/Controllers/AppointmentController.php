<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Practitioner;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{

    public function index(Request $request){
        $model = Appointment::class;
        return view('appointments.index',compact('model'));
    }

    public function calendar(Request $request){
        return view('appointments.calendar.index');
    }

    public function updateStatus(Appointment $appointment, Request $request)
    {
        $validStatuses = ['booked', 'arrived', 'fulfilled', 'cancelled', 'noshow'];

        if(in_array($request->status, $validStatuses)) {
            $appointment->update(['status' => $request->status]);

            // Si tienes integración FHIR, aquí podrías sincronizar el cambio
            // $this->syncWithFhirServer($appointment);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    public function create(){
        return view('appointments.create');
    }

    public function store(Request $request){

       $appointment= New Appointment();
       $fields=$request->except('_token');
       $appointment->fill($fields);
       $appointment->status='P';
       $appointment->save();

       return redirect(route('consultation.create',['id'=>$appointment->id]));
    }

    public function edit($id){

        $data = Client::findOFail($id);

        return view('clients.edit',compact('data'));
    }

    public function update(Request $request,$id){

    }


}
