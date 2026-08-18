<?php

namespace App\Http\Requests;

use App\Models\Practitioner;
use Illuminate\Contracts\Validation\ValidationRule;
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rol = $this->input('rol');
        $userId = $this->route('user'); // ID del usuario si es edición

        // Reglas de validación base
        $rules['rol'] = 'required|integer';
        switch ($rol) {
            case '2':
                /* ------VALIDACIÓN PARA DOCTOR------ */
                $rules['first_name'] = 'required|string|max:255';
                $rules['last_name'] = 'required|string|max:255';
                $rules['email'] = 'required|email|unique:users,email'.($userId ? ','.$userId : '');
                /*$rules['password'] = [
                    'required',
                    'string',
                    'confirmed',
                    'min:8',
                    'regex:/[a-z]/',      // debe contener al menos una letra minúscula
                    'regex:/[A-Z]/',      // debe contener al menos una letra mayúscula
                    'regex:/[0-9]/',      // debe contener al menos un número
                ];*/
                $rules['id_number'] = 'required|string|max:255|unique:practitioners,identifier'.($this->getPractitionerId($userId) ? ','.$this->getPractitionerId($userId) : '');
                $rules['registry'] = 'nullable|string|max:60|unique:practitioners,registry'.($this->getPractitionerId($userId) ? ','.$this->getPractitionerId($userId) : '');
                $rules['clients'] = 'required|array|min:1';
                break;
            case '3':
                /* ------VALIDACIÓN PARA RECEPCIONISTA------ */
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
                $rules['avatar'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
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
            'id_number.required' => 'El número de identificación es obligatorio.',
            'id_number.unique' => 'Este número de identificación ya está registrado.',
            'registry.unique' => 'Este número de registro médico ya está registrado.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Registrar los errores de validación
        \Log::info($validator->errors());

        parent::failedValidation($validator);
    }

    /**
     * Get the practitioner ID for a given user ID (for edit validation)
     */
    private function getPractitionerId(?int $userId): ?int
    {
        if (! $userId) {
            return null;
        }

        $practitioner = Practitioner::where('user_id', $userId)->first();

        return $practitioner?->id;
    }
}
