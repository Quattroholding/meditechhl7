<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicPatientRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Esta es una ruta pública, siempre autorizada
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Identificación
            'id_type' => 'required|in:CC,CE,PA,PT,SS',
            'id_number' => ['required', 'regex:'.$this->getIdPattern()],

            // Información personal
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'birthdate' => 'required|date|before:today',

            // Contacto
            'email' => 'required|email|max:255|unique:patients,email',
            'full_phone' => [
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
            'physical_address' => 'required|string|max:500',

            // Información adicional
            'marital_status' => 'required|in:single,married,divorced,widowed,domestic_partner',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',

            // Contacto de emergencia (opcional)
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'full_contact_phone' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^\+[1-9]\d{1,14}$/',
            ],

            // Información de seguro médico (opcional)
            'has_insurance' => 'nullable|boolean',
            'insurance_company_id' => 'nullable|required_if:has_insurance,1|exists:insurance_companies,id',
            'policy_number' => 'nullable|required_if:has_insurance,1|string|max:100',
            'group_number' => 'nullable|string|max:100',
            'relationship_to_subscriber' => 'nullable|required_if:has_insurance,1|in:self,spouse,child,parent,other',
            'subscriber_name' => [
                'nullable',
                'string',
                'max:255',
                /*
                function ($attribute, $value, $fail) {
                    // Solo validar si tiene seguro Y la relación no es "self"
                    if ($this->input('has_insurance') &&
                        $this->input('relationship_to_subscriber') !== 'self' &&
                        empty($value)) {
                        $fail('El nombre del titular es obligatorio cuando no es el mismo paciente.');
                    }
                },*/
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            // Identificación
            'id_type.required' => 'El tipo de documento es obligatorio.',
            'id_type.in' => 'El tipo de documento no es válido.',
            'id_number.required' => 'El número de documento es obligatorio.',
            'id_number.regex' => 'El formato del número de documento no es válido.',

            // Información personal
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'gender.required' => 'El género es obligatorio.',
            'gender.in' => 'El género seleccionado no es válido.',
            'birthdate.required' => 'La fecha de nacimiento es obligatoria.',
            'birthdate.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'birthdate.before' => 'La fecha de nacimiento debe ser anterior a hoy.',

            // Contacto
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está registrado en nuestro sistema.',
            'full_phone.required' => 'El teléfono es obligatorio.',
            'full_phone.regex' => 'El teléfono debe incluir el código de país en formato internacional (ej: +507...).',
            'physical_address.required' => 'La dirección física es obligatoria.',

            // Información adicional
            'marital_status.required' => 'El estado civil es obligatorio.',
            'marital_status.in' => 'El estado civil seleccionado no es válido.',
            'blood_type.in' => 'El tipo de sangre seleccionado no es válido.',
            'country_id.exists' => 'El país seleccionado no es válido.',
            'state_id.exists' => 'El estado/provincia seleccionado no es válido.',

            // Contacto de emergencia
            'contact_email.email' => 'El correo del contacto de emergencia debe ser válido.',
            'full_contact_phone.regex' => 'El teléfono del contacto de emergencia debe incluir el código de país (ej: +507...).',

            // Seguro médico
            'insurance_company_id.required_if' => 'Debe seleccionar una compañía de seguros.',
            'insurance_company_id.exists' => 'La compañía de seguros seleccionada no es válida.',
            'policy_number.required_if' => 'El número de póliza es obligatorio cuando tiene seguro.',
            'relationship_to_subscriber.required_if' => 'Debe especificar la relación con el titular del seguro.',
            'relationship_to_subscriber.in' => 'La relación con el titular seleccionada no es válida.',
            'subscriber_name.required_unless' => 'El nombre del titular es obligatorio cuando no es el mismo paciente.',
        ];
    }

    /**
     * Get the regex pattern for the ID number based on ID type.
     */
    private function getIdPattern(): string
    {
        $idType = $this->input('id_type', 'CC');

        return match ($idType) {
            'CC' => '/^[0-9-]+$/', // Cédula de Ciudadanía: solo números y guiones
            'CE' => '/^[0-9-]+$/', // Cédula Extranjera: solo números y guiones
            'PA' => '/^[A-Z0-9-]{5,20}$/', // Pasaporte: alfanumérico
            'PT' => '/^[A-Z0-9-]{5,20}$/', // Permiso Temporal: alfanumérico
            'SS' => '/^[0-9-]+$/', // Seguro Social: números y guiones
            default => '/^[A-Z0-9-]{5,20}$/', // Universal: alfanumérico
        };
    }
}
