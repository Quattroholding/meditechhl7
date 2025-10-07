<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\UserWidgetPreference;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        $dashboard = [];

        return view('Dashboard.index', compact('dashboard'));
    }

    public function admin_client(Request $request)
    {
        $dashboard = [];

        return view('Dashboard.index', compact('dashboard'));
    }

    public function doctor(Request $request)
    {

        // Get visible widgets for this user
        $visibleWidgets = UserWidgetPreference::getVisibleWidgets(auth()->id(), 'doctor');

        $widgetComponents = [
            'recent-appointment-list' => 'doctor.recent-appointment-list',
            'new-patients' => 'doctor.new-patients',
            'old-patients' => 'doctor.old-patients',
            'active-patients' => 'doctor.active-patients',
            'monthly-appointments' => 'doctor.monthly-appointments',
            'consultas-en-progreso' => 'doctor.consultas-en-progreso',
            'patients-by-gender' => 'doctor.patients-by-gender',
            'patients-by-age-block' => 'doctor.patients-by-age-block',
            'top-active-conditions' => 'doctor.top-active-conditions',
            'top-prescribed-medications' => 'doctor.top-prescribed-medications',
            'consultation-effectiveness' => 'doctor.consultation-effectiveness',
            'activity-heatmap' => 'doctor.activity-heatmap',
        ];

        return view('Dashboard.doctor-dashboard', compact('visibleWidgets', 'widgetComponents'));
    }

    public function patient(Request $request)
    {
        $patient = Patient::find(auth()->user()->patient->id);

        // Get visible widgets for this user
        $visibleWidgets = UserWidgetPreference::getVisibleWidgets(auth()->id(), 'patient');

        $widgetComponents = [
            'overview' => 'patient.dashboard.overview',
            'upcoming-appointments' => 'patient.dashboard.upcoming-appointments',
            'recent-consultations' => 'patient.dashboard.recent-consultations',
            'outstanding-invoices' => 'patient.dashboard.outstanding-invoices',
            'medical-summary' => 'patient.dashboard.medical-summary',
        ];

        return view('Dashboard.patient-dashboard', compact('patient', 'visibleWidgets', 'widgetComponents'));
    }

    public function assistence(Request $request)
    {
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

        return view('Dashboard.assistence-dashboard', compact('visibleWidgets', 'widgetComponents'));
    }
}
