<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\ConsultationData;
use App\Models\ConsultationField;
use App\Models\ConsultationFieldTemplate;
use App\Models\Encounter;
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

        /*
        $consultation_template = ConsultationFieldTemplate::whereUserId(Auth::getUser()->id)->pluck('consultation_field_id');
        $info = ConsultationField::when($consultation_template->count()>0,function ($q) use($consultation_template){
            $q->whereIn('id',$consultation_template);
        })->get();

        foreach ($info as $d){

            $template[$d->section][$d->id]['input'] = $d;

            $template[$d->section][$d->id]['data']=new ConsultationData();
            $data = ConsultationData::whereConsultationId($consultation->id)->whereConsultationFieldId($d->id)->get();
            if($data) $template[$d->section][$d->id]['data'] = $data;
        }
        */

        //dd($template);

       $secciones=[
           1=>'Queja Principal',
           2=>'Signos Vitales',
           3=>'Enfermedad Actual',
           4=>'Examen Físico',
           5=>'Diagnosticos',
           6=>'Laboratorios',
           7=>'Imagenes',
           8=>'Procedimientos',
           9=>'Referencia Especialista',
           10=>'Medicamentos'
       ];

        return view('consultations.create',compact('consultation','appointment','patient','secciones'));
    }
}
