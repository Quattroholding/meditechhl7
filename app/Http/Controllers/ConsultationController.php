<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\ConsultationData;
use App\Models\ConsultationField;
use App\Models\ConsultationFieldTemplate;
use App\Models\Encounter;
use App\Models\EncounterSection;
use App\Models\EncounterTemplate;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    public function show(Request $request,$appointment_id){


        $template=array();
        $client_id = auth()->user()->clients()->first()->id;
        $appointment=Appointment::find($appointment_id);
        $patient = Patient::find($appointment->patient_id);

        $consultation = Encounter::whereAppointmentId($appointment_id)->first();

        if(!$consultation){
            $consultation =  Encounter::create([ 'fhir_id' => 'encounter-' . fake()->uuid(),
                'patient_id' => $appointment->patient_id,
                'practitioner_id' => $appointment->practitioner_id,
                'appointment_id' => $appointment->id,
                'identifier' => 'ENC-' . fake()->unique()->numerify('#######'),
                'status' =>'in-progress',
                'class' => 'IMP',
                'type' => 'IMP',
                'priority' => 'routine',
                'start' => now(),
                'end' => now()]);
        }

        $encounter_sections_user = EncounterTemplate::whereUserId(Auth::getUser()->id)->pluck('encounter_section_id');

        if(!$encounter_sections_user){
            $encounter_sections = EncounterSection::get();
        }else{
            $encounter_sections = EncounterSection::whereIn('id',$encounter_sections_user)->get();
        }

       $secciones= $encounter_sections->pluck('name_esp','id');

        return view('consultations.create',compact('consultation','appointment','patient','encounter_sections','secciones'));
    }
}
