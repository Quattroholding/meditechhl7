<?php

namespace App\Http\Controllers;

use App\Models\MedicalLeave;

class MedicalLeaveVerificationController extends Controller
{
    public function verify(string $verificationHash)
    {
        $medicalLeave = MedicalLeave::where('verification_hash', $verificationHash)->first();

        if (! $medicalLeave) {
            return view('medical-leave-verification.not-found', [
                'message' => 'La incapacidad médica no fue encontrada en el sistema.',
            ]);
        }

        $medicalLeave->load(['patient', 'practitioner.specialties', 'client']);

        $isValid = true;
        $status = 'Válida';
        $statusColor = 'success';

        // Validar si está dentro del período
        $now = now();
        if ($now < $medicalLeave->start_datetime) {
            $status = 'No vigente aún';
            $statusColor = 'warning';
            $isValid = false;
        } elseif ($now > $medicalLeave->end_datetime) {
            $status = 'Incapacidad vencida';
            $statusColor = 'danger';
            $isValid = false;
        }

        return view('medical-leave-verification.show', [
            'medicalLeave' => $medicalLeave,
            'isValid' => $isValid,
            'status' => $status,
            'statusColor' => $statusColor,
            'patientName' => $medicalLeave->patient_name ?? $medicalLeave->patient->full_name,
            'doctorName' => $medicalLeave->practitioner_name ?? $medicalLeave->practitioner->full_name,
            'doctorLicense' => $medicalLeave->practitioner_license_number ?? $medicalLeave->practitioner->license_number,
            'specialty' => $medicalLeave->practitioner?->specialties?->first()?->name ?? 'Medicina General',
            'startDate' => $medicalLeave->start_datetime->format('d/m/Y'),
            'endDate' => $medicalLeave->end_datetime->format('d/m/Y'),
            'totalDays' => $medicalLeave->total_days,
            'diagnosis' => $medicalLeave->diagnosis ?? 'Diagnóstico médico',
            'clinicName' => $medicalLeave->clinic_name ?? $medicalLeave->client->name,
        ]);
    }
}
