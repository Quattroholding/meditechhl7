<?php

namespace App\Http\Requests;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
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
            'company_name' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
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
            'message' => 'mensaje',
        ];
    }
}
