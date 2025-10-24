<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicationRequestRequest extends FormRequest
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
            'patient_id' => 'required|exists:patients,id',
            'practitioner_id' => 'required|exists:practitioners,id',
            'encounter_id' => 'nullable|exists:encounters,id',
            'medication_id' => 'nullable|exists:medicines,id',
            'medication' => 'nullable|string|max:255',
            'status' => 'required|in:active,completed,draft,stopped,cancelled,on-hold',
            'intent' => 'required|string|max:50',
            'priority' => 'nullable|in:routine,urgent,asap,stat',
            'reason' => 'nullable|string',
            'dosage_instruction' => 'nullable|array',
            'dosage_text' => 'nullable|string',
            'route' => 'nullable|string|max:100',
            'frequency' => 'nullable|string|max:100',
            'quantity' => 'nullable|numeric|min:0',
            'refills' => 'nullable|integer|min:0',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after:valid_from',
            'substitution_allowed' => 'nullable|boolean',
            'note' => 'nullable|string',
            'narcotic' => 'nullable|boolean',
            'client_id' => 'required|exists:clients,id',
            'branch_id' => 'required|exists:branches,id',
            'consulting_room_id' => 'nullable|exists:consulting_rooms,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'El paciente es obligatorio.',
            'patient_id.exists' => 'El paciente seleccionado no existe.',
            'practitioner_id.required' => 'El médico es obligatorio.',
            'practitioner_id.exists' => 'El médico seleccionado no existe.',
            'encounter_id.exists' => 'La consulta seleccionada no existe.',
            'medication_id.exists' => 'El medicamento seleccionado no existe.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser uno de los valores permitidos.',
            'intent.required' => 'La intención es obligatoria.',
            'priority.in' => 'La prioridad debe ser uno de los valores permitidos.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'quantity.min' => 'La cantidad no puede ser negativa.',
            'refills.integer' => 'Los refills deben ser un número entero.',
            'refills.min' => 'Los refills no pueden ser negativos.',
            'valid_from.date' => 'La fecha de inicio debe ser una fecha válida.',
            'valid_to.date' => 'La fecha de fin debe ser una fecha válida.',
            'valid_to.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'substitution_allowed.boolean' => 'La sustitución permitida debe ser verdadero o falso.',
            'narcotic.boolean' => 'El narcótico debe ser verdadero o falso.',
            'client_id.required' => 'El cliente es obligatorio.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'branch_id.required' => 'La sucursal es obligatoria.',
            'branch_id.exists' => 'La sucursal seleccionada no existe.',
            'consulting_room_id.exists' => 'El consultorio seleccionado no existe.',
        ];
    }
}
