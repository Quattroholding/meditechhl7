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
use App\Models\PresentIllnesType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ConsultationController extends Controller
{

    public function index(){
        return view('consultations.index');
    }
    public function show(Request $request,$appointment_id){


        $template=array();
        $client_id = auth()->user()->clients()->first()->id;
        $appointment=Appointment::find($appointment_id);
        $patient = Patient::find($appointment->patient_id);

        $consultation = Encounter::whereAppointmentId($appointment_id)->first();

        $type ='4525004'; // consulta de medicina general
        if($appointment->medical_speciality_id <> '50')   $type ='26172008'; // consulta de especialidad

        if(!$consultation){
            $consultation =  Encounter::create([ 'fhir_id' => 'encounter-' . fake()->uuid(),
                'patient_id' => $appointment->patient_id,
                'practitioner_id' => $appointment->practitioner_id,
                'appointment_id' => $appointment->id,
                'identifier' => 'ENC-' . fake()->unique()->numerify('#######'),
                'status' =>'in-progress',
                'class' => 'SS',
                'type' =>$type,
                'priority' => 'routine',
                'start' => now(),
                'medical_speciality_id'=>$appointment->medical_speciality_id,
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

    public function finished(Request $request,$appointment_id){
        $appointment = Appointment::find($appointment_id);
        $appointment->status = 'fulfilled';
        $appointment->save();
        $encounter = Encounter::whereAppointmentId($appointment->id)->first();
        $encounter->status = 'finished';
        $encounter->end = now();
        $encounter->save();

        session()->flash('message.success', '¡Consulta finalizada con exito!');

        return redirect(route('consultation.index'));
    }

    public function downloadResumen(Request $request,$appointment_id){

        $appointment = Appointment::find($appointment_id);
        $data = Encounter::whereAppointmentId($appointment_id)->first();
        $data['consultation-list'] = PresentIllnesType::get();
        $consultation_disabilities = array();
        $lang='esp';
        $home_visit=false;
        $sello=$firma='';
        $mode='full';
        foreach ($data->diagnoses()->get() as $d){
            array_push($consultation_disabilities, "<td>".$d->condition->code."</td><td>".$d->condition->icd10Code->description_es."</td>");
        }

        if($request->has('html')) return view('consultations.consultation_report.index', compact('data','lang','home_visit','sello','firma','mode','consultation_disabilities'));

        $pdf = Pdf::loadView('consultations.consultation_report.index', compact('data','lang','home_visit','sello','firma','mode','consultation_disabilities'));

        return $pdf->stream('resumen.pdf');
    }
}
