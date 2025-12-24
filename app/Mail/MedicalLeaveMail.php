<?php

namespace App\Mail;

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
                'practitionerName' => $this->medicalLeave->practitioner_name,
                'identifier' => $this->medicalLeave->identifier,
                'totalDays' => $this->medicalLeave->total_days,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generar PDF
        $pdf = Pdf::loadView('pdf.medical-leave', [
            'medicalLeave' => $this->medicalLeave,
        ]);

        // Crear archivo temporal
        $filename = 'licencia-medica-'.$this->medicalLeave->identifier.'.pdf';
        $pdfContent = $pdf->output();

        return [
            Attachment::fromData(fn () => $pdfContent, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
