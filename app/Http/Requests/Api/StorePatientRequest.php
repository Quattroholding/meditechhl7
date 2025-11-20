<?php

namespace App\Http\Requests\Api;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:patients,email', 'unique:users,email'],
            'identifier' => ['required', 'string', 'max:50', 'unique:patients,identifier'],
            'phone' => ['required', 'string', 'max:20'],
            'id_document' => 'nullable|string', // ← Cambio aquí: acepta string (base64)
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
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'phone.max' => 'El teléfono no puede exceder los 20 caracteres.',
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
