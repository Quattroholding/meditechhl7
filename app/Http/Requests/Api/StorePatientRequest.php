<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:patients,email'],
            'identifier' => ['required', 'string', 'max:50', 'unique:patients,identifier'],
            'identification_type' => ['nullable', 'string', 'in:CC,CE,PA,SS,PT'],
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
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:M,F,male,female,other'],
            'id_document' => 'nullable|string', // Acepta string (base64)
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'Debe ingresar un correo válido.',
            'email.max' => 'El email no puede exceder los 255 caracteres.',
            'email.unique' => 'Este email ya está registrado.',
            'identifier.required' => 'El identificador es obligatorio.',
            'identifier.string' => 'El identificador debe ser una cadena de texto.',
            'identifier.max' => 'El identificador no puede exceder los 50 caracteres.',
            'identifier.unique' => 'Este identificador ya está registrado.',
            'identification_type.in' => 'El tipo de identificación debe ser: CC, CE, PA, SS o PT.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'phone.max' => 'El teléfono no puede exceder los 50 caracteres.',
            'phone.regex' => 'El teléfono debe incluir el código de país en formato internacional (ej: +507...).',
            'birth_date.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'gender.in' => 'El género debe ser: M, F, male, female u other.',
            'id_document.file' => 'El documento de identidad debe ser un archivo.',
            'id_document.mimes' => 'El documento de identidad debe ser un archivo PDF, JPG, JPEG o PNG.',
            'id_document.max' => 'El documento de identidad no puede exceder los 2MB.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
