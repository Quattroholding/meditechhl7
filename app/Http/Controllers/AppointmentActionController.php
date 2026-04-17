<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppointmentActionController extends Controller
{
    /**
     * Confirm appointment via WhatsApp link
     */
    public function confirm(Request $request, $appointmentId, $token)
    {
        try {
            $appointment = Appointment::findOrFail($appointmentId);

            // Verify token (simple hash validation)
            if (! $this->verifyToken($appointment, $token)) {
                return view('appointments.action-result', [
                    'success' => false,
                    'message' => 'Enlace inválido o expirado',
                    'appointment' => null,
                ]);
            }

            // Check if appointment can be confirmed
            if (! in_array($appointment->status, ['proposed', 'pending', 'booked'])) {
                return view('appointments.action-result', [
                    'success' => false,
                    'message' => 'Esta cita no puede ser confirmada. Estado actual: '.$appointment->status,
                    'appointment' => $appointment,
                ]);
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
            ]);

            return view('appointments.action-result', [
                'success' => true,
                'message' => '¡Cita confirmada exitosamente!',
                'appointment' => $appointment,
                'action' => 'confirmed',
            ]);

        } catch (\Exception $e) {
            Log::error('Error confirming appointment via WhatsApp', [
                'appointment_id' => $appointmentId,
                'error' => $e->getMessage(),
            ]);

            return view('appointments.action-result', [
                'success' => false,
                'message' => 'Ocurrió un error al confirmar la cita. Por favor contacte a la clínica.',
                'appointment' => null,
            ]);
        }
    }

    /**
     * Cancel appointment via WhatsApp link
     */
    public function cancel(Request $request, $appointmentId, $token)
    {
        try {
            $appointment = Appointment::findOrFail($appointmentId);

            // Verify token
            if (! $this->verifyToken($appointment, $token)) {
                return view('appointments.action-result', [
                    'success' => false,
                    'message' => 'Enlace inválido o expirado',
                    'appointment' => null,
                ]);
            }

            // Check if appointment can be cancelled
            if (in_array($appointment->status, ['cancelled', 'fulfilled', 'entered-in-error'])) {
                return view('appointments.action-result', [
                    'success' => false,
                    'message' => 'Esta cita ya fue cancelada o completada',
                    'appointment' => $appointment,
                ]);
            }

            // Check if appointment is too soon (less than 2 hours)
            $hoursUntilAppointment = now()->diffInHours($appointment->start, false);
            if ($hoursUntilAppointment < 2) {
                return view('appointments.action-result', [
                    'success' => false,
                    'message' => 'No se puede cancelar citas con menos de 2 horas de anticipación. Por favor contacte a la clínica.',
                    'appointment' => $appointment,
                ]);
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
            $appointment->notifyPractitionerAboutCancellation('Cancelado por el paciente vía WhatsApp');

            Log::info('Appointment cancelled via WhatsApp', [
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'old_status' => $oldStatus,
                'new_status' => 'cancelled',
            ]);

            return view('appointments.action-result', [
                'success' => true,
                'message' => 'Cita cancelada exitosamente',
                'appointment' => $appointment,
                'action' => 'cancelled',
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling appointment via WhatsApp', [
                'appointment_id' => $appointmentId,
                'error' => $e->getMessage(),
            ]);

            return view('appointments.action-result', [
                'success' => false,
                'message' => 'Ocurrió un error al cancelar la cita. Por favor contacte a la clínica.',
                'appointment' => null,
            ]);
        }
    }

    /**
     * Generate secure token for appointment action
     */
    public static function generateToken(Appointment $appointment): string
    {
        return hash('sha256', $appointment->id.$appointment->patient_id.$appointment->start->format('YmdHi').config('app.key'));
    }

    /**
     * Verify appointment action token
     */
    protected function verifyToken(Appointment $appointment, string $token): bool
    {
        return hash_equals(self::generateToken($appointment), $token);
    }

    /**
     * Confirm appointment via signed email link (for practitioners)
     */
    public function confirmSigned(Request $request, Appointment $appointment)
    {
        try {
            // Validate signed URL
            if (! $request->hasValidSignature()) {
                return view('appointments.action-result', [
                    'success' => false,
                    'message' => 'Enlace inválido o expirado. Por favor contacte a la clínica.',
                    'appointment' => null,
                ]);
            }

            // Check if appointment can be confirmed
            if (! in_array($appointment->status, ['proposed', 'pending', 'booked'])) {
                return view('appointments.action-result', [
                    'success' => false,
                    'message' => 'Esta cita no puede ser confirmada. Estado actual: '.$appointment->status,
                    'appointment' => $appointment,
                ]);
            }

            // Update appointment status
            $oldStatus = $appointment->status;
            $appointment->status = 'booked';
            $appointment->save();

            // Log status change
            AppointmentStatus::create([
                'appointment_id' => $appointment->id,
                'status' => 'booked',
                'user_id' => $appointment->practitioner->user_id ?? null,
                'comment' => 'Confirmado por el médico vía email',
            ]);

            // Notify patient about confirmation
            if ($oldStatus !== 'booked') {
                $appointment->notifyPatientAboutConfirmation();
            }

            Log::info('Appointment confirmed via email by practitioner', [
                'appointment_id' => $appointment->id,
                'practitioner_id' => $appointment->practitioner_id,
                'old_status' => $oldStatus,
                'new_status' => 'booked',
            ]);

            return view('appointments.action-result', [
                'success' => true,
                'message' => '¡Cita confirmada exitosamente! El paciente recibirá una notificación.',
                'appointment' => $appointment,
                'action' => 'confirmed',
            ]);

        } catch (\Exception $e) {
            Log::error('Error confirming appointment via email', [
                'appointment_id' => $appointment->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return view('appointments.action-result', [
                'success' => false,
                'message' => 'Ocurrió un error al confirmar la cita. Por favor contacte al administrador.',
                'appointment' => null,
            ]);
        }
    }

    /**
     * Cancel appointment via signed email link (for practitioners)
     */
    public function cancelSigned(Request $request, Appointment $appointment)
    {
        try {
            // Validate signed URL
            if (! $request->hasValidSignature()) {
                return view('appointments.action-result', [
                    'success' => false,
                    'message' => 'Enlace inválido o expirado. Por favor contacte a la clínica.',
                    'appointment' => null,
                ]);
            }

            // Check if appointment can be cancelled
            if (in_array($appointment->status, ['cancelled', 'fulfilled', 'entered-in-error'])) {
                return view('appointments.action-result', [
                    'success' => false,
                    'message' => 'Esta cita ya fue cancelada o completada',
                    'appointment' => $appointment,
                ]);
            }

            // Update appointment status
            $oldStatus = $appointment->status;
            $appointment->status = 'cancelled';
            $appointment->save();

            // Log status change
            AppointmentStatus::create([
                'appointment_id' => $appointment->id,
                'status' => 'cancelled',
                'user_id' => $appointment->practitioner->user_id ?? null,
                'comment' => 'Cancelado por el médico vía email',
            ]);

            // Notify patient about cancellation
            $appointment->notifyPatientAboutCancellation('El médico canceló la cita');

            Log::info('Appointment cancelled via email by practitioner', [
                'appointment_id' => $appointment->id,
                'practitioner_id' => $appointment->practitioner_id,
                'old_status' => $oldStatus,
                'new_status' => 'cancelled',
            ]);

            return view('appointments.action-result', [
                'success' => true,
                'message' => 'Cita cancelada exitosamente. El paciente recibirá una notificación.',
                'appointment' => $appointment,
                'action' => 'cancelled',
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling appointment via email', [
                'appointment_id' => $appointment->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return view('appointments.action-result', [
                'success' => false,
                'message' => 'Ocurrió un error al cancelar la cita. Por favor contacte al administrador.',
                'appointment' => null,
            ]);
        }
    }
}
