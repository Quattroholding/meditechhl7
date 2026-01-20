<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PublicRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Público
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $package = $this->getPackage();
        $requiresPractitioner = $package && $package->max_users === 1;

        return [
            // Información del Cliente
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'package_id' => ['required', 'exists:packages,id'],

            // Datos del Admin/Doctor
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    $existingUser = User::where('email', $value)->first();

                    if (! $existingUser) {
                        return; // Email no existe, todo bien
                    }

                    // Si el usuario ya tiene un client asignado, no puede registrarse de nuevo
                    if ($existingUser->default_client_id || $existingUser->clients()->exists()) {
                        $fail('Este email ya está registrado en el sistema.');
                    }
                    // Si no tiene client, es un usuario de SAMI Recetas - permitir registro
                },
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['required', 'string', 'max:50'],

            // Practitioner (condicional si max_users=1)
            'identifier_type' => [
                $requiresPractitioner ? 'required' : 'nullable',
                Rule::in(['CC', 'PA', 'CE', 'PT']),
            ],
            'identifier' => [
                $requiresPractitioner ? 'required' : 'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (! $value) {
                        return;
                    }

                    $existingPractitioner = \App\Models\Practitioner::withoutGlobalScopes()
                        ->where('identifier', $value)
                        ->first();

                    if (! $existingPractitioner) {
                        return; // Identifier no existe, todo bien
                    }

                    // Verificar si el practitioner tiene un usuario asociado
                    $user = $existingPractitioner->user;

                    if (! $user) {
                        $fail('Este número de documento ya está registrado.');

                        return;
                    }

                    // Si el usuario ya tiene un client asignado, no puede registrarse de nuevo
                    if ($user->default_client_id || $user->clients()->exists()) {
                        $fail('Este número de documento ya está registrado.');
                    }
                    // Si no tiene client, es un usuario de SAMI Recetas - permitir registro
                },
            ],
            'gender' => [
                $requiresPractitioner ? 'required' : 'nullable',
                Rule::in(['male', 'female']),
            ],
            'medical_speciality' => [
                'required',
                'exists:medical_specialties,id',
            ],

            // Código de Referido (Opcional)
            'referral_code' => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (! $value) {
                        return;
                    }

                    $referralCode = \App\Models\ReferralCode::where('code', $value)->first();

                    if (! $referralCode) {
                        $fail('El código de referido no existe.');

                        return;
                    }

                    if (! $referralCode->canBeUsed()) {
                        $fail('El código de referido no está disponible o ha expirado.');

                        return;
                    }
                },
            ],

            // Términos y Condiciones
            'terms_and_privacy' => ['required', 'accepted'],
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
            'name.required' => 'El nombre del consultorio/clínica es obligatorio.',
            'long_name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser una dirección válida.',
            'email.unique' => 'Este email ya está registrado en el sistema.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'package_id.required' => 'Debe seleccionar un paquete de suscripción.',
            'package_id.exists' => 'El paquete seleccionado no existe.',
            'logo.image' => 'El logo debe ser una imagen.',
            'logo.mimes' => 'El logo debe ser de tipo: jpeg, png, jpg.',
            'logo.max' => 'El logo no debe ser mayor a 2MB.',
            'phone.required' => 'El teléfono es obligatorio.',
            'first_name.required' => 'Los nombres son obligatorios.',
            'last_name.required' => 'Los apellidos son obligatorios.',
            'identifier_type.required' => 'El tipo de documento es obligatorio.',
            'identifier.required' => 'El número de documento es obligatorio.',
            'identifier.unique' => 'Este número de documento ya está registrado.',
            'gender.required' => 'El género es obligatorio.',
            'terms_and_privacy.required' => 'Debe aceptar los términos de servicio y políticas de privacidad.',
            'terms_and_privacy.accepted' => 'Debe aceptar los términos de servicio y políticas de privacidad.',
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
            'name' => 'nombre del consultorio',
            'long_name' => 'nombre completo',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'logo' => 'logo',
            'package_id' => 'paquete',
            'first_name' => 'nombres',
            'last_name' => 'apellidos',
            'identifier_type' => 'tipo de documento',
            'identifier' => 'número de documento',
            'gender' => 'género',
            'terms_and_privacy' => 'términos de servicio y políticas de privacidad',
        ];
    }
}
