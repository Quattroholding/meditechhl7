<?php

namespace App\Livewire\Practitioner;

use App\Models\Appointment;
use App\Models\ConsultingRoom;
use App\Models\Practitioner;
use App\Models\PractitionerQualification;
use App\Models\Scopes\PractitionerScope;
use App\Models\UserClient;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class MedicalDirectory extends Component
{

    use WithPagination;

    public $search = '';
    public $selectedSpecialty = '';
    public $perPage = 12;
    public $showInactive = false;
    public $showModal = false;
    public  $patient_id;
    public $doctor_id=null;
    public $appointment_date = '';
    public $appointment_time = '';
    public $duration = 30;
    public $status = 'proposed';
    public $description = '';
    public $reason = '';
    public $notes = '';
    public $consulting_room_id;
    public $consultorios=[];
    public $service_type;
    public $medical_speciality_id='';
    public $especialidades=[];
    public $doctor_name;

    protected $rules = [
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required',
        'duration' => 'required|integer|min:15|max:240',
        'consulting_room_id' => 'required|exists:consulting_rooms,id',
        'medical_speciality_id' => 'required|exists:medical_specialties,id',
        'service_type' => 'required|string',
        'description' => 'nullable|string',
        'notes' => 'nullable|string'
    ];

    protected $messages = [
        // 'patient_id.required' => 'Debe seleccionar un paciente.',
        'doctor_id.required' => 'Debe seleccionar un doctor.',
        'appointment_date.required' => 'La fecha es obligatoria.',
        'appointment_time.required' => 'La hora es obligatoria.',
        'appointment_date.after_or_equal' => 'La fecha no puede ser anterior a hoy.'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedSpecialty' => ['except' => ''],
        'perPage' => ['except' => 12],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedSpecialty()
    {
        $this->resetPage();
    }

    public function updatingShowInactive()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedSpecialty']);
        $this->resetPage();
    }

    public function getSpecialtiesProperty()
    {
        // Obtener todas las especialidades únicas de las calificaciones
        return PractitionerQualification::orderBy('display')->pluck('display','medical_speciality_id');
    }

    public function render()
    {
        if(auth()->user()->patient)
            $this->patient_id = auth()->user()->patient->id;

        $practitioners = Practitioner::withoutGlobalScope(PractitionerScope::class)->with('qualifications')
            ->when(!$this->showInactive, fn($query) => $query->active())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $searchTerm = '%' . $this->search . '%';
                    $q->where('name','like',$searchTerm)
                      ->orWhere('identifier', 'like', $searchTerm);
                });
            })
            ->when($this->selectedSpecialty, function ($query) {
                $query->whereHas('qualifications', function ($q) {
                    $q->where('medical_speciality_id',$this->selectedSpecialty);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.practitioner.medical-directory', [
            'practitioners' => $practitioners,
            'specialties' => $this->specialties,
        ]);
    }

    public function requestAppointment($practitioner_id){
        /*$this->resetForm();
        $this->doctor_id  = $practitioner_id;
        $practitioner= Practitioner::find($this->doctor_id);
        $this->doctor_name = $practitioner->name;
        $this->especialidades = \App\Models\MedicalSpeciality::whereIn('id',$practitioner->qualifications->pluck('medical_speciality_id'))->pluck('name','id')->toArray();
        $clientId=null;
        $userClient = UserClient::whereUserId($practitioner->user_id)->first();
        if($userClient) $clientId= $userClient->client_id;
          $this->consultorios =   ConsultingRoom::whereHas('branch',function ($q) use($clientId){
            $q->whereClientId($clientId);
          })->pluck('name','id')->toArray();
          $this->showModal=true;*/

        $this->showModal=true;
    }

    public function saveAppointment()
    {
        $this->validate();

        try {
            // Obtener información del doctor
            $start = Carbon::parse($this->appointment_date.' '.$this->appointment_time);

            $appointmentData = [
                'fhir_id'=> 'appointment-' . Str::uuid(),
                'identifier' => 'APT-' . fake()->unique()->numerify('#######'),
                'patient_id' => $this->patient_id,
                'practitioner_id' => $this->doctor_id,
                'medical_speciality_id' =>$this->medical_speciality_id,
                'start' =>$start->format('Y-m-d H:i'),
                'end' => $start->addMinutes($this->duration)->format('Y-m-d H:i'),
                'minutes_duration' => $this->duration,
                'consulting_room_id'=>$this->consulting_room_id,
                'service_type'=>$this->service_type,
                'status' => $this->status,
                'description' => $this->description,
                //'notes' => $this->notes
            ];

            // Verificar disponibilidad
            if (!$this->checkAvailability()) {
                $this->closeModal();
                session()->flash('message.error', 'El doctor no está disponible en ese horario.');
                return;
            }

            // Crear nueva cita
            Appointment::create($appointmentData);
            session()->flash('message.success', 'Su cita fue enviada al medico exitosamente , debe esperar un correo de confirmacion de la misma con la fecha y hora para su asistencia.');


            $this->closeModal();

        } catch (\Exception $e) {
            $this->closeModal();
            session()->flash('message.error', 'Error al guardar la cita: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function resetForm()
    {
        $this->doctor_id = '';
        $this->description = '';
        $this->appointment_date = Carbon::now()->addWeek(1)->format('Y-m-d');
        $this->appointment_time = '';
        $this->duration = 30;
        $this->status = 'proposed';
        $this->consulting_room_id = '';
        $this->medical_speciality_id = '';
        $this->service_type='';
    }

    private function checkAvailability()
    {
        $startTime = Carbon::parse($this->appointment_date.' '.$this->appointment_time);
        $endTime = $startTime->copy()->addMinutes($this->duration);

        $query = Appointment::where('practitioner_id', $this->doctor_id)
            ->whereRaw("date_format(start,'%Y-%m-%d') = '".$this->appointment_date."'")
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($startTime, $endTime) {
                $q->where(function($q2) use ($startTime, $endTime) {
                    $q2->where('start', '<', $endTime)
                        ->where('end', '>', $startTime);
                });
            });


        return $query->count() === 0;
    }
}
