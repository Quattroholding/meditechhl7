<?php

namespace App\Enums;

enum AppointmentCancelledReason: string
{
    case PATIENT_REQUEST = 'Solicitud del paciente';
    case PATIENT_NO_SHOW = 'Paciente no se presentó';
    case DOCTOR_UNAVAILABLE = 'Doctor no disponible';
    case MEDICAL_EMERGENCY = 'Emergencia médica del doctor';
    case SCHEDULING_ERROR = 'Error en la programación';
    case PATIENT_ILLNESS = 'Enfermedad del paciente';
    case FACILITY_ISSUE = 'Problema en las instalaciones';
    case WEATHER_CONDITIONS = 'Condiciones climáticas adversas';
    case PATIENT_RESCHEDULED = 'Paciente solicitó reprogramar';
    case DOCTOR_RESCHEDULED = 'Doctor solicitó reprogramar';
    case OTHER = 'Otra razón';

    /**
     * Get all cancellation reasons as an array for dropdowns
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    /**
     * Get the Spanish label for the reason
     */
    public function label(): string
    {
        return $this->value;
    }
}
