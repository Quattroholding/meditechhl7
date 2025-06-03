<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Encounter;
use App\Models\Patient;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin(Request $request){

        $dashboard = array();
        return view('dashboard',compact('dashboard'));
    }

    public function doctor(Request $request){

        $dashboards=array();

        $totApp = Appointment::count();
        $totAppFullFilled = Appointment::fullFilled()->count();
        $porcFullFilled = 0;//round($totAppFullFilled*100/$totApp,2);
        $classApp = 'status-pink';
        $signApp = '-';
        if($porcFullFilled>50){
            $classApp = 'status-green';
            $signApp = '+';
        }

        $dashboards[] = array(
            "title" =>__('appointment.title'),
            "icon" => "doctor-dash-01.svg",
            "count" =>$totAppFullFilled,
            "total" => "/".$totApp,
            "class" => $classApp,
            "percentageChange" =>$signApp.$porcFullFilled.'%');

        $totCon = Encounter::count();
        $totConCompleted = Encounter::finished()->count();
        $porcCompleted = 0;//round($totConCompleted*100/$totCon,2);
        $classCon = 'status-pink';
        $signCon = '-';
        if($porcCompleted>50){
            $classCon = 'status-green';
            $signCon = '+';
        }

        $dashboards[] = array(
            "title" => "Consultations",
            "icon" => "doctor-dash-02.svg",
            "count" =>$totConCompleted,
            "total" => "/".$totCon,
            "class" => $classCon,
            "percentageChange" =>$signCon.$porcCompleted.'%');

        $dashboards[] = array(
            "title" => "Ganacias",
            "icon" => "doctor-dash-04.svg",
            "count" => "530",
            "total" => "",
            "class" => "status-green",
            "percentageChange" => "+50%"
        );

        return view('Dashboard.doctor-dashboard',compact('dashboards'));
    }

    public function patient(Request $request){

        $patient = Patient::find(auth()->user()->patient->id);

        $dashboards=array();

        $totApp = Appointment::count();
        $totAppFullFilled = Appointment::fullFilled()->count();
        $porcFullFilled = 0;//round($totAppFullFilled*100/$totApp,2);
        $classApp = 'status-pink';
        $signApp = '-';
        if($porcFullFilled>50){
            $classApp = 'status-green';
            $signApp = '+';
        }

        $dashboards[] = array(
            "title" =>__('appointment.titles'),
            "icon" => "doctor-dash-01.svg",
            "count" =>$totAppFullFilled,
            "total" => "/".$totApp,
            "class" => $classApp,
            "percentageChange" =>$signApp.$porcFullFilled.'%');

        $totCon = Encounter::count();
        $totConCompleted = Encounter::finished()->count();
        $porcCompleted = 0;//round($totConCompleted*100/$totCon,2);
        $classCon = 'status-pink';
        $signCon = '-';
        if($porcCompleted>50){
            $classCon = 'status-green';
            $signCon = '+';
        }

        $dashboards[] = array(
            "title" => __('consultation.titles'),
            "icon" => "doctor-dash-02.svg",
            "count" =>$totConCompleted,
            "total" => "/".$totCon,
            "class" => $classCon,
            "percentageChange" =>$signCon.$porcCompleted.'%');

        $dashboards[] = array(
            "title" => "Gastos",
            "icon" => "doctor-dash-04.svg",
            "count" => "530",
            "total" => "",
            "class" => "status-green",
            "percentageChange" => "+50%"
        );


        return view('Dashboard.patient-dashboard',compact('dashboards','patient'));
    }
}
