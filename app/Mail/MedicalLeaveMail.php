<?php

namespace App\Mail;

use App\Models\ClientPreference;
use App\Models\MedicalLeave;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MedicalLeaveMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public MedicalLeave $medicalLeave
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Incapacidad Médica - '.$this->medicalLeave->identifier,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.medical-leave',
            with: [
                'medicalLeave' => $this->medicalLeave,
                'patientName' => $this->medicalLeave->patient_name,
                'patientIdentifier' => $this->medicalLeave->patient->identifier,
                'practitionerName' => $this->medicalLeave->practitioner_name,
                'identifier' => $this->medicalLeave->identifier,
                'totalDays' => $this->medicalLeave->total_days,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        // Cargar relaciones necesarias
        $this->medicalLeave->load(['patient', 'practitioner.specialties', 'client']);

        // Obtener el template seleccionado para el cliente
        $template = ClientPreference::getMedicalLeaveTemplate($this->medicalLeave->client_id);

        // Preparar datos para el template
        $branch = (object) [
            'name' => $this->medicalLeave->clinic_name ?? $this->medicalLeave->client->name ?? 'Clínica',
            'address' => $this->medicalLeave->clinic_address ?? '',
            'phone' => $this->medicalLeave->clinic_phone ?? '',
            'email' => $this->medicalLeave->client->email ?? '',
        ];

        $patient = (object) [
            'full_name' => $this->medicalLeave->patient_name ?? $this->medicalLeave->patient->full_name,
            'identifier_value' => $medicalLeave->patient->identifier ?? '',
            'birth_date' => $this->medicalLeave->patient->birth_date,
            'age' => $this->medicalLeave->patient->age ?? null,
        ];

        // Obtener la primera especialidad del médico si existe
        $specialty = 'Medicina General';
        if ($this->medicalLeave->practitioner && $this->medicalLeave->practitioner->specialties->isNotEmpty()) {
            $specialty = $this->medicalLeave->practitioner->specialties->first()->name;
        }

        $doctor = (object) [
            'full_name' => $this->medicalLeave->practitioner_name ?? $this->medicalLeave->practitioner->full_name,
            'specialty' => $specialty,
            'license' => $this->medicalLeave->practitioner_license_number ?? $this->medicalLeave->practitioner->license_number ?? '',
        ];

        $diagnosis = $this->medicalLeave->diagnosis ?? 'Diagnóstico médico';
        $startDate = $this->medicalLeave->start_datetime->format('d/m/Y');
        $endDate = $this->medicalLeave->end_datetime->format('d/m/Y');
        $days = $this->medicalLeave->total_days;
        $city = $this->medicalLeave->client->city ?? 'Ciudad';
        $issueDate = $this->medicalLeave->issue_date->format('d/m/Y');
        $documentNumber = $this->medicalLeave->identifier;
        $firma = $this->medicalLeave->getPractitionerFilePathAttribute('signature') ?? '';
        $sello = $this->medicalLeave->getPractitionerFilePathAttribute('seal') ?? '';

        // Variables adicionales para template 2 (formato horizontal)
        $start_time = $this->medicalLeave->start_datetime->format('H:i');
        $start_day = $this->medicalLeave->start_datetime->format('d');
        $start_month = $this->medicalLeave->start_datetime->format('m');
        $start_year = $this->medicalLeave->start_datetime->format('Y');
        $end_time = $this->medicalLeave->end_datetime->format('H:i');
        $end_day = $this->medicalLeave->end_datetime->format('d');
        $end_month = $this->medicalLeave->end_datetime->format('m');
        $end_year = $this->medicalLeave->end_datetime->format('Y');
        $logo = $this->medicalLeave->client->logo;

        $data = [
            'branch' => $branch,
            'patient' => $patient,
            'doctor' => $doctor,
            'diagnosis' => $diagnosis,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'start_time' => $start_time,
            'start_day' => $start_day,
            'start_month' => $start_month,
            'start_year' => $start_year,
            'end_time' => $end_time,
            'end_day' => $end_day,
            'end_month' => $end_month,
            'end_year' => $end_year,
            'days' => $days,
            'city' => $city,
            'issueDate' => $issueDate,
            'documentNumber' => $documentNumber,
            'medicalLeave' => $this->medicalLeave, // Mantener por compatibilidad
            'firma' => $firma,
            'sello' => $sello,
            'logo' => $logo,
        ];

        // Generar PDF con el template seleccionado
        $pdf = Pdf::loadView("templates.medical_leave.{$template}", $data);

        // Crear archivo temporal
        $filename = 'licencia-medica-'.$this->medicalLeave->identifier.'.pdf';
        $pdfContent = $pdf->output();

        return [
            Attachment::fromData(fn () => $pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
