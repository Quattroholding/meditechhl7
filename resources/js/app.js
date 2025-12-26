import './bootstrap';


import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';
import 'intl-tel-input/build/js/intlTelInput.js';

document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll("#form");

    forms.forEach(form => {
        const input = form.querySelector("#phone");
        const message = form.querySelector(".message");

        if (input) {
            const iti = intlTelInput(input, {
                initialCountry: "pa",
                autoPlaceholder: "polite",
                placeholderNumberType: 'MOBILE',
                nationalMode: false,
                hiddenInput: () => ({
                    phone: "full_phone",
                    country: "country_code",
                }),
                loadUtils: () => import("intl-tel-input/utils"),
            });

            form.onsubmit = () => {
                if (!iti.isValidNumber()) {
                    let error_message = '';
                    const error = iti.getValidationError();
                    if (error === intlTelInput.utils.validationError.TOO_SHORT) {
                        error_message = 'the number is too short';
                    }
                    message.innerHTML = `Invalid number, Please try again. ${error_message}`;
                    return false;
                }
            };

            form.addEventListener("submit", () => {
                input.value = iti.getNumber();
            });
        }
    });
});

// Notification functions
window.markAsRead = function(notificationId, actionUrl) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the notification from the list
            const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationElement) {
                notificationElement.remove();
            }

            // Update the unread count
            updateUnreadCount();

            // Redirect if there's an action URL
            if (actionUrl && actionUrl !== '#') {
                window.location.href = actionUrl;
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

window.markAllAsRead = function() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to refresh the notification list
            window.location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateUnreadCount() {
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.nav-link .badge');
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                }
            } else {
                if (badge) {
                    badge.remove();
                }
                const pulse = document.querySelector('.nav-link .pulse');
                if (pulse) {
                    pulse.remove();
                }
            }
        })
        .catch(error => console.error('Error:', error));
}

