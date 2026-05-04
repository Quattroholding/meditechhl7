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
        'position',
        'height',
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
                'new-patients' => ['name' => 'New Patients', 'order' => 1, 'description' => 'Pacientes Nuevos', 'width' => 'col-lg-3', 'position' => 'a1:c1'],
                'active-patients' => ['name' => 'Active Patients', 'order' => 3, 'description' => 'Pacientes Activos',  'width' => 'col-lg-3', 'position' => 'd1:f1'],
                'monthly-appointments' => ['name' => 'Monthly Appointments', 'order' => 4, 'description' => 'Citas del Mes',  'width' => 'col-lg-3', 'position' => 'g1:i1'],
                'consultas-en-progreso' => ['name' => 'Consultas en Progreso', 'order' => 5, 'description' => 'Consultas en Progreso',  'width' => 'col-lg-3', 'position' => 'j1:l1'],
                'yearly-appointments-chart' => ['name' => 'Yearly Appointments Chart', 'order' => 8, 'description' => 'Gráfico de Citas del Año', 'width' => 'col-lg-6', 'position' => 'a2:f2'],
                'recent-appointment-list' => ['name' => 'Recent Appointments', 'order' => 9, 'description' => 'Citas para hoy', 'width' => 'col-lg-6', 'position' => 'g2:l2'],
                'appointment-lead-time' => ['name' => 'Appointment Lead Time', 'order' => 6, 'description' => 'Tiempo Promedio: Solicitud → Atención', 'width' => 'col-lg-3', 'position' => 'a3:c3'],
                'consultation-duration-time' => ['name' => 'Consultation Duration Time', 'order' => 7, 'description' => 'Duración Promedio de Consultas', 'width' => 'col-lg-3', 'position' => 'd3:f3'],
                'patients-by-gender' => ['name' => 'Patients by Gender', 'order' => 10, 'description' => 'Pacientes por género', 'width' => 'col-lg-3', 'position' => 'g3:i3'],
                'patients-by-age-block' => ['name' => 'Patients by Age Block', 'order' => 11, 'description' => 'Pacientes por Rango de Edad', 'width' => 'col-lg-3', 'position' => 'j3:l3'],
                'top-active-conditions' => ['name' => 'Top Active Conditions', 'order' => 12, 'description' => 'Top 5 condicones activas', 'width' => 'col-lg-6', 'position' => 'a4:f4'],
                'top-prescribed-medications' => ['name' => 'Top Prescribed Medications', 'order' => 13, 'description' => 'Top 5 medicamentos prescritos', 'width' => 'col-lg-6', 'position' => 'g4:l4'],
                'consultation-effectiveness' => ['name' => 'Consultation Effectiveness', 'order' => 14, 'description' => 'Efectividad de Atención', 'width' => 'col-lg-6', 'position' => 'a5:f5'],
                'activity-heatmap' => ['name' => 'Activity Heatmap', 'order' => 15, 'description' => 'Horarios de Mayor Actividad', 'width' => 'col-lg-6', 'position' => 'g5:l5'],
                'branch-billing-chart' => ['name' => 'Branch Billing Chart', 'order' => 16, 'description' => 'Facturación por Sede', 'width' => 'col-lg-12', 'position' => 'a6:l6'],
                'billing-collection-rate' => ['name' => 'Billing Collection Rate', 'order' => 17, 'description' => 'Tasa de Facturación vs Cobros', 'width' => 'col-lg-12', 'position' => 'a7:l7'],
                'diagnostics-by-age-groups' => ['name' => 'Diagnostics by Age Groups', 'order' => 19, 'description' => 'Diagnósticos más frecuentes por  grupo etario', 'width' => 'col-lg-12', 'position' => 'a8:l8'],
            ],
            'recepcionist' => [
                'monthly-appointments' => ['name' => 'Monthly Appointments', 'order' => 4, 'description' => 'Citas del Mes', 'width' => 'col-lg-3', 'position' => 'a1:f2'],
                'consultas-en-progreso' => ['name' => 'Consultas en Progreso', 'order' => 5, 'description' => 'Consultas en Progreso', 'width' => 'col-lg-3', 'position' => 'g1:l2'],
                'yearly-appointments-chart' => ['name' => 'Yearly Appointments Chart', 'order' => 8, 'description' => 'Gráfico de Citas del Año', 'width' => 'col-lg-6', 'position' => 'a3:f3'],
                'recent-appointment-list' => ['name' => 'Recent Appointments', 'order' => 9, 'description' => 'Citas para hoy', 'width' => 'col-lg-6', 'position' => 'g3:l3'],
                'consultation-effectiveness' => ['name' => 'Consultation Effectiveness', 'order' => 14, 'description' => 'Efectividad de Atención', 'width' => 'col-lg-6', 'position' => 'a4:f4'],
                'activity-heatmap' => ['name' => 'Activity Heatmap', 'order' => 15, 'description' => 'Horarios de Mayor Actividad', 'width' => 'col-lg-6', 'position' => 'g4:l4'],
            ],
            'patient' => [
                'overview' => ['name' => 'Health Overview', 'order' => 1, 'description' => 'General', 'width' => 'col-lg-12'],
                'upcoming-appointments' => ['name' => 'Upcoming Appointments', 'order' => 2, 'description' => 'Proximas citas', 'width' => 'col-lg-6'],
                'recent-consultations' => ['name' => 'Recent Consultations', 'order' => 3, 'description' => 'Consultas recientes', 'width' => 'col-lg-6'],
                'outstanding-invoices' => ['name' => 'Outstanding Invoices', 'order' => 4, 'description' => 'Facturas pendientes', 'width' => 'col-lg-6'],
                'medical-summary' => ['name' => 'Medical Summary', 'order' => 5, 'description' => 'Resumen medico', 'width' => 'col-lg-6'],
            ],
            'registro medico' => [
                'new-patients' => ['name' => 'New Patients', 'order' => 1, 'description' => 'Pacientes Nuevos', 'width' => 'col-lg-3', 'position' => 'a1:c1'],
                'active-patients' => ['name' => 'Active Patients', 'order' => 3, 'description' => 'Pacientes Activos',  'width' => 'col-lg-3', 'position' => 'd1:f1'],
                'monthly-appointments' => ['name' => 'Monthly Appointments', 'order' => 4, 'description' => 'Citas del Mes',  'width' => 'col-lg-3', 'position' => 'g1:i1'],
                'consultas-en-progreso' => ['name' => 'Consultas en Progreso', 'order' => 5, 'description' => 'Consultas en Progreso',  'width' => 'col-lg-3', 'position' => 'j1:l1'],
                'yearly-appointments-chart' => ['name' => 'Yearly Appointments Chart', 'order' => 8, 'description' => 'Gráfico de Citas del Año', 'width' => 'col-lg-6', 'position' => 'a2:f2'],
                'recent-appointment-list' => ['name' => 'Recent Appointments', 'order' => 9, 'description' => 'Citas para hoy', 'width' => 'col-lg-6', 'position' => 'g2:l2'],
                'appointment-lead-time' => ['name' => 'Appointment Lead Time', 'order' => 6, 'description' => 'Tiempo Promedio: Solicitud → Atención', 'width' => 'col-lg-3', 'position' => 'a3:c3'],
                'consultation-duration-time' => ['name' => 'Consultation Duration Time', 'order' => 7, 'description' => 'Duración Promedio de Consultas', 'width' => 'col-lg-3', 'position' => 'd3:f3'],
                'patients-by-gender' => ['name' => 'Patients by Gender', 'order' => 10, 'description' => 'Pacientes por género', 'width' => 'col-lg-3', 'position' => 'g3:i3'],
                'patients-by-age-block' => ['name' => 'Patients by Age Block', 'order' => 11, 'description' => 'Pacientes por Rango de Edad', 'width' => 'col-lg-3', 'position' => 'j3:l3'],
                //'top-active-conditions' => ['name' => 'Top Active Conditions', 'order' => 12, 'description' => 'Top 5 condicones activas', 'width' => 'col-lg-6', 'position' => 'a4:f4'],
                //'top-prescribed-medications' => ['name' => 'Top Prescribed Medications', 'order' => 13, 'description' => 'Top 5 medicamentos prescritos', 'width' => 'col-lg-6', 'position' => 'g4:l4'],
                'consultation-effectiveness' => ['name' => 'Consultation Effectiveness', 'order' => 14, 'description' => 'Efectividad de Atención', 'width' => 'col-lg-6', 'position' => 'a5:f5'],
                'activity-heatmap' => ['name' => 'Activity Heatmap', 'order' => 15, 'description' => 'Horarios de Mayor Actividad', 'width' => 'col-lg-6', 'position' => 'g5:l5'],
                //'branch-billing-chart' => ['name' => 'Branch Billing Chart', 'order' => 16, 'description' => 'Facturación por Sede', 'width' => 'col-lg-12', 'position' => 'a6:l6'],
                //'billing-collection-rate' => ['name' => 'Billing Collection Rate', 'order' => 17, 'description' => 'Tasa de Facturación vs Cobros', 'width' => 'col-lg-12', 'position' => 'a7:l7'],

            ],
        ];

        return $widgets[$dashboardType] ?? [];
    }

    /**
     * Convierte el ancho (width) antiguo a posición de Spatie Dashboard
     */
    public static function widthToSpatieWidth(string $width): int
    {
        return match ($width) {
            'col-lg-3' => 3,  // 1/4 del grid (12 columnas)
            'col-lg-4' => 4,  // 1/3 del grid
            'col-lg-6' => 6,  // 1/2 del grid
            'col-lg-12' => 12, // Ancho completo
            default => 6
        };
    }

    /**
     * Genera la posición en formato Spatie basado en el order y width
     */
    public static function generateSpatiePosition(int $order, string $width, int $height = 1): string
    {
        $widthCols = self::widthToSpatieWidth($width);
        $row = (int) ceil($order / (12 / $widthCols));

        // Calcular la columna de inicio
        $widgetsPerRow = 12 / $widthCols;
        $posInRow = (($order - 1) % $widgetsPerRow);
        $startCol = ($posInRow * $widthCols) + 1;
        $endCol = $startCol + $widthCols - 1;

        // Convertir a letras (a=1, b=2, etc.)
        $startLetter = chr(96 + $startCol); // 96 + 1 = 97 = 'a'
        $endLetter = chr(96 + $endCol);

        $endRow = $row + $height - 1;

        return "{$startLetter}{$row}:{$endLetter}{$endRow}";
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
            $width = $preference ? $preference->width : $widget['width'];
            $order = $preference ? $preference->order_position : $widget['order'];
            $height = $preference ? ($preference->height ?? 1) : ($widget['height'] ?? 1);

            // Prioridad de posiciones:
            // 1. Posición guardada en preferencias del usuario
            // 2. Posición definida en default widgets
            // 3. Generar posición automáticamente
            $position = $preference && $preference->position
                ? $preference->position
                : ($widget['position'] ?? self::generateSpatiePosition($order, $width, $height));

            return [
                'key' => $key,
                'name' => $widget['name'],
                'is_visible' => $preference ? $preference->is_visible : true,
                'order_position' => $order,
                'width' => $width,
                'position' => $position,
                'height' => $height,
            ];
        })->filter(function ($widget) {
            return $widget['is_visible'];
        })->sortBy('order_position')->values()->toArray();

        return $visibleWidgets;
    }
}
