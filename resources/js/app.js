import './bootstrap';


import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';
import 'intl-tel-input/build/js/intlTelInput.js';

// Almacenar instancias de intl-tel-input globalmente
window.phoneInputInstances = new Map();

// Función para inicializar un campo de teléfono
function initializePhoneInput(input) {
    // Evitar inicializar dos veces
    if (input.classList.contains('iti__input') || input.dataset.itiInitialized === 'true') {
        return input.itiInstance;
    }

    const iti = intlTelInput(input, {
        initialCountry: "pa",
        autoPlaceholder: "polite",
        placeholderNumberType: 'MOBILE',
        nationalMode: true,  // Modo nacional: solo muestra el número sin código
        formatOnDisplay: true,
        separateDialCode: true,  // Muestra el código de país separado
        strictMode: true,
        loadUtils: () => import("intl-tel-input/utils"),
    });

    // Marcar como inicializado
    input.dataset.itiInitialized = 'true';

    // Guardar la instancia globalmente
    window.phoneInputInstances.set(input.id || input.name, iti);

    // Hacer la instancia accesible desde el input
    input.itiInstance = iti;

    // Crear campo oculto para almacenar el número completo
    const hiddenInputName = 'full_' + (input.name || input.id);
    let hiddenInput = document.getElementById(hiddenInputName);

    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.id = hiddenInputName;
        hiddenInput.name = hiddenInputName;

        // Si hay wire:model en el input visible, agregarlo al hidden
        // Solo en componentes Livewire
        if (input.hasAttribute('wire:model')) {
            const wireModel = input.getAttribute('wire:model');
            hiddenInput.setAttribute('wire:model', 'full_' + wireModel);
        }

        input.parentElement.appendChild(hiddenInput);
    }

    input.hiddenInput = hiddenInput;

    // Si el input ya tiene un valor (edición de registro), parsearlo
    if (input.value && input.value.trim() !== '') {
        const initialValue = input.value.trim();

        // Si el valor ya tiene código de país, establecerlo
        if (initialValue.startsWith('+')) {
            iti.setNumber(initialValue);

            // Actualizar el campo oculto inmediatamente
            hiddenInput.value = iti.getNumber();
        } else {
            // Si no tiene código, agregarlo con el país por defecto
            hiddenInput.value = '+507' + initialValue;
        }
    }

    return iti;
}

// Función para limpiar valores corruptos solamente
function cleanCorruptValue(input) {
    const currentValue = input.value ? String(input.value).trim() : '';

    // Solo limpiar si está claramente corrupto
    if (currentValue.includes('[object Object]') ||
        currentValue.includes('undefined') ||
        currentValue === 'null') {

        input.value = '';

        // Limpiar errores visuales
        const existingError = input.parentElement.querySelector('.phone-error-message');
        if (existingError) {
            existingError.remove();
        }
        input.style.borderColor = '';
    }
}

