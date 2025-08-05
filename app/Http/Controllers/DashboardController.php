<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\UserWidgetPreference;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin(Request $request){
        $dashboard = array();
        return view('Dashboard.index',compact('dashboard'));
    }
    public function admin_client(Request $request){
        $dashboard = array();
        return view('Dashboard.index',compact('dashboard'));
    }
    public function doctor(Request $request){

        // Get visible widgets for this user
        $visibleWidgets = UserWidgetPreference::getVisibleWidgets(auth()->id(), 'doctor');

        $widgetComponents = [
            'recent-appointment-list' => 'doctor.recent-appointment-list',
            'new-patients' => 'doctor.new-patients',
            'old-patients' => 'doctor.old-patients',
            'active-patients' => 'doctor.active-patients',
            'patients-by-gender' => 'doctor.patients-by-gender',
            'top-active-conditions' => 'doctor.top-active-conditions',
            'top-prescribed-medications' => 'doctor.top-prescribed-medications',
            'consultation-effectiveness' => 'doctor.consultation-effectiveness',
            'activity-heatmap' => 'doctor.activity-heatmap',
        ];

        $widgetLayouts = [
            'recent-appointment-list' => 'col-lg-5',
            'patients-by-gender' => 'col-lg-7',
            'new-patients' => 'col-lg-4',
            'old-patients' => 'col-lg-4',
            'active-patients' => 'col-lg-43',

            'top-active-conditions' => 'col-lg-6',
            'top-prescribed-medications' => 'col-lg-6',
            'consultation-effectiveness' => 'col-lg-6',
            'activity-heatmap' => 'col-lg-6',
        ];

        return view('Dashboard.doctor-dashboard', compact('visibleWidgets', 'widgetComponents', 'widgetLayouts'));
    }
    public function patient(Request $request){
        $patient = Patient::find(auth()->user()->patient->id);
        $dashboards = array();

        // Get visible widgets for this user
        $visibleWidgets = UserWidgetPreference::getVisibleWidgets(auth()->id(), 'patient');

        $widgetComponents = [
            'overview' => 'patient.dashboard.overview',
            'upcoming-appointments' => 'patient.dashboard.upcoming-appointments',
            'recent-consultations' => 'patient.dashboard.recent-consultations',
            'outstanding-invoices' => 'patient.dashboard.outstanding-invoices',
            'medical-summary' => 'patient.dashboard.medical-summary',
        ];

        $widgetLayouts = [
            'overview' => 'col-12',
            'upcoming-appointments' => 'col-12 mb-4',
            'recent-consultations' => 'col-12 mb-4',
            'outstanding-invoices' => 'col-12 mb-4',
            'medical-summary' => 'col-12 mb-4',
        ];

        return view('Dashboard.patient-dashboard', compact('dashboards', 'patient', 'visibleWidgets', 'widgetComponents', 'widgetLayouts'));
    }
    public function assistence(Request $request){
        $dashboards = array();

        // Get visible widgets for this user
        $visibleWidgets = UserWidgetPreference::getVisibleWidgets(auth()->id(), 'assistant');

        $widgetComponents = [
            'recent-appointment-list' => 'doctor.recent-appointment-list',
            'new-patients' => 'doctor.new-patients',
            'old-patients' => 'doctor.old-patients',
            'active-patients' => 'doctor.active-patients',
            'patients-by-gender' => 'doctor.patients-by-gender',
            'consultation-effectiveness' => 'doctor.consultation-effectiveness',
            'activity-heatmap' => 'doctor.activity-heatmap',
        ];

        $widgetLayouts = [
            'recent-appointment-list' => 'col-lg-6',
            'new-patients' => 'col-lg-5',
            'old-patients' => 'col-lg-5',
            'active-patients' => 'col-lg-5',
            'patients-by-gender' => 'col-lg-7',
            'consultation-effectiveness' => 'col-lg-6',
            'activity-heatmap' => 'col-lg-6',
        ];

        return view('Dashboard.assistence-dashboard', compact('dashboards', 'visibleWidgets', 'widgetComponents', 'widgetLayouts'));
    }
}
