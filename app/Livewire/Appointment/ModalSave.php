<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use App\Models\ConsultingRoom;
use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use App\Models\Scopes\ConsultingRoomScope;
use App\Models\UserClient;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Validate;

class ModalSave extends Component
{
    #[Modelable]
    public $showModal;
    public $appointment;
    public $title;
    public $buttonSaveTitle='Guardar Cita';

    public $doctor_id = '';
    public $patient_id = '';
    public $appointment_date = '';
    public $appointment_time = '';
    public $service_type;
    public $duration = 30;
    public $status = 'booked';
    public $description = '';
    public $reason = '';
    public $notes = '';
    public $consulting_room_id;
    public $consultorios=[];
    public $medical_speciality_id='';
    public $especialidades=[];
    public $practitioners=[];
    public $confirm=false;
    public $client_id;

    protected $rules = [
        'patient_id' => 'required|exists:patients,id',
        'doctor_id' => 'required|exists:practitioners,id',
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required',
        'duration' => 'required|integer|min:15|max:240',
        //'status' => 'required|in:booked,confirmed,in-progress,fullfilled,cancelled,noshow',
        'consulting_room_id' => 'required|exists:consulting_rooms,id',
        'medical_speciality_id' => 'required|exists:medical_specialties,id',
        'service_type' => 'required|string',
        'description' => 'nullable|string',
        'notes' => 'nullable|string'
    ];

    protected $messages = [
        // 'patient_id.required' => 'Debe seleccionar un paciente.',
        'doctor_id.required' => 'Debe seleccionar un doctor.',
        'consulting_room_id.required' => 'Debe seleccionar un consultorio.',
        'appointment_date.required' => 'La fecha es obligatoria.',
        'appointment_time.required' => 'La hora es obligatoria.',
        'appointment_date.after_or_equal' => 'La fecha no puede ser anterior a hoy.'
    ];

    public function mount(){

        $this->loadEspecialidades();
        $this->loadDoctors();
        $this->loadConsultorios();

        if(auth()->user()->hasRole('paciente')) {
            $this->patient_id = auth()->user()->patient->id;
            $this->status='proposed';
        }
        if(auth()->user()->hasRole('doctor'))  $this->doctor_id = auth()->user()->practitioner->id;


    }

