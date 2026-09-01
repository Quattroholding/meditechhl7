<?php

namespace App\Livewire\Appointment;

use App\Enums\AppointmentCancelledReason;
use App\Models\Appointment;
use App\Models\AppointmentWaitlistEntry;
use App\Models\ConsultingRoom;
use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use App\Models\User;
use App\Models\UserClient;
use App\Models\UserWorkingHour;
use App\Services\WaitlistService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSave extends Component
{
    #[Modelable]
    public $showModal = false;

    public $appointment;

    public $title;

    public $modalTitle = '';

    public $editingAppointment = null;

    public $canEdit = false;

    public $buttonSaveTitle = 'Guardar Cita';

    public $doctor_id = '';

    public $patient_id = '';

    public $appointment_date = '';

    public $appointment_time = '';

    public $consultation_type = 'presencial';

    public $service_type;

    public $duration = 30;

    public $status = 'booked';

    public $description = '';

    public $reason = '';

    public $notes = '';

    public $consulting_room_id;

    public $consultorios = [];

    public $medical_speciality_id = '';

    public $especialidades = [];

    public $practitioners = [];

    public $confirm = false;

    public $client_id;

    public $clients = [];

    public $showCancelModal = false;

    public $cancellationReason = '';

    public $customCancellationReason = '';

    // Propiedades para lista de espera
    public $showWaitlistModal = false;

    public $waitlistUrgencyLevel = 'routine';

    public $waitlistPreferredDate = '';

    public $waitlistPreferredTime = '';

    public $waitlistIsFlexibleDate = true;

    public $waitlistIsFlexibleTime = true;

    public $waitlistMaxWaitDays = 30;

    public $waitlistReason = '';

    public $assisted_by = '';

    public $assistants = [];

    // Propiedades para detectar conflictos en tiempo real
    public $hasConflict = false;

    public $conflictingAppointment = null;

    public $conflictingPatientName = '';

    protected $rules = [
        'patient_id' => 'required|exists:patients,id',
        'doctor_id' => 'required|exists:practitioners,id',
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required',
        'duration' => 'required|integer|min:15|max:240',
        // 'status' => 'required|in:booked,confirmed,in-progress,fullfilled,cancelled,noshow',
        'consulting_room_id' => 'required|exists:consulting_rooms,id',
        'medical_speciality_id' => 'required|exists:medical_specialties,id',
        'service_type' => 'required|string',
        'description' => 'nullable|string',
        'notes' => 'nullable|string',
        'assisted_by' => 'nullable|exists:users,id',
    ];

    protected $messages = [
        // 'patient_id.required' => 'Debe seleccionar un paciente.',
        'doctor_id.required' => 'Debe seleccionar un doctor.',
        'consulting_room_id.required' => 'Debe seleccionar un consultorio.',
        'appointment_date.required' => 'La fecha es obligatoria.',
        'appointment_time.required' => 'La hora es obligatoria.',
        'appointment_date.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
    ];

    public function mount()
    {
        $this->loadDoctors();
        $this->loadEspecialidades();

        if (auth()->user()->hasRole('paciente')) {
            $this->patient_id = auth()->user()->patient->id;
            $this->status = 'proposed';
        }
        if (auth()->user()->hasRole('doctor')) {
            $this->doctor_id = auth()->user()->practitioner->id;
        }
    }

    public function loadData() {}

    public function render()
    {

        if (auth()->user()->getCurrentClient()) {
            $this->client_id = auth()->user()->getCurrentClient()->id;
        }

        return view('livewire.appointment.modal-save', [
            'cancellationReasons' => AppointmentCancelledReason::toArray(),
        ]);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    #[On('openAppointmentModal')]
    public function openModal($title, $date, $time)
    {
        $this->resetForm($date, $time);
        $this->showModal = true;
        $this->title = $title;
        $this->buttonSaveTitle = 'Guardar Cita';

        // Cargar consultorios si ya hay doctor y fecha
        if ($this->doctor_id && $this->appointment_date) {
            Log::info('openModal: Cargando consultorios', [
                'doctor_id' => $this->doctor_id,
                'appointment_date' => $this->appointment_date,
            ]);
            $this->loadConsultorios();
        }
    }

    #[On('openAppointmentModalWithPatient')]
    public function openModalWithPatient($patientId, $title = 'Nueva Cita', $date = null, $time = null)
    {
        $this->resetForm($date, $time);
        $this->patient_id = $patientId;
        $this->showModal = true;
        $this->title = $title;
        $this->buttonSaveTitle = 'Guardar Cita';

        // Cargar consultorios si ya hay doctor y fecha
        if ($this->doctor_id && $this->appointment_date) {
            $this->loadConsultorios();
        }
    }

    public function resetForm($date = null, $time = '')
    {
        $this->consulting_room_id = '';
        $this->medical_speciality_id = '';
        $this->service_type = '';
        $this->description = '';
        $this->appointment_date = $date ?? Carbon::now()->format('Y-m-d');
        $this->appointment_time = $time ?? '';
        $this->duration = 30;
        $this->status = 'booked';
        $this->assisted_by = '';
        $this->assistants = [];

        $this->appointment = null;
        if (! auth()->user()->hasRole('paciente')) {
            $this->patient_id = '';
        }
        if (! auth()->user()->hasRole('doctor')) {
            $this->doctor_id = '';
            Log::info('resetForm: Usuario NO es doctor, limpiando doctor_id');
        } else {
            // Si es doctor, mantener su ID
            if (auth()->user()->practitioner) {
                $this->doctor_id = auth()->user()->practitioner->id;
                $this->medical_speciality_id = auth()->user()->practitioner->qualifications()->first()->code;
            }
        }

        if (auth()->user()->hasRole('paciente')) {
            $this->status = 'proposed';
        }

    }

    public function loadDoctors()
    {
        if (! auth()->user()->practitioner or auth()->user()->hasRole('asistente medico')) {
            $this->practitioners = Practitioner::when($this->medical_speciality_id, function ($q) {
                $q->whereHas('qualifications', function ($q) {
                    $q->where('medical_speciality_id', $this->medical_speciality_id);
                });
            })
                ->whereHas('user', function ($q) {
                    $q->whereHas('roles', function ($q) {
                        $q->where('name', 'doctor');
                    });
                })
                ->withActiveSubscription()
                ->get()
                ->pluck('name', 'id')
                ->toArray();
        }

    }

    /**
     * Listener para cuando cambia la fecha de la cita
     */
    public function updatedAppointmentDate($value)
    {
        Log::info('updatedAppointmentDate called', [
            'date' => $value,
            'doctor_id' => $this->doctor_id,
        ]);

        $this->loadConsultorios();
        $this->consulting_room_id = '';
        $this->checkConflicts();

        Log::info('Consultorios after update', [
            'consultorios' => $this->consultorios,
        ]);
    }

    /**
     * Listener para cuando cambia la hora de la cita
     */
    public function updatedAppointmentTime($value)
    {
        $this->checkConflicts();
    }

    /**
     * Listener para cuando cambia el doctor
     */
    public function updatedDoctorId($value)
    {
        Log::info('updatedDoctorId called', [
            'doctor_id' => $value,
            'appointment_date' => $this->appointment_date,
        ]);

        if ($value) {
            $this->loadConsultorios();
            $this->loadAssistants();
            $this->consulting_room_id = '';
            $this->assisted_by = '';
        }

        Log::info('Consultorios after update', [
            'consultorios' => $this->consultorios,
        ]);
    }

    /**
     * Listener para cuando cambia la especialidad médica
     */
    public function updatedMedicalSpecialityId($value)
    {
        Log::info('updatedMedicalSpecialityId called', ['speciality_id' => $value]);
        if (! auth()->user()->practitioner or auth()->user()->hasRole('asistente medico')) {
            // Recargar la lista de doctores filtrados por especialidad

            $this->loadDoctors();
            // Limpiar doctor y consultorios seleccionados
            $this->doctor_id = '';
            $this->consulting_room_id = '';
            $this->consultorios = [];
        }

    }

    public function loadEspecialidades()
    {

        $this->clients = auth()->user()->clients()->pluck('client_id')->toArray();
        $esp = MedicalSpeciality::when(auth()->user()->hasRole('doctor'), function ($q) {
            $q->whereIn('id', auth()->user()->practitioner->qualifications->pluck('medical_speciality_id'));
        })->when(auth()->user()->hasRole('recepcionista') or auth()->user()->hasRole('asistente medico') or auth()->user()->hasRole('admin client'), function ($q) {
            $q->whereHas('practitionerQualifications.practitioner.user.clients', function ($q2) {
                $q2->whereIn('clients.id', $this->clients);
            });
        })->orderBy('name')->get();

        $this->especialidades = $esp->pluck('name', 'id')->toArray();

        if ($esp->count() == 1) {
            $this->medical_speciality_id = $esp->first()->id;
        }

    }

    public function loadConsultorios()
    {
        Log::info('loadConsultorios START', [
            'doctor_id' => $this->doctor_id,
            'appointment_date' => $this->appointment_date,
        ]);

        if (! $this->doctor_id) {
            $this->consultorios = [];
            Log::info('loadConsultorios: No doctor_id, consultorios vacíos');

            return;
        }

        $practitioner = Practitioner::find($this->doctor_id);
        if (! $practitioner) {
            $this->consultorios = [];
            Log::info('loadConsultorios: Practitioner no encontrado');

            return;
        }

        Log::info('Practitioner encontrado', [
            'practitioner_id' => $practitioner->id,
            'user_id' => $practitioner->user_id,
        ]);

        // Obtener el día de la semana de la fecha seleccionada
        $dayOfWeek = null;
        if ($this->appointment_date) {
            $date = Carbon::parse($this->appointment_date);
            $dayOfWeek = $this->getDayNameInSpanish($date);
            Log::info('Día de la semana calculado', ['dayOfWeek' => $dayOfWeek]);
        }

        // Verificar si el doctor tiene horarios configurados
        $workingHours = $this->getDoctorWorkingHours();

        Log::info('Working hours obtenidos', [
            'count' => $workingHours->count(),
            'days' => $workingHours->pluck('day_of_week')->toArray(),
        ]);

        if ($workingHours->isEmpty()) {
            // Si no tiene horarios configurados, mostrar todos los consultorios
            $this->consultorios = ConsultingRoom::whereHas('branch', function ($q2) use ($practitioner) {
                if ($practitioner->user) {
                    $q2->whereIn('client_id', $practitioner->user->clients->pluck('id'));
                }
            })->get()->pluck('full_name_branch', 'id')->toArray();

            Log::info('Sin horarios configurados, todos los consultorios', [
                'count' => count($this->consultorios),
            ]);
        } else {
            // Si tiene horarios configurados, filtrar por el día de la semana
            if ($dayOfWeek) {
                $workingHoursForDay = $workingHours->where('day_of_week', $dayOfWeek);

                Log::info('Filtrando por día', [
                    'dayOfWeek' => $dayOfWeek,
                    'count' => $workingHoursForDay->count(),
                ]);

                if ($workingHoursForDay->isEmpty()) {
                    // El doctor no trabaja este día
                    $this->consultorios = [];
                    Log::info('Doctor no trabaja este día, consultorios vacíos');
                } else {
                    // Obtener solo los consultorios donde trabaja ese día
                    $consultingRoomIds = $workingHoursForDay->pluck('consulting_room_id')->unique();
                    $this->consultorios = ConsultingRoom::whereIn('id', $consultingRoomIds)
                        ->get()
                        ->pluck('full_name_branch', 'id')
                        ->toArray();

                    Log::info('Consultorios del día', [
                        'room_ids' => $consultingRoomIds->toArray(),
                        'consultorios' => $this->consultorios,
                    ]);
                }
            } else {
                // Si no hay fecha seleccionada, mostrar todos los consultorios configurados
                $consultingRoomIds = $workingHours->pluck('consulting_room_id')->unique();
                $this->consultorios = ConsultingRoom::whereIn('id', $consultingRoomIds)
                    ->get()
                    ->pluck('full_name_branch', 'id')
                    ->toArray();

                Log::info('Sin fecha, todos los consultorios configurados', [
                    'room_ids' => $consultingRoomIds->toArray(),
                    'count' => count($this->consultorios),
                ]);
            }
        }

        Log::info('loadConsultorios END', [
            'consultorios_count' => count($this->consultorios),
            'consultorios' => array_keys($this->consultorios),
        ]);
    }

    /**
     * Cargar lista de asistentes médicos disponibles para el cliente del doctor
     */
    public function loadAssistants()
    {
        if (! $this->doctor_id) {
            $this->assistants = [];

            return;
        }

        $practitioner = Practitioner::find($this->doctor_id);
        if (! $practitioner || ! $practitioner->user) {
            $this->assistants = [];

            return;
        }

        // Obtener el cliente del doctor desde su primer cliente asignado
        $userClient = UserClient::where('user_id', $practitioner->user_id)->first();
        if (! $userClient) {
            $this->assistants = [];

            return;
        }

        // Obtener asistentes médicos del mismo cliente
        $assistants = User::whereHas('roles', function ($q) {
            $q->where('name', 'asistente medico');
        })
            ->whereHas('clients', function ($q) use ($userClient) {
                $q->where('client_id', $userClient->client_id);
            })
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        // Si hay un asistente actual asignado, asegurarse de que esté en la lista
        if ($this->assisted_by && ! isset($assistants[$this->assisted_by])) {
            $currentAssistant = User::find($this->assisted_by);
            if ($currentAssistant) {
                $assistants[$this->assisted_by] = $currentAssistant->full_name;
            }
        }

        $this->assistants = $assistants;

        Log::info('loadAssistants completed', [
            'doctor_id' => $this->doctor_id,
            'client_id' => $userClient->client_id,
            'assistants_count' => count($this->assistants),
            'assisted_by' => $this->assisted_by,
        ]);
    }

    /**
     * Obtener los horarios laborales del doctor
     */
    private function getDoctorWorkingHours()
    {
        if (! $this->doctor_id) {
            return collect();
        }

        $practitioner = Practitioner::find($this->doctor_id);
        if (! $practitioner || ! $practitioner->user_id) {
            return collect();
        }

        return UserWorkingHour::where('user_id', $practitioner->user_id)
            ->with(['branch', 'consultingRoom'])
            ->get();
    }

    /**
     * Convertir fecha a nombre de día en español
     */
    private function getDayNameInSpanish($date)
    {
        $dayNames = [
            0 => __('domingo'),
            1 => __('lunes'),
            2 => __('martes'),
            3 => __('miercoles'),
            4 => __('jueves'),
            5 => __('viernes'),
            6 => __('sabado'),
        ];

        return $dayNames[$date->dayOfWeek];
    }

    /**
     * Obtener el horario laboral del doctor para una fecha específica
     */
    public function getWorkingHourForDate($date)
    {
        $dayOfWeek = $this->getDayNameInSpanish(Carbon::parse($date));
        $workingHours = $this->getDoctorWorkingHours();

        return $workingHours->where('day_of_week', $dayOfWeek)->first();
    }

    /**
     * Verificar si el doctor trabaja en una fecha específica
     */
    public function isDoctorWorkingOnDate($date)
    {
        $workingHours = $this->getDoctorWorkingHours();

        // Si no tiene horarios configurados, puede trabajar cualquier día
        if ($workingHours->isEmpty()) {
            return true;
        }

        $dayOfWeek = $this->getDayNameInSpanish(Carbon::parse($date));

        return $workingHours->where('day_of_week', $dayOfWeek)->isNotEmpty();
    }

    public function saveAppointment()
    {
        // dd(gettype($this->duration),$this->duration);
        $this->validate();

        try {
            // Obtener información del doctor
            // $doctor = Practitioner::find($this->doctor_id);
            $rooms = ConsultingRoom::find($this->consulting_room_id);
            $client_id = $rooms->branch->client_id;

            $start = Carbon::parse($this->appointment_date.' '.$this->appointment_time);
            $original_requested_datetime = null;
            $practitioner_suggested_datetime = null;
            $hasDateTimeChanged = false;

            if ($this->appointment) {
                $original_requested_datetime = $this->appointment->original_requested_datetime;
                $practitioner_suggested_datetime = $this->appointment->practitioner_suggested_datetime;
                // Detectar si cambió la fecha/hora
                $originalStart = $this->appointment->start->copy();
                $newStart = $start->copy();
                $hasDateTimeChanged = ! $originalStart->equalTo($newStart);
            }

            // Verificar disponibilidad solo si es nueva cita o si cambió fecha/hora
            if (! $this->appointment || $hasDateTimeChanged) {
                if (! $this->checkAvailability()) {
                    // Mostrar modal de lista de espera
                    $this->showWaitlistOptions();

                    return;
                }
            }

            if ($this->confirm) {
                $practitioner_suggested_datetime = $start->format('Y-m-d H:i');
                $this->status = 'booked';
            } elseif ($this->status == 'proposed') {
                $original_requested_datetime = $start->format('Y-m-d H:i');
            }
            $minutes = (int) $this->duration;
            $appointmentData = [
                'fhir_id' => 'appointment-'.Str::uuid(),
                'identifier' => 'APT-'.strtoupper(Str::random(7)),
                'patient_id' => $this->patient_id,
                'practitioner_id' => $this->doctor_id,
                'client_id' => $client_id,
                'medical_speciality_id' => $this->medical_speciality_id,
                'start' => $start->format('Y-m-d H:i'),
                'end' => $start->addMinutes($minutes)->format('Y-m-d H:i'),
                'minutes_duration' => $this->duration,
                'consulting_room_id' => $this->consulting_room_id,
                'service_type' => $this->service_type,
                'status' => $this->status,
                'consultation_type' => $this->consultation_type,
                'description' => $this->description,
                'original_requested_datetime' => $original_requested_datetime,
                'practitioner_suggested_datetime' => $practitioner_suggested_datetime,
                'comment' => $this->notes,
                'assisted_by' => $this->assisted_by ?: null,
            ];

            if ($this->appointment) {
                // Actualizar cita existente
                // Actualizar la cita
                $this->appointment->update($appointmentData);

                if ($this->confirm) {
                    $this->appointment->notifyPatientAboutConfirmation();
                }

                // Si cambió la fecha/hora y la cita está confirmada, notificar al paciente
                if ($hasDateTimeChanged && $this->appointment->status->value === 'booked') {

                    $this->appointment->notifyPatientAboutReschedule($originalStart, $this->notes);

                    // Si el usuario que cambia la cita no es el médico, notificar también al médico
                    $currentUser = auth()->user();
                    $isPractitioner = $currentUser->hasRole('doctor') &&
                                    $this->appointment->practitioner->user_id === $currentUser->id;

                    if (! $isPractitioner) {
                        $changedBy = $currentUser->first_name.' '.$currentUser->last_name;
                        $this->appointment->notifyPractitionerAboutReschedule($originalStart, $this->notes, $changedBy);

                        Log::info('Médico notificado sobre reprogramación de cita por otro usuario', [
                            'appointment_id' => $this->appointment->id,
                            'practitioner_id' => $this->appointment->practitioner_id,
                            'changed_by' => $changedBy,
                            'changed_by_user_id' => $currentUser->id,
                        ]);
                    }

                    // Clear reminder tracking to allow new reminder for rescheduled datetime
                    $this->appointment->clearReminderTracking();

                    // Schedule new reminder with the new datetime
                    $this->appointment->notifyPatientAboutAppointment();

                    Log::info('Cita reprogramada - notificación enviada y recordatorio reprogramado', [
                        'appointment_id' => $this->appointment->id,
                        'original_datetime' => $originalStart->format('Y-m-d H:i:s'),
                        'new_datetime' => $newStart->format('Y-m-d H:i:s'),
                    ]);
                }
                // Si la cita está confirmada (booked) pero no cambió la hora, enviar notificaciones normales
                elseif ($this->appointment->status === 'booked' && ! $hasDateTimeChanged) {
                    // Notificación inmediata de confirmación
                    $this->appointment->notifyPatientAboutBooking();
                    // Programar recordatorio para 2 horas antes
                    $this->appointment->notifyPatientAboutAppointment();
                }

                session()->flash('message.success', 'Cita actualizada exitosamente.');
                $this->dispatch('showToastrModalSave',
                    type: 'success',
                    message: 'Cita actualizada exitosamente.',
                );

            } else {
                // Crear nueva cita

                $app = Appointment::create($appointmentData);

                if ($this->status == 'proposed') {
                    $app->addPatientToPractitionerClient();
                    $app->notifyPractitionerAboutProposal();
                } elseif ($this->status === 'booked') {
                    // Si la cita se crea directamente como confirmada, enviar notificaciones
                    $app->addPatientToPractitionerClient();
                    // Notificación al paciente
                    $app->notifyPatientAboutBooking();
                    // Notificación al médico con botones de acción
                    $app->notifyPractitionerAboutBooking();
                    // Programar recordatorio para 2 horas antes
                    $app->notifyPatientAboutAppointment();
                }

                session()->flash('message.success', 'Cita creada exitosamente.');
                $this->dispatch('showToastrModalSave',
                    type: 'success',
                    message: 'Cita creada exitosamente.',
                );
            }

            $this->closeModal();
            $this->dispatch('loadAppointments');
            // $this->dispatch('loadStats');

        } catch (\Exception $e) {
            Log::error('Error guardando cita en ModalSave::saveAppointment', [
                'user_id' => Auth::id(),
                'appointment_id' => $this->appointment?->id,
                'patient_id' => $this->patient_id,
                'doctor_id' => $this->doctor_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->closeModal();
            session()->flash('message.error', 'Error al guardar la cita: '.$e->getMessage());
            $this->dispatch('showToastrModalSave',
                type: 'error',
                message: 'Error al guardar la cita: '.$e->getMessage(),
            );

        }
    }

    public function rejectAppointment()
    {
        $this->appointment->status = 'cancelled';
        $this->appointment->comment = $this->notes;
        $this->appointment->save();
        $this->appointment->notifyPatientAboutRejection($this->notes);
        session()->flash('message.success', 'Cita cancelada exitosamente , se le envio notificacion al paciente.');
        $this->closeModal();
        $this->dispatch('loadAppointments');
        $this->dispatch('showToastrModalSave',
            type: 'success',
            message: 'Cita cancelada exitosamente , se le envio notificacion al paciente.',
        );
    }

    #[On('editAppointmentModal')]
    public function editAppointment($appointment_id)
    {
        $this->appointment = Appointment::find($appointment_id);
        $this->title = 'Actualizar Cita';
        $this->buttonSaveTitle = 'Actualizar Cita';

        if ($this->appointment) {

            if ($this->appointment->status == 'proposed' && auth()->user()->can('booked', $this->appointment)) {
                $this->title = 'Confirmar Cita';
                $this->buttonSaveTitle = 'Confirmar Cita';
                $this->confirm = true;
                $this->status = 'booked';
            }

            $practitioner = Practitioner::find($this->appointment->practitioner_id);

            $clientId = null;
            $userClient = UserClient::whereUserId($practitioner->user_id)->first();
            if ($userClient) {
                $clientId = $userClient->client_id;
            }

            $this->consultorios = ConsultingRoom::whereHas('branch', function ($q) use ($clientId) {
                $q->whereClientId($clientId);
            })->pluck('name', 'id')->toArray();

            $this->especialidades = MedicalSpeciality::whereIn('id', $practitioner->qualifications->pluck('medical_speciality_id'))->pluck('name', 'id')->toArray();

            $this->practitioners = Practitioner::whereHas('qualifications', function ($q) {
                $q->where('medical_speciality_id', $this->appointment->medical_speciality_id);
            })
                ->whereHas('user', function ($q) {
                    $q->whereHas('roles', function ($q) {
                        $q->where('name', 'doctor');
                    });
                })
                ->get()->pluck('name', 'id')->toArray();

            $this->editingAppointment = $appointment_id;
            $this->modalTitle = 'Editar Cita';
            $this->patient_id = $this->appointment->patient_id;
            $this->doctor_id = $this->appointment->practitioner_id;
            $this->appointment_date = $this->appointment->start->format('Y-m-d');
            $this->appointment_time = $this->appointment->start->format('H:i');
            $this->duration = $this->appointment->minutes_duration;
            $this->status = $this->appointment->status;
            $this->medical_speciality_id = $this->appointment->medical_speciality_id;
            $this->consulting_room_id = $this->appointment->consulting_room_id;
            $this->service_type = $this->appointment->service_type;
            $this->reason = $this->appointment->description;
            $this->description = $this->appointment->description;
            $this->notes = $this->appointment->comment;
            $this->assisted_by = $this->appointment->assisted_by;
            $this->canEdit = auth()->user()->can('edit', $this->appointment);
            $this->loadAssistants();
            $this->showModal = true;
            // $this->dispatch('cita-message', message: 'Cita actualizada exitosamente.');
        }
    }

    public function deleteAppointment($appointmentId)
    {
        try {
            $appointment = Appointment::find($appointmentId);
            if ($appointment) {
                $appointment->delete();
                session()->flash('message.success', 'Cita eliminada exitosamente.');
                $this->closeModal();
                $this->dispatch('loadAppointments');
                $this->dispatch('showToastrModalSave',
                    type: 'success',
                    message: 'Cita eliminada exitosamente.',
                );
            }
        } catch (\Exception $e) {
            Log::error('Error eliminando cita en ModalSave::deleteAppointment', [
                'user_id' => Auth::id(),
                'appointment_id' => $appointmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('message.error', 'Error al eliminar la cita.');
            $this->dispatch('showToastrModalSave',
                type: 'error',
                message: 'Error al eliminar la cita. '.$e->getMessage(),
            );
        }
    }

    public function openCancelModal()
    {
        $this->showModal = false; // Cerrar el modal principal
        $this->showCancelModal = true;
    }

    public function confirmCancellation()
    {

        $this->validate([
            'cancellationReason' => 'required',
            'customCancellationReason' => 'required_if:cancellationReason,OTHER',
        ], [
            'cancellationReason.required' => 'Debe seleccionar una razón de cancelación',
            'customCancellationReason.required_if' => 'Debe especificar la razón cuando selecciona "Otra razón"',
        ]);

        try {
            if (! $this->appointment) {
                throw new \Exception('No se encontró la cita para cancelar');
            }

            $oldStatus = $this->appointment->status;

            // ⚠️ IMPORTANTE: Registrar espacio liberado ANTES de cambiar el status
            $freedSlot = null;
            if (in_array($oldStatus->value, ['booked', 'confirm', 'arrived'])) {
                $waitlistService = app(WaitlistService::class);
                $freedSlot = $waitlistService->registerFreedSlot($this->appointment, 'cancellation');

                \Log::info('ModalSave::confirmCancellation - registerFreedSlot resultado', [
                    'appointment_id' => $this->appointment->id,
                    'oldStatus' => $oldStatus->value,
                    'freedSlot_id' => $freedSlot?->id ?? 'NULL',
                    'freed_slot_data' => $freedSlot ? [
                        'date' => $freedSlot->slot_date,
                        'start_time' => $freedSlot->slot_start_time,
                        'practitioner_id' => $freedSlot->practitioner_id,
                    ] : null,
                ]);
            }

            // Determinar la razón final
            $finalReason = $this->cancellationReason === 'OTHER'
                ? $this->customCancellationReason
                : AppointmentCancelledReason::{$this->cancellationReason}->value;

            // Ahora sí cambiar el status a cancelled
            $this->appointment->status = 'cancelled';
            $this->appointment->save();

            // Guardar la razón de cancelación en el registro de historial de status
            $this->appointment->statusHistory()->first()?->update(['observation' => $finalReason]);

            // Mostrar modal de asignación manual si hay freedSlot y está configurado para ello
            if ($freedSlot && ! $this->appointment->client->getSettings('waitlist_auto_assign', false)) {
                $this->dispatch('show-manual-assignment', slotId: $freedSlot->id);
            }

            // Enviar notificación con la razón
            $this->appointment->notifyPatientAboutCancellation($finalReason);

            // Emitir evento para actualizar el calendario
            $this->dispatch('loadAppointments');
            $this->dispatch('appointmentStatusChanged',
                appointment_id: $this->appointment->id,
                new_status: 'cancelled'
            );

            session()->flash('message.success', '¡Cita cancelada, se envió notificación al paciente!');

            // Cerrar modales y limpiar
            $this->closeCancelModal();
            $this->closeModal();

        } catch (\Exception $e) {
            Log::error('Error cancelando cita en ModalSave::confirmCancellation', [
                'user_id' => Auth::id(),
                'appointment_id' => $this->appointment?->id,
                'cancellation_reason' => $this->cancellationReason,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('message.error', 'Error al cancelar la cita: '.$e->getMessage());
        }
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancellationReason = '';
        $this->customCancellationReason = '';
        $this->resetValidation();
    }

    /**
     * Mostrar opciones de lista de espera cuando no hay disponibilidad
     */
    public function showWaitlistOptions(): void
    {
        // Pre-rellenar con la fecha y hora solicitadas
        $this->waitlistPreferredDate = $this->appointment_date;
        $this->waitlistPreferredTime = $this->appointment_time;
        $this->waitlistReason = $this->reason;

        $this->dispatch('showToastrModalSave',
            type: 'info',
            message: 'El doctor no está disponible en ese horario. Puedes agregarte a la lista de espera.',
        );

        $this->showWaitlistModal = true;
    }

    /**
     * Agregar paciente a la lista de espera
     */
    public function addToWaitlist(): void
    {
        try {
            // Verificar que no exista una entrada activa en waitlist para este paciente/doctor/fecha
            $existingEntry = AppointmentWaitlistEntry::where('patient_id', $this->patient_id)
                ->where('practitioner_id', $this->doctor_id)
                ->where('status', 'active')
                ->whereDate('created_at', today())
                ->first();

            if ($existingEntry) {
                throw new \Exception('Ya tienes una entrada en la lista de espera activa para este doctor. Por favor, intenta más tarde.');
            }

            // Crear cita provisional para la lista de espera
            $start = Carbon::parse($this->appointment_date.' '.$this->appointment_time);

            $appointmentData = [
                'fhir_id' => 'appointment-'.Str::uuid(),
                'identifier' => 'APT-'.strtoupper(Str::random(7)),
                'patient_id' => $this->patient_id,
                'practitioner_id' => $this->doctor_id,
                'client_id' => ConsultingRoom::find($this->consulting_room_id)->branch->client_id,
                'medical_speciality_id' => $this->medical_speciality_id,
                'start' => $start->format('Y-m-d H:i'),
                'end' => $start->copy()->addMinutes((int) $this->duration)->format('Y-m-d H:i'),
                'minutes_duration' => (int) $this->duration,
                'consulting_room_id' => $this->consulting_room_id,
                'service_type' => $this->service_type,
                'status' => 'waitlist', // Estado de lista de espera
                'consultation_type' => $this->consultation_type,
                'description' => $this->description,
                'original_requested_datetime' => $start->format('Y-m-d H:i'),
            ];

            $appointment = Appointment::create($appointmentData);

            // Usar el servicio WaitlistService
            $waitlistService = app(WaitlistService::class);
            $waitlistService->addToWaitlist($appointment, [
                'urgency_level' => $this->waitlistUrgencyLevel,
                'preferred_date' => $this->waitlistPreferredDate ? Carbon::parse($this->waitlistPreferredDate) : null,
                'preferred_time' => $this->waitlistPreferredTime ? Carbon::createFromTimeString($this->waitlistPreferredTime) : null,
                'is_flexible_date' => $this->waitlistIsFlexibleDate,
                'is_flexible_time' => $this->waitlistIsFlexibleTime,
                'max_wait_days' => $this->waitlistMaxWaitDays,
                'reason' => $this->waitlistReason,
            ], auth()->user());

            // Notificar al paciente
            $appointment->addPatientToPractitionerClient();

            $this->dispatch('showToastrModalSave',
                type: 'success',
                message: 'Te has agregado a la lista de espera exitosamente. Te notificaremos cuando haya disponibilidad.',
            );

            $this->closeWaitlistModal();
            $this->closeModal();
            $this->dispatch('loadAppointments');

        } catch (\Exception $e) {
            Log::error('Error agregando paciente a waitlist en ModalSave::addToWaitlist', [
                'user_id' => Auth::id(),
                'patient_id' => $this->patient_id,
                'doctor_id' => $this->doctor_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('showToastrModalSave',
                type: 'error',
                message: 'Error al agregar a la lista de espera: '.$e->getMessage(),
            );
        }
    }

    /**
     * Cerrar modal de lista de espera
     */
    public function closeWaitlistModal(): void
    {
        $this->showWaitlistModal = false;
        $this->resetWaitlistForm();
        $this->resetValidation();
    }

    /**
     * Resetear formulario de lista de espera
     */
    private function resetWaitlistForm(): void
    {
        $this->waitlistUrgencyLevel = 'routine';
        $this->waitlistPreferredDate = '';
        $this->waitlistPreferredTime = '';
        $this->waitlistIsFlexibleDate = true;
        $this->waitlistIsFlexibleTime = true;
        $this->waitlistMaxWaitDays = 30;
        $this->waitlistReason = '';
    }

    /**
     * Verificar conflictos con otras citas en tiempo real
     */
    private function checkConflicts(): void
    {
        // Limpiar conflicto anterior
        $this->hasConflict = false;
        $this->conflictingAppointment = null;
        $this->conflictingPatientName = '';

        // Si no hay doctor, fecha u hora seleccionada, no verificar
        if (! $this->doctor_id || ! $this->appointment_date || ! $this->appointment_time) {
            return;
        }

        $minutes = (int) $this->duration;
        $startTime = Carbon::parse($this->appointment_date.' '.$this->appointment_time);
        $endTime = $startTime->copy()->addMinutes($minutes);

        // Buscar conflictos con otras citas
        $query = Appointment::where('practitioner_id', $this->doctor_id)
            ->whereDate('start', $this->appointment_date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start', '<', $endTime)
                    ->where('end', '>', $startTime);
            });

        // Excluir la cita actual si estamos editando
        if ($this->appointment) {
            $query->where('id', '!=', $this->appointment->id);
        }

        $conflicting = $query->with('patient')->first();

        if ($conflicting) {
            $this->hasConflict = true;
            $this->conflictingAppointment = $conflicting;
            $this->conflictingPatientName = $conflicting->patient?->profile_name ?? 'Paciente desconocido';

            Log::info('Conflicto detectado', [
                'doctor_id' => $this->doctor_id,
                'requested_time' => $startTime->format('Y-m-d H:i'),
                'conflicting_appointment_id' => $conflicting->id,
                'conflicting_patient' => $this->conflictingPatientName,
                'conflicting_start' => $conflicting->start->format('Y-m-d H:i'),
            ]);
        }
    }

    private function checkAvailability()
    {
        $minutes = (int) $this->duration;
        $startTime = Carbon::parse($this->appointment_date.' '.$this->appointment_time);
        $endTime = $startTime->copy()->addMinutes($minutes);

        Log::info('checkAvailability() iniciado', [
            'doctor_id' => $this->doctor_id,
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'startTime' => $startTime->format('Y-m-d H:i'),
            'endTime' => $endTime->format('Y-m-d H:i'),
            'duration' => $minutes,
        ]);

        // Verificar si el doctor tiene horarios configurados
        $workingHours = $this->getDoctorWorkingHours();

        Log::info('checkAvailability() - Working hours retrieved', [
            'doctor_id' => $this->doctor_id,
            'working_hours_count' => $workingHours->count(),
        ]);

        if ($workingHours->isNotEmpty()) {
            // Validar que el doctor trabaje ese día
            if (! $this->isDoctorWorkingOnDate($this->appointment_date)) {
                Log::info('checkAvailability() - Doctor not working on this day', [
                    'doctor_id' => $this->doctor_id,
                    'date' => $this->appointment_date,
                ]);
                $this->addError('appointment_date', 'El doctor no trabaja este día de la semana según su configuración de horarios.');

                return false;
            }

            // Validar que la hora esté dentro del horario laboral
            $workingHour = $this->getWorkingHourForDate($this->appointment_date);

            if ($workingHour) {
                $workStart = Carbon::parse($this->appointment_date.' '.$workingHour->start_time);
                $workEnd = Carbon::parse($this->appointment_date.' '.$workingHour->end_time);

                Log::info('checkAvailability() - Validating working hours', [
                    'workStart' => $workStart->format('Y-m-d H:i'),
                    'workEnd' => $workEnd->format('Y-m-d H:i'),
                    'startTime' => $startTime->format('Y-m-d H:i'),
                    'endTime' => $endTime->format('Y-m-d H:i'),
                ]);

                if ($startTime->lt($workStart) || $endTime->gt($workEnd)) {
                    Log::info('checkAvailability() - Appointment outside working hours', [
                        'doctor_id' => $this->doctor_id,
                        'start_time' => $this->appointment_time,
                        'working_hours' => $workingHour->start_time.' - '.$workingHour->end_time,
                    ]);
                    $this->addError('appointment_time',
                        "La cita debe estar dentro del horario laboral del doctor: {$workingHour->start_time} - {$workingHour->end_time}"
                    );

                    return false;
                }

                // Validar que el consultorio seleccionado sea el configurado para ese día
                if ($this->consulting_room_id != $workingHour->consulting_room_id) {
                    Log::info('checkAvailability() - Consulting room mismatch', [
                        'doctor_id' => $this->doctor_id,
                        'selected_room' => $this->consulting_room_id,
                        'required_room' => $workingHour->consulting_room_id,
                    ]);
                    $roomName = $workingHour->consultingRoom->full_name_branch ?? 'N/A';
                    $this->addError('consulting_room_id',
                        "El doctor trabaja en '{$roomName}' este día. Por favor, seleccione el consultorio correcto."
                    );

                    return false;
                }
            }
        }

        // Verificar conflictos con otras citas
        $query = Appointment::where('practitioner_id', $this->doctor_id)
            ->whereDate('start', $this->appointment_date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start', '<', $endTime)
                        ->where('end', '>', $startTime);
                });
            });

        if ($this->appointment) {
            $query->where('id', '!=', $this->appointment->id);
        }

        $conflictingAppointments = $query->count();
        Log::info('checkAvailability() - Checking for conflicts', [
            'doctor_id' => $this->doctor_id,
            'date' => $this->appointment_date,
            'conflicting_appointments' => $conflictingAppointments,
        ]);

        if ($conflictingAppointments > 0) {
            Log::info('checkAvailability() - Conflict found', [
                'doctor_id' => $this->doctor_id,
                'start_time' => $startTime->format('Y-m-d H:i'),
                'end_time' => $endTime->format('Y-m-d H:i'),
            ]);
            $this->addError('appointment_time', 'El doctor ya tiene una cita programada en ese horario.');

            return false;
        }

        Log::info('checkAvailability() - Check passed, availability confirmed', [
            'doctor_id' => $this->doctor_id,
            'appointment_date' => $this->appointment_date,
        ]);

        return true;
    }
}
