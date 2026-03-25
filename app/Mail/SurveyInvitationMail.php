<?php

namespace App\Mail;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SurveyInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public SurveyResponse $surveyResponse,
        public Encounter $encounter,
        public Patient $patient,
        public Survey $survey
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Encuesta de Satisfacción - '.config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.survey-invitation',
            with: [
                'surveyUrl' => route('survey.public', $this->surveyResponse->token),
                'patientName' => $this->patient->full_name,
                'surveyTitle' => $this->survey->title,
                'practitionerName' => $this->encounter->practitioner->full_name ?? 'Su médico',
                'encounterDate' => $this->encounter->start->format('d/m/Y'),
                'clinicName' => config('app.name'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
