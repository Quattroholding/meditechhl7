<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest
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
        $rol = $this->input('rol');
        // Reglas de validación base
        $rules['rol'] = 'required|integer';
        switch ($rol) {
            case '3':
                /* ------VALIDACIÓN PARA ASISTENTE------ */
                $rules['first_name'] = 'required|string|max:255';
                $rules['last_name'] = 'required|string|max:255';
                $rules['email'] = 'required|email|unique:users,email';
                $rules['password'] = [
                    'required',
                    'string',
                    'confirmed',
                    'min:8',
                    'regex:/[a-z]/',      // debe contener al menos una letra minúscula
                    'regex:/[A-Z]/',      // debe contener al menos una letra mayúscula
                    'regex:/[0-9]/',      // debe contener al menos un número
                ];
                $rules['clients'] = 'required|array|min:1';
                break;
            case '5':
                $rules['first_name'] = 'required|string|max:255';
                $rules['last_name'] = 'required|string|max:255';
                $rules['email'] = 'required|email|unique:users,email';
                $rules['password'] = [
                    'required',
                    'string',
                    'confirmed',
                    'min:8',
                    'regex:/[a-z]/',      // debe contener al menos una letra minúscula
                    'regex:/[A-Z]/',      // debe contener al menos una letra mayúscula
                    'regex:/[0-9]/',      // debe contener al menos un número
                ];
                $rules['avatar'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
                $rules['clients'] = 'required|array|min:1';
                break;
            default:
                break;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una letra minúscula y un número.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'clients.required' => 'Debe seleccionar al menos un cliente.',
            'clients.min' => 'Debe seleccionar al menos un cliente.',
            'avatar.required' => 'La imagen de perfil es obligatoria.',
            'avatar.image' => 'El archivo debe ser una imagen.',
            'avatar.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'avatar.max' => 'La imagen no debe ser mayor a 2MB.',
            'rol.required' => 'El rol es obligatorio.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Registrar los errores de validación
        \Log::info($validator->errors());

        parent::failedValidation($validator);
    }
}
