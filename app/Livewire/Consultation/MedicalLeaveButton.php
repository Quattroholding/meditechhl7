<?php

namespace App\Livewire\Consultation;

use App\Jobs\SendMedicalLeaveEmail;
use App\Models\Condition;
use App\Models\Encounter;
use App\Models\MedicalLeave;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class MedicalLeaveButton extends Component
{
    public $encounter_id;

    public $encounter;

    public $hasDiagnoses = false;

    public $showModal = false;

    // Datos del formulario
    public $start_datetime;

    public $end_datetime;

    public $notes = '';

    public $selected_condition_id = null;

    protected function rules()
    {
        return [
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'notes' => 'nullable|string|max:1000',
            'selected_condition_id' => 'required|exists:conditions,id',
        ];
    }

    protected $messages = [
        'start_datetime.required' => 'La fecha y hora de inicio es obligatoria',
        'end_datetime.required' => 'La fecha y hora de fin es obligatoria',
        'end_datetime.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
        'selected_condition_id.required' => 'Debe seleccionar un diagnóstico',
        'selected_condition_id.exists' => 'El diagnóstico seleccionado no existe',
    ];

    public function mount()
    {
        $this->encounter = Encounter::with(['diagnoses.condition', 'patient', 'practitioner', 'appointment.consultingRoom.branch.client'])
            ->findOrFail($this->encounter_id);

        $this->checkDiagnoses();

        // Inicializar fechas por defecto (hoy a las 8am hasta mañana a las 8am)
        $this->start_datetime = now()->setTime(8, 0)->format('Y-m-d\TH:i');
        $this->end_datetime = now()->addDay()->setTime(8, 0)->format('Y-m-d\TH:i');
    }

    #[On('findFinishedButtonStatus')]
    public function checkDiagnoses()
    {
        $this->hasDiagnoses = $this->encounter->diagnoses()->count() > 0;

        // Seleccionar automáticamente el primer diagnóstico si existe
        if ($this->hasDiagnoses && ! $this->selected_condition_id) {
            $firstDiagnosis = $this->encounter->diagnoses()->first();
            $this->selected_condition_id = $firstDiagnosis->condition_id ?? null;
        }
    }

    public function openModal()
    {
        $this->checkDiagnoses();

        if (! $this->hasDiagnoses) {
            $this->dispatch('showToastrConsultation', [
                'type' => 'warning',
                'message' => 'Debe agregar al menos un diagnóstico antes de crear una incapacidad médica',
            ]);

            return;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save()
    {
        \Log::info('MedicalLeaveButton::save() llamado', [
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'selected_condition_id' => $this->selected_condition_id,
            'notes' => $this->notes,
        ]);

        // Validar formulario
        try {
            $this->validate();
        } catch (ValidationException $e) {
            \Log::error('Validación falló', ['errors' => $e->errors()]);
            // La validación falló - Livewire muestra los errores automáticamente
            session()->flash('error', 'Por favor corrija los errores en el formulario');

            return;
        }

        try {
            $patient = $this->encounter->patient;
            $practitioner = $this->encounter->practitioner;
            $appointment = $this->encounter->appointment;
            $consultingRoom = $this->encounter->appointment->consultingRoom ?? null;
            $branch = $consultingRoom?->branch;
            $client = $branch?->client;

            // Calcular días totales
            $start = Carbon::parse($this->start_datetime);
            $end = Carbon::parse($this->end_datetime);
            $totalDays = $start->diffInDays($end) + 1; // +1 para incluir el día final

            // Obtener el diagnóstico seleccionado
            $condition = Condition::findOrFail($this->selected_condition_id);

            $medicalLeave = MedicalLeave::create([
                'fhir_id' => 'medical-leave-'.Str::uuid(),
                'identifier' => 'LM-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),
                'patient_id' => $patient->id,
                'encounter_id' => $this->encounter->id,
                'condition_id' => $this->selected_condition_id,
                'practitioner_id' => $practitioner->id,
                'client_id' => $appointment->client_id,
                'consulting_room_id' => $appointment->consulting_room_id,
                'practitioner_name' => $practitioner->name,
                'practitioner_license_number' => $practitioner->registry ?? '',
                'clinic_name' => $client?->name ?? 'N/A',
                'clinic_address' => $branch?->address ?? 'N/A',
                'clinic_phone' => $branch?->phone ?? 'N/A',
                'clinic_logo_url' => $client?->logo ?? null,
                'patient_name' => $patient->name,
                'start_datetime' => $this->start_datetime,
                'end_datetime' => $this->end_datetime,
                'total_days' => $totalDays,
                'diagnosis' => $condition->onset_info ?? $condition->icd10Code?->description_es,
                'diagnosis_code' => $condition->code,
                'status' => 'active',
                'issue_date' => now()->toDateString(),
                'notes' => $this->notes,
            ]);

            // Enviar email al paciente si tiene email registrado
            if ($patient->email) {

                if (config('mail.testing_mode')) {
                    $email = config('mail.testing_patient_email');
                } else {
                    $email = $patient->email;
                }

                // Refrescar el modelo para asegurar que esté sincronizado con la BD
                $medicalLeave->refresh();

                // Despachar el Job después del commit de la transacción
                SendMedicalLeaveEmail::dispatch($medicalLeave, $email)
                    ->afterCommit();

                $this->dispatch('showToastrConsultation',
                    type: 'success',
                    message: "Incapacidad médica #{$medicalLeave->identifier} creada exitosamente. Se enviará por email a {$email}"
                );
            } else {
                $this->dispatch('showToastrConsultation',
                    type: 'success',
                    message: "Incapacidad médica #{$medicalLeave->identifier} creada exitosamente. El paciente no tiene email registrado."
                );
            }

            $this->closeModal();

            // Refrescar la página para ver la licencia
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            \Log::error('Error al crear licencia médica: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            // session()->flash('error', 'Error al crear la licencia médica: '.$e->getMessage());

            $this->dispatch('showToastrConsultation',
                type: 'error',
                message: 'Error al crear la incapacidad médica: '.$e->getMessage()
            );

        }
    }

    public function render()
    {
        return view('livewire.consultation.medical-leave-button');
    }
}
