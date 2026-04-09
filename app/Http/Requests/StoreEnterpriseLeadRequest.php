<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnterpriseLeadRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpgradeRequest = $this->input('source') === 'upgrade_request';

        // Validación más flexible del teléfono para upgrade requests
        $phoneRules = ['required', 'string', 'max:50'];

        if (! $isUpgradeRequest) {
            // Validación estricta solo para landing page
            $phoneRules[] = 'regex:/^\+[1-9]\d{1,14}$/';
            $phoneRules[] = function ($attribute, $value, $fail) {
                if (! str_starts_with($value, '+')) {
                    $fail('El número de teléfono debe incluir el código de país (ej: +507...)');

                    return;
                }

                if (strlen($value) < 8) {
                    $fail('El número de teléfono es demasiado corto.');

                    return;
                }
            };
        }

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => $phoneRules,
            'company_name' => ['required', 'string', 'max:255'],
            'number_of_doctors' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'agent_preference' => ['nullable', 'string', 'in:sami,personalized'],
            'number_of_branches' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'current_system' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],

            // Campos opcionales para requests desde usuarios autenticados
            'source' => ['nullable', 'string', 'in:landing_page,upgrade_request'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'current_subscription_id' => ['nullable', 'integer', 'exists:client_subscriptions,id'],
            'current_package_id' => ['nullable', 'integer', 'exists:packages,id'],
            'requested_package_id' => ['nullable', 'integer', 'exists:packages,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser una dirección válida.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.regex' => 'El teléfono debe incluir el código de país en formato internacional (ej: +507...).',
            'company_name.required' => 'El nombre de la empresa/clínica es obligatorio.',
            'number_of_doctors.integer' => 'El número de médicos debe ser un valor numérico.',
            'number_of_doctors.min' => 'El número de médicos debe ser al menos 1.',
            'number_of_doctors.max' => 'El número de médicos no puede exceder 10,000.',
            'agent_preference.in' => 'La preferencia de agente debe ser "Agente SAMI" o "Agente Personalizado".',
            'number_of_branches.integer' => 'El número de sucursales debe ser un valor numérico.',
            'number_of_branches.min' => 'El número de sucursales debe ser al menos 1.',
            'number_of_branches.max' => 'El número de sucursales no puede exceder 1,000.',
            'current_system.max' => 'El nombre del sistema actual no debe exceder 255 caracteres.',
            'message.max' => 'El mensaje no debe exceder 1000 caracteres.',
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
            'full_name' => 'nombre completo',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'company_name' => 'nombre de la empresa',
            'number_of_doctors' => 'número de médicos',
            'agent_preference' => 'preferencia de agente',
            'number_of_branches' => 'número de sucursales',
            'current_system' => 'sistema actual',
            'message' => 'mensaje',
        ];
    }
}
