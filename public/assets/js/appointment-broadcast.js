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
                        <div class="text-end" style="margin-top: 20px; display: flex; gap: 10px;">

                            <button type="button" class="btn btn-secondary" onclick="dismissAppointmentPopup()">
                                <i class="fas fa-times me-2"></i>Cerrar
                            </button>

                            <button type="button" class="btn btn-warning" onclick="snoozeAppointmentPopup(${appointmentData.id})">
                                <i class="fas fa-clock me-2"></i>Recordar en 5 min
                            </button>

                            <button type="button" class="btn btn-primary" onclick="goToConsultation(${appointmentData.id})">
                                <i class="fas fa-stethoscope me-2"></i>Iniciar Consulta
                            </button>
                        </div>
                    </div>
            </div>
        `;
        document.body.appendChild(popup);

        // Save appointment data to localStorage for snooze functionality
        localStorage.setItem(`appointment_${appointmentData.id}`, JSON.stringify(appointmentData));

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

    // Function to snooze popup for 5 minutes
    window.snoozeAppointmentPopup = function(appointmentId) {
        const modal = document.getElementById('appointmentCheckedInModal');
        if (modal) {
            modal.style.opacity = '0';
            setTimeout(() => modal.remove(), 300);
        }

        // Get appointment data from localStorage or create default
        let appointmentData = JSON.parse(localStorage.getItem(`appointment_${appointmentId}`) || '{}');
        if (!appointmentData.id) {
            // If no data in localStorage, use default data (from last popup)
            appointmentData = {
                id: appointmentId,
                patient_name: "Paciente",
                appointment_time: "Pendiente",
                consulting_room: "Consultorio"
            };
        }

        console.log(`Appointment snoozed for 5 minutes. Will remind at:`, new Date(Date.now() + 5 * 60 * 1000));

        // Set timeout for 5 minutes (300,000 milliseconds)
        setTimeout(() => {
            console.log('Showing snoozed appointment reminder');
            showAppointmentPopup(appointmentData);
        }, 5 * 60 * 1000);

        // Show confirmation toast
        if (typeof toastr !== 'undefined') {
            toastr.info('Te recordaré sobre este paciente en 5 minutos', 'Recordatorio programado');
        } else {
            alert('Te recordaré sobre este paciente en 5 minutos');
        }
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
