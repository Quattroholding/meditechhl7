<?php

namespace App\Services\Reports;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Practitioner;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AppointmentsReport extends BaseReport
{
    protected string $name = 'appointments';

    protected string $title = 'Historial de Citas';

    protected string $description = 'Reporte completo de citas médicas con filtros personalizables';

    protected array $roles = ['admin', 'doctor', 'asistente'];

    /**
     * Obtener los datos del reporte aplicando filtros
     */
    public function getData(array $filters = []): Collection
    {
        $query = Appointment::with([
            'patient:id,name,identifier',
            'practitioner:id,name',
            'medicalSpeciality:id,name',
            'consultingRoom:id,name',
        ])->orderBy('created_at', 'desc');

        // Aplicar filtros por rol (automático)
        $query = $this->applyRoleFilters($query);

        // Aplicar filtros del usuario
        $query = $this->applyUserFilters($query, $filters);

        return $query->get();
    }

    /**
     * Aplicar filtros basados en el rol del usuario
     */
    protected function applyRoleFilters($query)
    {
        $user = auth()->user();

        // Si es doctor, solo ver sus propias citas
        if ($user->hasRole('doctor')) {
            $practitionerId = $user->practitioner->id ?? null;
            if ($practitionerId) {
                $query->where('practitioner_id', $practitionerId);
            }
        }

        // Si es paciente, solo ver sus citas
        if ($user->hasRole('paciente')) {
            $patientId = $user->patient->id ?? null;
            if ($patientId) {
                $query->where('patient_id', $patientId);
            }
        }

        // Admin ve todas las citas sin restricciones

        return $query;
    }

    /**
     * Aplicar filtros proporcionados por el usuario
     */
    protected function applyUserFilters($query, array $filters)
    {
        // Filtro por rango de fechas de creación
        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filtro por paciente (solo para admin y asistente)
        if (! empty($filters['patient_id']) && ! auth()->user()->hasRole('doctor')) {
            $query->where('patient_id', $filters['patient_id']);
        }

        // Filtro por practitioner (solo para admin y asistente, doctor ya está pre-filtrado)
        if (! empty($filters['practitioner_id']) && ! auth()->user()->hasRole('doctor')) {
            $query->where('practitioner_id', $filters['practitioner_id']);
        }

        // Filtro por estatus (todos los roles)
        if (! empty($filters['status'])) {
            if (is_array($filters['status'])) {
                $query->whereIn('status', $filters['status']);
            } else {
                $query->where('status', $filters['status']);
            }
        }

        // Filtro por especialidad
        if (! empty($filters['medical_speciality_id'])) {
            $query->where('medical_speciality_id', $filters['medical_speciality_id']);
        }

        return $query;
    }

    /**
     * Obtener los filtros disponibles según el rol
     */
    public function getAvailableFilters(): array
    {
        $user = auth()->user();

        $filters = [
            'date_from' => [
                'type' => 'date',
                'label' => 'Fecha de Solicitud - Desde',
                'required' => false,
                'default' => Carbon::now()->subMonth()->format('Y-m-d'),
            ],
            'date_to' => [
                'type' => 'date',
                'label' => 'Fecha de Solicitud - Hasta',
                'required' => false,
                'default' => Carbon::now()->format('Y-m-d'),
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Estatus de la Cita',
                'options' => [
                    'proposed' => 'Propuesto',
                    'pending' => 'Pendiente',
                    'booked' => 'Reservado',
                    'arrived' => 'Llegó',
                    'fulfilled' => 'Cumplido',
                    'cancelled' => 'Cancelado',
                    'noshow' => 'No Presentó',
                    'entered-in-error' => 'Error de Captura',
                    'checked-in' => 'Registrado',
                    'waitlist' => 'Lista de Espera',
                ],
                'required' => false,
                'multiple' => true,
            ],
        ];

        // Filtros adicionales solo para admin y asistente (no para doctor)
        if ($user->hasRole('admin') || $user->hasRole('asistente')) {
            $filters['patient_id'] = [
                'type' => 'select',
                'label' => 'Paciente',
                'options' => $this->getPatientOptions(),
                'required' => false,
                'searchable' => true,
            ];

            $filters['practitioner_id'] = [
                'type' => 'select',
                'label' => 'Médico',
                'options' => $this->getPractitionerOptions(),
                'required' => false,
                'searchable' => true,
            ];
        }

        return $filters;
    }

    /**
     * Obtener opciones de pacientes para el select
     */
    protected function getPatientOptions(): array
    {
        return Patient::orderBy('name')
            ->limit(100)
            ->get()
            ->mapWithKeys(function ($patient) {
                return [$patient->id => $patient->profile_name.' - '.$patient->identifier_value];
            })
            ->toArray();
    }

    /**
     * Obtener opciones de médicos para el select
     */
    protected function getPractitionerOptions(): array
    {
        return Practitioner::orderBy('name')
            ->get()
            ->mapWithKeys(function ($practitioner) {
                return [$practitioner->id => $practitioner->profile_name];
            })
            ->toArray();
    }

    /**
     * Obtener las columnas para exportación a Excel
     */
    public function getExcelColumns(): array
    {
        return [
            'ID',
            'Fecha de Solicitud',
            'Fecha/Hora de Cita',
            'Tipo Servicio',
            'Paciente',
            'Identificación',
            'Médico',
            'Especialidad',
            'Consultorio',
            'Estatus',
            'Duración (min)',
            'Descripcion',

        ];
    }

    /**
     * Mapear datos para Excel
     */
    public function mapDataForExcel($appointment): array
    {
        return [
            $appointment->id,
            $appointment->created_at ? $appointment->created_at : 'N/A',
            $appointment->start ? $appointment->start->format('d/m/Y H:i') : 'N/A',
            $appointment->service_type ?? '',
            $appointment->patient->name ?? 'N/A',
            $appointment->patient->identifier ?? 'N/A',
            $appointment->practitioner->name ?? 'N/A',
            $appointment->medicalSpeciality->name ?? 'N/A',
            $appointment->consultingRoom->name ?? 'N/A',
            $this->getStatusLabel($appointment->status),
            $appointment->minutes_duration ?? 'N/A',
            $appointment->description ?? 'N/A',

        ];
    }

    /**
     * Obtener etiqueta legible del estatus
     */
    protected function getStatusLabel(string $status): string
    {
        $labels = [
            'proposed' => 'Propuesto',
            'pending' => 'Pendiente',
            'booked' => 'Reservado',
            'arrived' => 'Llegó',
            'fulfilled' => 'Cumplido',
            'cancelled' => 'Cancelado',
            'noshow' => 'No Presentó',
            'entered-in-error' => 'Error de Captura',
            'checked-in' => 'Registrado',
            'waitlist' => 'Lista de Espera',
        ];

        return $labels[$status] ?? $status;
    }
}