document.addEventListener("DOMContentLoaded", () => {
    // Inicializar todos los campos de teléfono
    const phoneInputs = document.querySelectorAll('input[type="phone"], input[name="phone"], input#phone');

    phoneInputs.forEach(input => {
        const iti = initializePhoneInput(input);
        if (!iti) return;

        setupPhoneValidation(input);
    });

    // Observer para detectar cambios en el DOM (Livewire, etc.)
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) { // Es un elemento
                    // Verificar si el nodo es un input de teléfono
                    if (node.matches && node.matches('input[type="phone"], input[name="phone"], input#phone')) {
                        initializePhoneInput(node);
                        setupPhoneValidation(node);
                    }

                    // Verificar si el nodo contiene inputs de teléfono
                    const phoneInputs = node.querySelectorAll && node.querySelectorAll('input[type="phone"], input[name="phone"], input#phone');
                    if (phoneInputs) {
                        phoneInputs.forEach(input => {
                            initializePhoneInput(input);
                            setupPhoneValidation(input);
                        });
                    }
                }
            });
        });
    });

    // Observar cambios en el body
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Función separada para configurar validación (evitar duplicación de código)
function setupPhoneValidation(input) {
    // Evitar configurar múltiples veces
    if (input.dataset.validationSetup === 'true') {
        return;
    }
    input.dataset.validationSetup = 'true';

    const iti = input.itiInstance;
    if (!iti) return;

    const form = input.closest('form');

    // Validar al enviar el formulario
    if (form) {
        form.addEventListener('submit', (e) => {
            const currentValue = input.value ? String(input.value).trim() : '';
            const isRequired = input.hasAttribute('required');

            // Si el campo está vacío y es opcional, no validar
            if (!currentValue && !isRequired) {
                // Limpiar el campo oculto también
                if (input.hiddenInput) {
                    input.hiddenInput.value = '';
                }

                // Limpiar cualquier error existente
                const existingError = input.parentElement.querySelector('.phone-error-message');
                if (existingError) {
                    existingError.remove();
                }
                input.style.borderColor = '';
                return;
            }

            // Update both visible and hidden fields with full international number
            const fullNumber = iti.getNumber();
            if (fullNumber) {
                input.value = fullNumber;
            }
            if (input.hiddenInput) {
                input.hiddenInput.value = fullNumber;
            }

            // Solo validar si hay un valor o es requerido
            if (currentValue && !iti.isValidNumber()) {
                e.preventDefault();

                let errorMessage = 'Número de teléfono inválido. ';
                const error = iti.getValidationError();

                if (error === intlTelInput.utils.validationError.TOO_SHORT) {
                    errorMessage += 'El número es muy corto.';
                } else if (error === intlTelInput.utils.validationError.TOO_LONG) {
                    errorMessage += 'El número es muy largo.';
                } else if (error === intlTelInput.utils.validationError.INVALID_COUNTRY_CODE) {
                    errorMessage += 'Código de país inválido.';
                } else if (error === intlTelInput.utils.validationError.NOT_A_NUMBER) {
                    errorMessage += 'El valor ingresado no es un número.';
                }

                // Mostrar mensaje de error
                const existingError = input.parentElement.querySelector('.phone-error-message');
                if (existingError) {
                    existingError.textContent = errorMessage;
                } else {
                    const errorSpan = document.createElement('span');
                    errorSpan.className = 'error phone-error-message';
                    errorSpan.style.cssText = 'color: #e74c3c; font-size: 12px; margin-top: 5px; display: block;';
                    errorSpan.textContent = errorMessage;
                    input.parentElement.appendChild(errorSpan);
                }

                // Resaltar el campo con error
                input.style.borderColor = '#e74c3c';

                return false;
            }

            // Limpiar errores si el número es válido o está vacío
            const existingError = input.parentElement.querySelector('.phone-error-message');
            if (existingError) {
                existingError.remove();
            }
            input.style.borderColor = '';
        });
    }

    // Limpiar valores corruptos al hacer foco
    input.addEventListener('focus', () => {
        cleanCorruptValue(input);
    });

    // Actualizar campo oculto cuando pierde el foco
    input.addEventListener('blur', () => {
        // Primero limpiar valores corruptos
        cleanCorruptValue(input);

        const currentValue = input.value ? String(input.value).trim() : '';

        // Actualizar el campo oculto con el número completo en formato internacional
        if (iti && input.hiddenInput) {
            if (currentValue) {
                const fullNumber = iti.getNumber();
                if (fullNumber) {
                    input.hiddenInput.value = fullNumber;

                    // Disparar evento para que Livewire detecte el cambio en el hidden input
                    input.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            } else {
                // Si el campo está vacío, limpiar el campo oculto también
                input.hiddenInput.value = '';
                input.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
    });

    // Limpiar el mensaje de error cuando el usuario empiece a escribir
    input.addEventListener('input', () => {
        // Limpiar valores corruptos mientras escribe
        cleanCorruptValue(input);

        const existingError = input.parentElement.querySelector('.phone-error-message');
        if (existingError && iti && iti.isValidNumber && iti.isValidNumber()) {
            existingError.remove();
            input.style.borderColor = '';
        }
    });
}

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

