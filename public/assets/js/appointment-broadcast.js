/**
 * Appointment Broadcast Listener for Doctors
 * Handles real-time notifications when patients check-in
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Appointment broadcast script loaded');
    console.log('isDoctorRole:', window.isDoctorRole);
    console.log('Echo available:', typeof window.Echo);

    // Solo ejecutar si es un doctor
    if (!window.isDoctorRole) return;

    // Function to show appointment popup
    function showAppointmentPopup(appointmentData) {
        // Remove any existing popup
        const existingModal = document.getElementById('appointmentCheckedInModal');
        if (existingModal) {
            existingModal.remove();
        }

        const popup = document.createElement('div');
        popup.innerHTML = `
            <div class="modal-overlay" id="appointmentCheckedInModal" style="z-index: 9999;">
                <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-user-clock me-2"></i>
                                ¡Paciente Listo para Consulta!
                            </h5>
                        </div>
                        <div class="modal-body text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-user-md fa-3x text-primary mb-3"></i>
                            </div>
                            <h4 class="mb-3">El paciente <strong>${appointmentData.patient_name}</strong> ya está listo</h4>
                            <p class="mb-2">
                                <i class="fas fa-clock me-2"></i>
                                Hora de la cita: <strong>${appointmentData.appointment_time}</strong>
                            </p>
                            <p class="mb-4">
                                <i class="fas fa-door-open me-2"></i>
                                Consultorio: <strong>${appointmentData.consulting_room}</strong>
                            </p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                El paciente ha sido registrado y está esperando para iniciar la consulta.
                            </div>
                        </div>
                        <div class="text-end" style="margin-top: 20px; display: flex; gap: 15px;">

                            <button type="button" class="btn btn-secondary" onclick="dismissAppointmentPopup()">
                                <i class="fas fa-times me-2"></i>Cerrar
                            </button>

                            <button type="button" class="btn btn-primary" onclick="goToConsultation(${appointmentData.id})">
                                <i class="fas fa-stethoscope me-2"></i>Iniciar Consulta
                            </button>
                        </div>
                    </div>
            </div>
        `;
        document.body.appendChild(popup);

        // Play notification sound
        playNotificationSound();

        // Auto-hide after 30 seconds if not closed
        /*setTimeout(() => {
            const modal = document.getElementById('appointmentCheckedInModal');
            if (modal) {
                modal.style.opacity = '0.8';
            }
        }, 30000);*/
    }

    // Function to dismiss popup
    window.dismissAppointmentPopup = function() {
        const modal = document.getElementById('appointmentCheckedInModal');
        if (modal) {
            modal.style.opacity = '0';
            setTimeout(() => modal.remove(), 300);
        }
    }

    // Function to redirect to consultation
    window.goToConsultation = function(appointmentId) {
        window.location.href = `/consultation/${appointmentId}`;
    }

    // Play notification sound
    function playNotificationSound() {
        try {
            // Try to play browser notification sound
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmshBTmN2O/OdCgEKHzJ8N2QRQsRZbXo7a5WFAlCsNb0wHA2B');
            audio.volume = 0.3;
            audio.play().catch(e => console.log('Could not play sound:', e));
        } catch (e) {
            console.log('Audio not available:', e);
        }
    }

    // Test popup function (remove after testing)
    window.testPopup = function() {
        showAppointmentPopup({
            id: 64,
            patient_name: "Liberty Horton",
            appointment_time: "12:01",
            consulting_room: "Consultorio 1"
        });
    };

    // Check if Echo is available
    if (typeof window.Echo !== 'undefined') {
        console.log('Echo is available, setting up broadcast listener...');

        // Get current doctor ID from meta or data attribute
        const doctorId = document.querySelector('meta[name="doctor-id"]')?.getAttribute('content') ||
                        document.body.dataset.doctorId;

        console.log('Doctor ID found:', doctorId);

        if (doctorId) {
            console.log(`Subscribing to channel: doctor.${doctorId}`);

            // Listen to private channel for this doctor
            window.Echo.private(`doctor.${doctorId}`)
                .listen('.appointment.checked.in', (data) => {
                    console.log('Appointment checked in event received:', data);
                    showAppointmentPopup(data.appointment);
                })
                .error((error) => {
                    console.error('Echo channel error:', error);
                });
        } else {
            console.warn('Doctor ID not found. Cannot subscribe to broadcast channel.');
        }
    } else {
        console.warn('Laravel Echo not available. Broadcasting features disabled.');
    }
});
