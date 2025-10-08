<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;

class TwilioWebhookController extends Controller
{
    /**
     * Handle incoming WhatsApp messages from Twilio
     */
    public function handleIncomingMessage(Request $request)
    {
        try {
            // Get message details
            $from = $request->input('From'); // whatsapp:+50769121653
            $body = trim($request->input('Body'));
            $messageId = $request->input('MessageSid');

            Log::info('Incoming WhatsApp message', [
                'from' => $from,
                'body' => $body,
                'message_id' => $messageId,
            ]);

            // Parse the message body for appointment actions
            // Expected format: "CONFIRMAR 123" or "CANCELAR 123"
            $response = $this->processMessage($from, $body);

            // Return TwiML response
            return response($response, 200)->header('Content-Type', 'text/xml');

        } catch (\Exception $e) {
            Log::error('Error processing WhatsApp webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error message to user
            $twiml = new MessagingResponse;
            $twiml->message('Lo sentimos, ocurrió un error al procesar su solicitud. Por favor contacte a la clínica.');

            return response($twiml, 200)->header('Content-Type', 'text/xml');
        }
    }

    /**
     * Process WhatsApp message and return appropriate response
     */
    protected function processMessage(string $from, string $body): string
    {
        $twiml = new MessagingResponse;

        // Extract phone number from WhatsApp format (whatsapp:+50769121653)
        $phoneNumber = str_replace('whatsapp:', '', $from);

        // Parse message for action keywords
        $body = strtoupper($body);

        // Check for CONFIRMAR or CANCELAR commands
        if (preg_match('/CONFIRMAR\s+(\d+)/', $body, $matches)) {
            $appointmentId = $matches[1];
            $result = $this->confirmAppointment($appointmentId, $phoneNumber);
            $twiml->message($result['message']);
        } elseif (preg_match('/CANCELAR\s+(\d+)/', $body, $matches)) {
            $appointmentId = $matches[1];
            $result = $this->cancelAppointment($appointmentId, $phoneNumber);
            $twiml->message($result['message']);
        } else {
            // Unknown command
            $twiml->message("Comando no reconocido. Por favor responda:\n• CONFIRMAR [número de cita]\n• CANCELAR [número de cita]");
        }

        return $twiml;
    }

    /**
     * Confirm appointment
     */
    protected function confirmAppointment(int $appointmentId, string $phoneNumber): array
    {
        try {
            $appointment = Appointment::find($appointmentId);

            if (! $appointment) {
                return [
                    'success' => false,
                    'message' => "❌ No se encontró la cita #{$appointmentId}. Por favor verifique el número de cita.",
                ];
            }

            // Verify phone number matches patient
            if (! $this->verifyPatientPhone($appointment, $phoneNumber)) {
                return [
                    'success' => false,
                    'message' => "❌ Este número de teléfono no coincide con el paciente de la cita #{$appointmentId}.",
                ];
            }

            // Check if appointment can be confirmed
            if (! in_array($appointment->status, ['proposed', 'pending', 'booked', 'confirm'])) {
                return [
                    'success' => false,
                    'message' => "❌ Esta cita no puede ser confirmada. Estado actual: {$appointment->status}",
                ];
            }

            // Update appointment status
            $oldStatus = $appointment->status;
            $appointment->status = 'confirm';
            $appointment->save();

            // Log status change
            AppointmentStatus::create([
                'appointment_id' => $appointment->id,
                'status' => 'confirm',
                'user_id' => $appointment->patient->user_id ?? null,
                'comment' => 'Confirmado vía WhatsApp',
            ]);

            Log::info('Appointment confirmed via WhatsApp', [
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'old_status' => $oldStatus,
                'new_status' => 'confirm',
                'phone' => $phoneNumber,
            ]);

            $message = "✅ *¡Cita Confirmada!*\n\n";
            $message .= "📋 *Cita #{$appointment->id}*\n";
            $message .= "👨‍⚕️ *Doctor:* {$appointment->practitioner->name}\n";
            $message .= "📅 *Fecha:* {$appointment->start->format('d/m/Y')}\n";
            $message .= "🕐 *Hora:* {$appointment->start->format('H:i a')}\n\n";
            $message .= "Por favor llegue 15 minutos antes de su cita.\n";
            $message .= '¡Esperamos verle pronto! 😊';

            return [
                'success' => true,
                'message' => $message,
            ];

        } catch (\Exception $e) {
            Log::error('Error confirming appointment via WhatsApp', [
                'appointment_id' => $appointmentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => '❌ Ocurrió un error al confirmar la cita. Por favor contacte a la clínica.',
            ];
        }
    }

    /**
     * Cancel appointment
     */
    protected function cancelAppointment(int $appointmentId, string $phoneNumber): array
    {
        try {
            $appointment = Appointment::find($appointmentId);

            if (! $appointment) {
                return [
                    'success' => false,
                    'message' => "❌ No se encontró la cita #{$appointmentId}. Por favor verifique el número de cita.",
                ];
            }

            // Verify phone number matches patient
            if (! $this->verifyPatientPhone($appointment, $phoneNumber)) {
                return [
                    'success' => false,
                    'message' => "❌ Este número de teléfono no coincide con el paciente de la cita #{$appointmentId}.",
                ];
            }

            // Check if appointment can be cancelled
            if (in_array($appointment->status, ['cancelled', 'fulfilled', 'entered-in-error'])) {
                return [
                    'success' => false,
                    'message' => '❌ Esta cita ya fue cancelada o completada.',
                ];
            }

            // Check if appointment is too soon (less than 2 hours)
            $hoursUntilAppointment = now()->diffInHours($appointment->start, false);
            if ($hoursUntilAppointment < 2) {
                return [
                    'success' => false,
                    'message' => '❌ No se puede cancelar citas con menos de 2 horas de anticipación. Por favor contacte a la clínica al teléfono de atención.',
                ];
            }

            // Update appointment status
            $oldStatus = $appointment->status;
            $appointment->status = 'cancelled';
            $appointment->save();

            // Log status change
            AppointmentStatus::create([
                'appointment_id' => $appointment->id,
                'status' => 'cancelled',
                'user_id' => $appointment->patient->user_id ?? null,
                'comment' => 'Cancelado por el paciente vía WhatsApp',
            ]);

            // Notify practitioner about cancellation
            if (method_exists($appointment, 'notifyPractitionerAboutCancellation')) {
                $appointment->notifyPractitionerAboutCancellation('Cancelado por el paciente vía WhatsApp');
            }

            Log::info('Appointment cancelled via WhatsApp', [
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'old_status' => $oldStatus,
                'new_status' => 'cancelled',
                'phone' => $phoneNumber,
            ]);

            $message = "✅ *Cita Cancelada*\n\n";
            $message .= "📋 *Cita #{$appointment->id}*\n";
            $message .= "👨‍⚕️ *Doctor:* {$appointment->practitioner->name}\n";
            $message .= "📅 *Fecha:* {$appointment->start->format('d/m/Y')}\n";
            $message .= "🕐 *Hora:* {$appointment->start->format('H:i a')}\n\n";
            $message .= "Su cita ha sido cancelada exitosamente.\n";
            $message .= 'Si desea reagendar, por favor contacte a la clínica.';

            return [
                'success' => true,
                'message' => $message,
            ];

        } catch (\Exception $e) {
            Log::error('Error cancelling appointment via WhatsApp', [
                'appointment_id' => $appointmentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => '❌ Ocurrió un error al cancelar la cita. Por favor contacte a la clínica.',
            ];
        }
    }

    /**
     * Verify that phone number belongs to appointment's patient
     */
    protected function verifyPatientPhone(Appointment $appointment, string $phoneNumber): bool
    {
        $patient = $appointment->patient;

        if (! $patient) {
            return false;
        }

        // Normalize both phone numbers for comparison
        $normalizedInput = $this->normalizePhoneForComparison($phoneNumber);
        $normalizedWhatsApp = $this->normalizePhoneForComparison($patient->whatsapp_phone);
        $normalizedPhone = $this->normalizePhoneForComparison($patient->phone);

        return $normalizedInput === $normalizedWhatsApp || $normalizedInput === $normalizedPhone;
    }

    /**
     * Normalize phone number for comparison (remove all non-digits)
     */
    protected function normalizePhoneForComparison(?string $phone): string
    {
        if (! $phone) {
            return '';
        }

        return preg_replace('/[^\d]/', '', $phone);
    }
}
