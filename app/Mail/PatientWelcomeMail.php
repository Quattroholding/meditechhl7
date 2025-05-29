<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PatientWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $patient;
    public $registrationData;

    public $client;
    public $clinicInfo;

    /**
     * Create a new message instance.
     */
    public function __construct(Patient $patient,Client $client, array $registrationData = [])
    {
        $this->patient = $patient;
        $this->client = $client;
        $this->registrationData = $registrationData;
        $this->clinicInfo = [
            'name' => $this->client->name,
            'address' => '',
            'phone' => $this->client->whatsapp,
            'email' => $this->client->email,
            'website' => config('app.url')
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido a ' . env('APP_NAME') . '! - Registro Completado',
            from: config('mail.from.address'),
            replyTo: $this->clinicInfo['email']
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.patient-welcome',
            with: [
                'patient' => $this->patient,
                'clinicInfo' => $this->clinicInfo,
                'registrationData' => $this->registrationData
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
