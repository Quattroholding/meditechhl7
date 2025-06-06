<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;


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
        /*------VALIDACIÓN PARA ADMIN------*/
        case '1': 
        /*------VALIDACIÓN PARA ADMIN-CLIENT------*/
        case '5':
                $rules['first_name'] = 'required|string|max:255';
                $rules['last_name'] = 'required|string|max:255';
                $rules['email'] = 'required|email|unique:users,email';
                $rules['password'] = 'required|string|confirmed';//|min:8';
                $rules['avatar'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
                $rules['clients'] = 'required|array|min:1';
            break;
        /*------VALIDACIÓN PARA DOCTOR------*/
        case '2':
                $rules['first_name'] = 'required|string|max:255';
                $rules['last_name'] = 'required|string|max:255';
                $rules['email'] = 'required|email|unique:users,email';
                $rules['password'] = 'required|string|confirmed';//'required|string|min:8|confirmed'
                $rules['avatar'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
                $rules['id_type'] = 'required|string';
                $rules['id_number'] = 'required|integer';
                $rules['medical_speciality'] = 'required|array|min:1';
                $rules['gender'] = 'required|string';
                $rules['birth_date'] = 'required';
                $rules['address'] = 'required|string';
                $rules['phone'] = 'required';
                $rules['clients'] = 'required|array|min:1';
            break;
            /*------VALIDACIÓN PARA ASISTENTE------*/
        case '3':
                $rules['first_name'] = 'required|string|max:255';
                $rules['last_name'] = 'required|string|max:255';
                $rules['email'] = 'required|email|unique:users,email';
                $rules['password'] = 'required|string|confirmed';//'required|string|min:8|confirmed'
                $rules['avatar'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
                $rules['id_type'] = 'required|string';
                $rules['id_number'] = 'required|string';
                $rules['gender'] = 'required|string';
                $rules['birth_date'] = 'required|date';
                $rules['address'] = 'required|string';
                $rules['phone'] = 'required|string';
                $rules['clients'] = 'required|array|min:1';
            break;

        /*------VALIDACIÓN PARA PACIENTE------*/
        case '4': 
                $rules['first_name'] = 'required|string|max:255';
                $rules['last_name'] = 'required|string|max:255';
                $rules['email'] = 'required|email|unique:users,email';
                $rules['password'] = 'required|string|confirmed'; //'required|string|min:8|confirmed'
                $rules['avatar'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
                $rules['id_type'] = 'required|string';
                $rules['id_number'] = 'required|string';
                $rules['marital_status'] = 'required|string';
                $rules['gender'] = 'required|string';
                $rules['birth_date'] = 'required|date';
                $rules['address'] = 'required|string';
                $rules['phone'] = 'required|string';
            break;

        default:
            break;
    }
        return $rules;
    }

    protected function failedValidation(Validator $validator)
{
    // Registrar los errores de validación
    \Log::info($validator->errors());

    parent::failedValidation($validator);
}
}