    public function render()
    {
        if(auth()->user()->getCurrentClient()) $this->client_id = auth()->user()->getCurrentClient()->id;
        return view('livewire.appointment.modal-save');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function resetForm()
    {
        $this->appointment = null;
        if(!auth()->user()->hasRole('paciente'))
            $this->patient_id = '';
        if(!auth()->user()->hasRole('doctor'))
            $this->doctor_id = '';

        $this->description = '';
        $this->appointment_date = Carbon::now()->format('Y-m-d');
        $this->appointment_time = '';
        $this->duration = 30;
        $this->status = 'booked';

        if(auth()->user()->hasRole('paciente')) $this->status = 'proposed';

        $this->consulting_room_id = '';
        $this->medical_speciality_id = '';
        $this->service_type='';
    }

    public function loadDoctors()
    {
        $this->practitioners = Practitioner::when($this->medical_speciality_id,function ($q){
            $q->whereHas('qualifications',function ($q){
                $q->where('medical_speciality_id',$this->medical_speciality_id);
            });
        })->get()->pluck('name','id')->toArray();

        $this->doctor_id='';
    }

    public function loadEspecialidades()
    {
        $esp= MedicalSpeciality::when(auth()->user()->hasRole('doctor'),function ($q){
            $q->whereIn('id',auth()->user()->practitioner->qualifications->pluck('medical_speciality_id'));
        })->orderBy('name')->get();

        $this->especialidades = $esp->pluck('name','id')->toArray();

        if($esp->count()==1){
            $this->medical_speciality_id=$esp->first()->id;
        }

    }

    public function loadConsultorios()
    {
        $this->consultorios =   ConsultingRoom::when($this->doctor_id,function ($q){
            $q->whereHas('branch',function ($q2){
                $practitioner = Practitioner::find($this->doctor_id);
                $q2->whereIn('client_id',$practitioner->user->clients->pluck('id'));
            });
        })->get()->pluck('full_name_branch','id')->toArray();
    }

    public function saveAppointment()
    {
        $this->validate();

        try {
            // Obtener información del doctor
            //$doctor = Practitioner::find($this->doctor_id);
            $rooms = ConsultingRoom::find($this->consulting_room_id);
            $client_id = $rooms->branch->client_id;

            $start = Carbon::parse($this->appointment_date.' '.$this->appointment_time);
            $original_requested_datetime=null;
            $practitioner_suggested_datetime=null;
            if($this->appointment){
                $original_requested_datetime=$this->appointment->original_requested_datetime;
                $practitioner_suggested_datetime=$this->appointment->practitioner_suggested_datetime;
            }

            if($this->confirm){
                $practitioner_suggested_datetime = $start->format('Y-m-d H:i');
                $this->status='booked';
            }else if($this->status=='proposed'){
                $original_requested_datetime = $start->format('Y-m-d H:i');
            }

            $appointmentData = [
                'fhir_id'=> 'appointment-' . Str::uuid(),
                'identifier' => 'APT-' . fake()->unique()->numerify('#######'),
                'patient_id' => $this->patient_id,
                'practitioner_id' => $this->doctor_id,
                'client_id'=>$client_id,
                'medical_speciality_id' =>$this->medical_speciality_id,
                'start' =>$start->format('Y-m-d H:i'),
                'end' => $start->addMinutes($this->duration)->format('Y-m-d H:i'),
                'minutes_duration' => $this->duration,
                'consulting_room_id'=>$this->consulting_room_id,
                'service_type'=>$this->service_type,
                'status' => $this->status,
                'description' => $this->description,
                'original_requested_datetime'=>$original_requested_datetime,
                'practitioner_suggested_datetime'=>$practitioner_suggested_datetime,
                'comment' => $this->notes
            ];
            // Verificar disponibilidad
            if (!$this->checkAvailability()) {
                $this->closeModal();
                session()->flash('error', 'El doctor no está disponible en ese horario.');
                return;
            }

            if ($this->appointment) {
                // Actualizar cita existente
                //$appointment = Appointment::find($this->editingAppointment);
                $this->appointment->update($appointmentData);
                if($this->confirm){
                    $this->appointment->notifyPatientAboutConfirmation();
                }

                session()->flash('message.success', 'Cita actualizada exitosamente.');
            } else {
                // Crear nueva cita

                $app = Appointment::create($appointmentData);

                if($this->status=='proposed'){
                    $app->addPatientToPractitionerClient();
                    $app->notifyPractitionerAboutProposal();
                }
                session()->flash('message.success', 'Cita creada exitosamente.');
            }

            $this->closeModal();
            $this->dispatch('loadAppointments');
            //$this->dispatch('loadStats');

        } catch (\Exception $e) {
            $this->closeModal();
            session()->flash('message.error', 'Error al guardar la cita: ' . $e->getMessage());
        }
    }

    public function rejectAppointment(){
        $this->appointment->status ='cancelled';
        $this->appointment->comment = $this->notes;
        $this->appointment->save();
        $this->appointment->notifyPatientAboutRejection($this->notes);
        session()->flash('message.success','Cita cancelada exitosamente , se le envio notificacion al paciente.');
        $this->closeModal();
        $this->dispatch('loadAppointments');
    }

    #[On('editAppointmentModal')]
    public function editAppointment($appointment_id)
    {
        $this->appointment = Appointment::find($appointment_id);
        $this->title='Actualizar Cita';
        $this->buttonSaveTitle = 'Actualizar Cita';

        if ($this->appointment) {

            if($this->appointment->status=='proposed') {
                $this->title='Confirmar Cita';
                $this->buttonSaveTitle = 'Confirmar Cita';
                $this->confirm=true;
                $this->status='booked';
            }

            $practitioner = Practitioner::find($this->appointment->practitioner_id);
            $clientId=null;
            $userClient = UserClient::whereUserId($practitioner->user_id)->first();
            if($userClient) $clientId= $userClient->client_id;

            $this->consultorios =   ConsultingRoom::whereHas('branch',function ($q) use($clientId){
                $q->whereClientId($clientId);
            })->pluck('name','id')->toArray();

            $this->especialidades = \App\Models\MedicalSpeciality::whereIn('id',$practitioner->qualifications->pluck('medical_speciality_id'))->pluck('name','id')->toArray();

            $this->practitioners = Practitioner::whereHas('qualifications',function ($q){
                $q->where('medical_speciality_id',$this->appointment->medical_speciality_id);
            })->get()->pluck('name','id')->toArray();

            $this->editingAppointment = $appointment_id;
            $this->modalTitle = 'Editar Cita';
            $this->patient_id =  $this->appointment->patient_id;
            $this->doctor_id =  $this->appointment->practitioner_id;
            $this->appointment_date =  $this->appointment->start->format('Y-m-d');
            $this->appointment_time =  $this->appointment->start->format('H:i');
            $this->duration =  $this->appointment->minutes_duration;
            $this->status =  $this->appointment->status;
            $this->medical_speciality_id =  $this->appointment->medical_speciality_id;
            $this->consulting_room_id =  $this->appointment->consulting_room_id;
            $this->service_type =  $this->appointment->service_type;
            $this->reason =  $this->appointment->description;
            $this->description =  $this->appointment->description;
            $this->notes = $this->appointment->comment;
            $this->canEdit = auth()->user()->can('edit',$this->appointment);
            $this->showModal = true;
        }
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

        if ($this->appointment) {
            $query->where('id', '!=', $this->appointment->id);
        }

        return $query->count() === 0;
    }
}
