<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWidgetPreference extends Model
{
    protected $fillable = [
        'user_id',
        'dashboard_type',
        'widget_name',
        'widget_description',
        'is_visible',
        'order_position',
        'width',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'order_position' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaultWidgets(string $dashboardType): array
    {
        $widgets = [
            'doctor' => [
                'new-patients' => ['name' => 'New Patients', 'order' => 1, 'description' => 'Pacientes Nuevos', 'width' => 'col-lg-3'],
                // 'old-patients' => ['name' => 'Old Patients', 'order' => 2, 'description' => 'Pacientes Antiguos', 'width' => 'col-lg-3'],
                'active-patients' => ['name' => 'Active Patients', 'order' => 3, 'description' => 'Pacientes Activos', 'width' => 'col-lg-3'],
                'monthly-appointments' => ['name' => 'Monthly Appointments', 'order' => 4, 'description' => 'Citas del Mes', 'width' => 'col-lg-3'],
                'consultas-en-progreso' => ['name' => 'Consultas en Progreso', 'order' => 5, 'description' => 'Consultas en Progreso', 'width' => 'col-lg-3'],

                'yearly-appointments-chart' => ['name' => 'Yearly Appointments Chart', 'order' => 8, 'description' => 'Gráfico de Citas del Año', 'width' => 'col-lg-6'],
                'recent-appointment-list' => ['name' => 'Recent Appointments', 'order' => 9, 'description' => 'Citas para hoy', 'width' => 'col-lg-6'],

                'appointment-lead-time' => ['name' => 'Appointment Lead Time', 'order' => 6, 'description' => 'Tiempo Promedio: Solicitud → Atención', 'width' => 'col-lg-3'],
                'consultation-duration-time' => ['name' => 'Consultation Duration Time', 'order' => 7, 'description' => 'Duración Promedio de Consultas', 'width' => 'col-lg-3'],
                'patients-by-gender' => ['name' => 'Patients by Gender', 'order' => 10, 'description' => 'Pacientes por género', 'width' => 'col-lg-3'],
                'patients-by-age-block' => ['name' => 'Patients by Age Block', 'order' => 11, 'description' => 'Pacientes por Rango de Edad', 'width' => 'col-lg-3'],

                'top-active-conditions' => ['name' => 'Top Active Conditions', 'order' => 12, 'description' => 'Top 5 condicones activas', 'width' => 'col-lg-6'],
                'top-prescribed-medications' => ['name' => 'Top Prescribed Medications', 'order' => 13, 'description' => 'Top 5 medicamentos prescritos', 'width' => 'col-lg-6'],

                'consultation-effectiveness' => ['name' => 'Consultation Effectiveness', 'order' => 14, 'description' => 'Efectividad de Atención', 'width' => 'col-lg-6'],
                'activity-heatmap' => ['name' => 'Activity Heatmap', 'order' => 15, 'description' => 'Horarios de Mayor Actividad', 'width' => 'col-lg-6'],

                'branch-billing-chart' => ['name' => 'Branch Billing Chart', 'order' => 16, 'description' => 'Facturación por Sede', 'width' => 'col-lg-12'],
                'billing-collection-rate' => ['name' => 'Billing Collection Rate', 'order' => 17, 'description' => 'Tasa de Facturación vs Cobros', 'width' => 'col-lg-12'],
                'diagnostics-by-specialties' => ['name' => 'Diagnostics by Specialties', 'order' => 18, 'description' => 'Diagnósticos más frecuentes por especialidad', 'width' => 'col-lg-3'],
            ],
            'assistant' => [
                'new-patients' => ['name' => 'New Patients', 'order' => 1, 'description' => 'Pacientes Nuevos', 'width' => 'col-lg-4'],
                // 'old-patients' => ['name' => 'Old Patients', 'order' => 2, 'description' => 'Pacientes Antiguos', 'width' => 'col-lg-4'],
                'active-patients' => ['name' => 'Active Patients', 'order' => 3, 'description' => 'Pacientes Activos', 'width' => 'col-lg-4'],
                'recent-appointment-list' => ['name' => 'Recent Appointments', 'order' => 4, 'description' => 'Citas para hoy', 'width' => 'col-lg-6'],
                'patients-by-gender' => ['name' => 'Patients by Gender', 'order' => 5, 'description' => 'Pacientes por género', 'width' => 'col-lg-6'],
                'consultation-effectiveness' => ['name' => 'Consultation Effectiveness', 'order' => 6, 'description' => 'Efectividad de Atención', 'width' => 'col-lg-6'],
                'activity-heatmap' => ['name' => 'Activity Heatmap', 'order' => 7, 'description' => 'Horarios de Mayor Actividad', 'width' => 'col-lg-6'],
            ],
            'patient' => [
                'overview' => ['name' => 'Health Overview', 'order' => 1, 'description' => 'General', 'width' => 'col-lg-12'],
                'upcoming-appointments' => ['name' => 'Upcoming Appointments', 'order' => 2, 'description' => 'Proximas citas', 'width' => 'col-lg-6'],
                'recent-consultations' => ['name' => 'Recent Consultations', 'order' => 3, 'description' => 'Consultas recientes', 'width' => 'col-lg-6'],
                'outstanding-invoices' => ['name' => 'Outstanding Invoices', 'order' => 4, 'description' => 'Facturas pendientes', 'width' => 'col-lg-6'],
                'medical-summary' => ['name' => 'Medical Summary', 'order' => 5, 'description' => 'Resumen medico', 'width' => 'col-lg-6'],
            ],
        ];

        return $widgets[$dashboardType] ?? [];
    }

    public static function getVisibleWidgets(int $userId, string $dashboardType): array
    {
        $defaultWidgets = self::getDefaultWidgets($dashboardType);
        $userPreferences = self::where('user_id', $userId)
            ->where('dashboard_type', $dashboardType)
            ->get()
            ->keyBy('widget_name');

        $visibleWidgets = collect($defaultWidgets)->map(function ($widget, $key) use ($userPreferences) {
            $preference = $userPreferences->get($key);

            return [
                'key' => $key,
                'name' => $widget['name'],
                'is_visible' => $preference ? $preference->is_visible : true,
                'order_position' => $preference ? $preference->order_position : $widget['order'],
                'width' => $preference ? $preference->width : $widget['width'],
            ];
        })->filter(function ($widget) {
            return $widget['is_visible'];
        })->sortBy('order_position')->values()->toArray();

        return $visibleWidgets;
    }
}
