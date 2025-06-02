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

