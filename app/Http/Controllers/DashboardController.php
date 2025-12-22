<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\UserWidgetPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Initialize default widgets for first-time users
     */
    protected function initializeDefaultWidgets($userId, $dashboardType)
    {
        // Check if user already has preferences
        $hasPreferences = UserWidgetPreference::where('user_id', $userId)
            ->where('dashboard_type', $dashboardType)
            ->exists();

        // If no preferences exist, create them from defaults
        if (! $hasPreferences) {
            $defaultWidgets = UserWidgetPreference::getDefaultWidgets($dashboardType);

            foreach ($defaultWidgets as $key => $widget) {
                UserWidgetPreference::create([
                    'user_id' => $userId,
                    'dashboard_type' => $dashboardType,
                    'widget_name' => $key,
                    'widget_description' => $widget['description'],
                    'is_visible' => true,
                    'order_position' => $widget['order'],
                    'width' => $widget['width'],
                    'position' => $widget['position'] ?? UserWidgetPreference::generateSpatiePosition(
                        $widget['order'],
                        $widget['width'],
                        $widget['height'] ?? 1
                    ),
                    'height' => $widget['height'] ?? 1,
                ]);
            }

            Log::info('Default widgets initialized', [
                'user_id' => $userId,
                'dashboard_type' => $dashboardType,
                'widgets_count' => count($defaultWidgets),
            ]);
        }
    }

    public function admin(Request $request)
    {
        $dashboard = [];

        return view('Dashboard.index', compact('dashboard'));
    }

    public function admin_client(Request $request)
    {
        $dashboard = [];

        // Si no tiene el parámetro show_salute y es la primera visita, redirigir con el parámetro
        /* if (! $request->has('show_salute') && ! session()->has('dashboard_client_visited')) {
             session()->put('dashboard_client_visited', true);

             return redirect()->route('client.dashboard', ['show_salute' => 'true']);
         }*/

        // Redirect
        if (! session()->has('dashboard_client_visited')) {
            session()->put('dashboard_client_visited', true);

            // Solo redirigir si NO tiene el parámetro
            if (! $request->has('show_salute')) {
                return redirect()->route('client.dashboard', ['show_salute' => 'true']);
            }
        }

        return view('livewire.client.dashboard', compact('dashboard'));
    }

    public function doctor(Request $request)
    {
        // Si no tiene el parámetro show_salute y es la primera visita, redirigir con el parámetro
        /*if (! $request->has('show_salute') && ! session()->has('dashboard_doctor_visited')) {
            session()->put('dashboard_doctor_visited', true);

            return redirect()->route('doctor.dashboard', ['show_salute' => 'true']);
        }*/
        // Lógica simplificada de primera visita
        $isFirstVisit = ! session()->has('dashboard_doctor_visited');

        if ($isFirstVisit) {
            session()->put('dashboard_doctor_visited', true);

            // Solo redirigir si no tiene el parámetro
            if (! $request->has('show_salute')) {
                return redirect()->route('doctor.dashboard', ['show_salute' => 'true']);
            }
        }

        // Initialize default widgets if user has no preferences
        $this->initializeDefaultWidgets(auth()->id(), 'doctor');

        // Get visible widgets for this user
        $visibleWidgets = UserWidgetPreference::getVisibleWidgets(auth()->id(), 'doctor');

        $widgetComponents = [
            'recent-appointment-list' => 'doctor.recent-appointment-list',
            'new-patients' => 'doctor.new-patients',
            'old-patients' => 'doctor.old-patients',
            'active-patients' => 'doctor.active-patients',
            'monthly-appointments' => 'doctor.monthly-appointments',
            'consultas-en-progreso' => 'doctor.consultas-en-progreso',
            'appointment-lead-time' => 'doctor.appointment-lead-time',
            'consultation-duration-time' => 'doctor.consultation-duration-time',
            'yearly-appointments-chart' => 'doctor.yearly-appointments-chart',
            'patients-by-gender' => 'doctor.patients-by-gender',
            'patients-by-age-block' => 'doctor.patients-by-age-block',
            'top-active-conditions' => 'doctor.top-active-conditions',
            'top-prescribed-medications' => 'doctor.top-prescribed-medications',
            'consultation-effectiveness' => 'doctor.consultation-effectiveness',
            'activity-heatmap' => 'doctor.activity-heatmap',
            'branch-billing-chart' => 'dashboard.admin.branch-billing-chart',
            'billing-collection-rate' => 'dashboard.admin.billing-collection-rate',
            // 'diagnostics-by-specialties' => 'doctor.diagnostics-by-specialties',
            'diagnostics-by-age-groups' => 'doctor.diagnostics-by-age-groups',
        ];

        // dd($visibleWidgets);
        return view('Dashboard.doctor-dashboard', compact('visibleWidgets', 'widgetComponents'));
    }

    public function patient(Request $request)
    {
        $patient = Patient::find(auth()->user()->patient->id);

        // Initialize default widgets if user has no preferences
        $this->initializeDefaultWidgets(auth()->id(), 'patient');

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
        // Initialize default widgets if user has no preferences
        $this->initializeDefaultWidgets(auth()->id(), 'recepcionist');

        // Get visible widgets for this user
        $visibleWidgets = UserWidgetPreference::getVisibleWidgets(auth()->id(), 'recepcionist');

        $widgetComponents = [
            'recent-appointment-list' => 'doctor.recent-appointment-list',
            'monthly-appointments' => 'doctor.monthly-appointments',
            'consultas-en-progreso' => 'doctor.consultas-en-progreso',
            'yearly-appointments-chart' => 'doctor.yearly-appointments-chart',
            'consultation-effectiveness' => 'doctor.consultation-effectiveness',
            'activity-heatmap' => 'doctor.activity-heatmap',
        ];

        return view('Dashboard.assistence-dashboard', compact('visibleWidgets', 'widgetComponents'));
    }

    public function accounting(Request $request)
    {
        $dashboard = [];

        return view('Dashboard.contabilidad-dashboard', compact('dashboard'));
    }
}
