<?php

namespace App\Http\Requests;

use App\Enums\DiscountType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller/middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $package = $this->getPackage();
        $requiresPractitioner = $package && $package->max_doctors_included === 1;

        return [
            // Información Básica del Cliente
            'name' => ['required', 'string', 'max:255'],
            'long_name' => ['required', 'string', 'max:500'],
            'ruc' => ['required', 'string', 'max:50', 'unique:clients,ruc'],
            'dv' => ['required', 'integer', 'min:0', 'max:99'],
            'phone' => [
                'required',
                'string',
                'max:50',
                'regex:/^\+[1-9]\d{1,14}$/',
                function ($attribute, $value, $fail) {
                    if (! str_starts_with($value, '+')) {
                        $fail('El número de teléfono debe incluir el código de país (ej: +507...)');

                        return;
                    }

                    if (strlen($value) < 8) {
                        $fail('El número de teléfono es demasiado corto.');

                        return;
                    }
                },
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],

            // Plan y Suscripción
            'package_id' => ['required', 'exists:packages,id'],
            'extra_doctors' => ['nullable', 'integer', 'min:0', 'max:100'],
            'billing_day' => ['nullable', 'integer', 'min:1', 'max:28'],

            // Datos del Practitioner (requeridos si max_doctors=1)
            'identifier_type' => [
                $requiresPractitioner ? 'required' : 'nullable',
                Rule::in(['CC', 'PA', 'CE', 'PT']),
            ],
            'identifier' => [
                $requiresPractitioner ? 'required' : 'nullable',
                'string',
                'max:50',
                'unique:practitioners,identifier',
            ],
            'first_name' => [
                $requiresPractitioner ? 'required' : 'nullable',
                'string',
                'max:255',
            ],
            'last_name' => [
                $requiresPractitioner ? 'required' : 'nullable',
                'string',
                'max:255',
            ],
            'gender' => [
                $requiresPractitioner ? 'required' : 'nullable',
                Rule::in(['male', 'female']),
            ],

            // Beneficios y Descuentos
            'trial_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'free_months' => ['nullable', 'integer', 'min:0', 'max:12'],
            'referral_code' => ['nullable', 'string', 'max:50', 'exists:referral_codes,code'],

            // Descuentos Personalizados
            'custom_discount_type' => [
                'nullable',
                Rule::in([DiscountType::PERCENTAGE->value, DiscountType::FIXED_AMOUNT->value]),
            ],
            'custom_discount_value' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $discountType = $this->input('custom_discount_type');
                    if ($discountType === DiscountType::PERCENTAGE->value && $value > 100) {
                        $fail('El porcentaje de descuento no puede ser mayor a 100%.');
                    }
                },
            ],
            'custom_discount_invoices' => ['nullable', 'integer', 'min:1', 'max:24'],
            'custom_discount_reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get the package for validation
     */
    protected function getPackage()
    {
        if (! $this->input('package_id')) {
            return null;
        }

        return \App\Models\Package::find($this->input('package_id'));
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre corto del cliente es obligatorio.',
            'long_name.required' => 'El nombre completo del cliente es obligatorio.',
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.unique' => 'Este RUC ya está registrado en el sistema.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser una dirección válida.',
            'email.unique' => 'Este email ya está registrado en el sistema.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'package_id.required' => 'Debe seleccionar un paquete de suscripción.',
            'package_id.exists' => 'El paquete seleccionado no existe.',
            'logo.image' => 'El logo debe ser una imagen.',
            'logo.mimes' => 'El logo debe ser de tipo: jpeg, png, jpg, gif, svg.',
            'logo.max' => 'El logo no debe ser mayor a 2MB.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'El teléfono debe incluir el código de país en formato internacional (ej: +507...).',
            'billing_day.min' => 'El día de facturación debe ser entre 1 y 28.',
            'billing_day.max' => 'El día de facturación debe ser entre 1 y 28.',
            'trial_days.max' => 'Los días de prueba no pueden ser más de 365.',
            'free_months.max' => 'Los meses gratis no pueden ser más de 12.',
            'referral_code.exists' => 'El código de referido no es válido.',
            'custom_discount_invoices.max' => 'El descuento no puede aplicarse a más de 24 facturas.',

            // Practitioner messages
            'practitioner_identifier_type.required' => 'El tipo de documento del doctor es obligatorio.',
            'practitioner_identifier.required' => 'El número de documento del doctor es obligatorio.',
            'practitioner_identifier.unique' => 'Este número de documento ya está registrado para otro doctor.',
            'practitioner_given_name.required' => 'Los nombres del doctor son obligatorios.',
            'practitioner_family_name.required' => 'Los apellidos del doctor son obligatorios.',
            'practitioner_gender.required' => 'El género del doctor es obligatorio.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre corto',
            'long_name' => 'nombre completo',
            'ruc' => 'RUC',
            'dv' => 'dígito verificador',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'logo' => 'logo',
            'package_id' => 'paquete',
            'extra_doctors' => 'doctores adicionales',
            'billing_day' => 'día de facturación',
            'trial_days' => 'días de prueba',
            'free_months' => 'meses gratis',
            'referral_code' => 'código de referido',
            'custom_discount_type' => 'tipo de descuento',
            'custom_discount_value' => 'valor del descuento',
            'custom_discount_invoices' => 'facturas para aplicar descuento',
            'custom_discount_reason' => 'razón del descuento',

            // Practitioner attributes
            'practitioner_identifier_type' => 'tipo de documento del doctor',
            'practitioner_identifier' => 'número de documento del doctor',
            'practitioner_given_name' => 'nombres del doctor',
            'practitioner_family_name' => 'apellidos del doctor',
            'practitioner_gender' => 'género del doctor',
        ];
    }
}
