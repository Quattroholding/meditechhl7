<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterPractitionerRequest extends FormRequest
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
            'email' => 'required|string|email|max:255|unique:users,email',
            'identifier' => ['required', 'regex:'.$this->getIdPattern($this->identifier_type)],
            'identifier_type' => 'required|string',
            'given_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:male,female,unknown',
            'registry' => 'required|string|max:50',
            'cedula_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'idoneidad_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'cedula_file.required' => 'La cédula de identidad es obligatoria',
            'cedula_file.file' => 'El archivo de cédula debe ser un archivo válido',
            'cedula_file.mimes' => 'La cédula debe ser un archivo JPG, JPEG, PNG o PDF',
            'cedula_file.max' => 'La cédula no debe ser mayor a 5MB',
            'idoneidad_file.required' => 'La idoneidad médica es obligatoria',
            'idoneidad_file.file' => 'El archivo de idoneidad debe ser un archivo válido',
            'idoneidad_file.mimes' => 'La idoneidad debe ser un archivo JPG, JPEG, PNG o PDF',
            'idoneidad_file.max' => 'La idoneidad no debe ser mayor a 5MB',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'identifier.regex' => 'El formato del identificador no es válido para el tipo seleccionado',
        ];
    }

    /**
     * Get the regex pattern for the identifier type
     */
    private function getIdPattern(?string $type): string
    {
        return match ($type) {
            'CC' => '/^[0-9]+-[0-9]+-[0-9]+$/',
            'CE' => '/^[A-Z]+-[0-9]+-[0-9]+$/',
            'PA' => '/^[A-Z0-9-]{5,20}$/',
            'PT' => '/^[A-Z0-9-]{8,15}$/',
            'SS' => '/^\d{3}-?\d{2}-?\d{4}$/',
            default => '/^[A-Z0-9-]{5,20}$/',
        };
    }
}
