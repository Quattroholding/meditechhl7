<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use App\Models\ConsultingRoom;
use App\Models\Practitioner;
use App\Models\User;
use App\Models\UserClient;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class Calendar extends Component
{
    // Propiedades del calendario
    public $currentView = 'daily';
    public $currentDate;
    public $selectedDate;

    // Modal y formulario
    public $showModal = false;
    public $editingAppointment = null;
    public $modalTitle = 'Nueva Cita';

    // Campos del formulario
    public $patient_name = '';
    public $patient_phone = '';
    public $doctor_id = '';
    public $patient_id = '';
    public $appointment_date = '';
    public $appointment_time = '';
    public $duration = 30;
    public $status = 'scheduled';
    public $description = '';
    public $reason = '';
    public $notes = '';
    public $consulting_room_id;
    public $consultorios=[];
    public $service_type;
    public $medical_speciality_id='';
    public $especialidades=[];

    // Filtros
    public $selectedDoctor = '';
    public $selectedStatus = '';
    public $searchTerm = '';

    // Datos
    public $appointments = [];
    public $doctors = [];
    public $stats = [];

    protected $rules = [
        'patient_id' => 'required|exists:patients,id',
        'doctor_id' => 'required|exists:practitioners,id',
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required',
        'duration' => 'required|integer|min:15|max:240',
        'status' => 'required|in:booked,confirmed,in-progress,fullfilled,cancelled,noshow',
        'consulting_room_id' => 'required|exists:consulting_rooms,id',
        'medical_speciality_id' => 'required|exists:medical_specialties,id',
        'service_type' => 'required|string',
        'description' => 'nullable|string',
        'notes' => 'nullable|string'
    ];

    protected $messages = [
        'patient_name.required' => 'El nombre del paciente es obligatorio.',
        'doctor_id.required' => 'Debe seleccionar un doctor.',
        'appointment_date.required' => 'La fecha es obligatoria.',
        'appointment_time.required' => 'La hora es obligatoria.',
        'appointment_date.after_or_equal' => 'La fecha no puede ser anterior a hoy.'
    ];

    // NUEVAS PROPIEDADES PARA CONFIGURACIÓN DE TIEMPO
    public $timeBlockMinutes = 30; // Bloques de 30 minutos por defecto
    public $startHour = 7; // Hora de inicio del calendario
    public $endHour = 21; // Hora de fin del calendario
    public $showTimeBlockConfig = false; // Para mostrar/ocultar configuración

    public $timeBlockOptions = [
        15 => '15 minutos',
        30 => '30 minutos',
        60 => '60 minutos'
    ];

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->appointment_date = Carbon::now()->format('Y-m-d');

        $this->timeBlockMinutes = session('calendar.time_block_minutes', 30);
        $this->startHour = session('calendar.start_hour', 7);
        $this->endHour = session('calendar.end_hour', 21);

        $this->loadDoctors();
        $this->loadAppointments();
        $this->loadStats();
    }

    public function render()
    {
        return view('livewire.appointment.calendar', [
            'calendarData' => $this->getCalendarData(),
            'currentPeriod' => $this->getCurrentPeriod()
        ]);
    }

    // ===========================================
    // MÉTODOS DE NAVEGACIÓN
    // ===========================================

    public function changeView($view)
    {
        $this->currentView = $view;
        $this->loadAppointments();
    }

    public function navigateCalendar($direction)
    {
        if ($this->currentView === 'monthly') {
            $this->currentDate->addMonths($direction);
        } elseif ($this->currentView === 'weekly') {
            $this->currentDate->addWeeks($direction);
        } elseif ($this->currentView === 'daily') {
            $this->currentDate->addDays($direction);
        }

        $this->loadAppointments();
    }

    public function goToToday()
    {
        $this->currentDate = Carbon::now();
        $this->loadAppointments();
    }

    // ===========================================
    // MÉTODOS DE DATOS
    // ===========================================

    public function loadDoctors()
    {
        $this->doctors = Practitioner::select('id', 'name', 'email')
            ->get()
            ->toArray();
    }

    public function loadAppointments()
    {
        $query = Appointment::with('practitioner:id,name')
            ->with('patient:id,name,phone')
            ->with('medicalSpeciality:id,name')
            ->selectRaw('appointments.*')
            ->leftJoin('patients','patients.id','=','appointments.patient_id')
            ->leftJoin('practitioners','practitioners.id','=','appointments.practitioner_id')
            ->leftJoin('medical_specialties','medical_specialties.id','=','appointments.medical_speciality_id');

        // Filtrar por período según la vista
        if ($this->currentView === 'monthly') {
            $query->whereMonth('start', $this->currentDate->month)
                ->whereYear('start', $this->currentDate->year);
        } elseif ($this->currentView === 'weekly') {
            $startOfWeek = $this->currentDate->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = $this->currentDate->copy()->endOfWeek(Carbon::SUNDAY);
            $query->whereBetween('start', [$startOfWeek, $endOfWeek]);
        } elseif ($this->currentView === 'daily') {
            $query->whereDate('start', $this->currentDate->format('Y-m-d'));
        }

        // Aplicar filtros
        if ($this->selectedDoctor) {
            $query->where('practitioner_id', $this->selectedDoctor);
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->orWhere('service_type', 'like', '%' . $this->searchTerm . '%');
                $q->orWhere('start', 'like', '%' . $this->searchTerm . '%');
                $q->orWhere('end', 'like', '%' . $this->searchTerm . '%');
                $q->orWhere('status', 'like', '%' . $this->searchTerm . '%');
                $q->orWhereRaw("patients.name like '%" . $this->searchTerm . "%'");
                $q->orWhereRaw("practitioners.name like '%" . $this->searchTerm . "%'");
            });
        }
        $query->whereNotIn('status',['pending','proposed','whaitlist','noshow','cancelled']);
        $this->appointments = $query->orderBy('start')->get()->toArray();
    }

    public function loadStats()
    {
        $startOfMonth = $this->currentDate->copy()->startOfMonth();
        $endOfMonth = $this->currentDate->copy()->endOfMonth();

        $this->stats = [
            'total' => Appointment::whereBetween('start', [$startOfMonth, $endOfMonth])->count(),
            'booked' => Appointment::whereBetween('start', [$startOfMonth, $endOfMonth])->where('status', 'booked')->count(),
            'confirmed' => Appointment::whereBetween('start', [$startOfMonth, $endOfMonth])->where('status', 'confirmed')->count(),
            'fullfilled' => Appointment::whereBetween('start', [$startOfMonth, $endOfMonth])->where('status', 'fullfilled')->count(),
            'cancelled' => Appointment::whereBetween('start', [$startOfMonth, $endOfMonth])->where('status', 'cancelled')->count(),
            'today' => Appointment::whereDate('start', Carbon::today())->count()
        ];
    }

    // ===========================================
    // MÉTODOS DEL MODAL Y FORMULARIO
    // ===========================================

    public function openModal($date = null, $time = null)
    {
        $this->resetForm();
        $this->showModal = true;
        $this->modalTitle = 'Nueva Cita';

        if ($date) {
            $this->appointment_date = $date;
        }
        if ($time) {
            $this->appointment_time = $time;
        }
    }

    public function editAppointment($appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if ($appointment) {
            $this->editingAppointment = $appointmentId;
            $this->modalTitle = 'Editar Cita';
            $this->patient_id = $appointment->patient_id;
            $this->doctor_id = $appointment->practitioner_id;
            $this->appointment_date = $appointment->start->format('Y-m-d');
            $this->appointment_time = $appointment->start->format('H:i');
            $this->duration = $appointment->minutes_duration;
            $this->status = $appointment->status;
            $this->medical_speciality_id = $appointment->medical_speciality_id;
            $this->consulting_room_id = $appointment->consulting_room_id;
            $this->service_type = $appointment->service_type;
            $this->reason = $appointment->description;
            $this->description = $appointment->description;
            //$this->notes = $appointment->notes;
            $practitioner = Practitioner::find($appointment->practitioner_id);

            $clientId=null;
            $userClient = UserClient::whereUserId($practitioner->user_id)->first();
            if($userClient) $clientId= $userClient->client_id;

            $this->consultorios =   ConsultingRoom::whereHas('branch',function ($q) use($clientId){
                $q->whereClientId($clientId);
            })->pluck('name','id')->toArray();

            $this->especialidades = \App\Models\MedicalSpeciality::whereIn('id',$practitioner->qualifications->pluck('medical_speciality_id'))->pluck('name','id')->toArray();

            $this->showModal = true;
        }else{

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
        $this->editingAppointment = null;
        $this->patient_id = '';
        $this->doctor_id = '';
        $this->description = '';
        $this->appointment_date = Carbon::now()->format('Y-m-d');
        $this->appointment_time = '';
        $this->duration = 30;
        $this->status = 'booked';
        $this->consulting_room_id = '';
        $this->medical_speciality_id = '';
        $this->service_type='';
    }

    public function saveAppointment()
    {
        $this->validate();

        try {
            // Obtener información del doctor
            $doctor = Practitioner::find($this->doctor_id);

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

                session()->flash('error', 'El doctor no está disponible en ese horario.');
                return;
            }

            if ($this->editingAppointment) {
                // Actualizar cita existente
                $appointment = Appointment::find($this->editingAppointment);
                $appointment->update($appointmentData);
                session()->flash('message', 'Cita actualizada exitosamente.');
            } else {
                // Crear nueva cita
                Appointment::create($appointmentData);
                session()->flash('message', 'Cita creada exitosamente.');
            }

            $this->closeModal();
            $this->loadAppointments();
            $this->loadStats();

        } catch (\Exception $e) {
            dd($e->getMessage());
            session()->flash('error', 'Error al guardar la cita: ' . $e->getMessage());
        }
    }

    public function deleteAppointment($appointmentId)
    {
        try {
            $appointment = Appointment::find($appointmentId);
            if ($appointment) {
                $appointment->delete();
                session()->flash('message', 'Cita eliminada exitosamente.');
                $this->loadAppointments();
                $this->loadStats();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la cita.');
        }
    }

    public function updateStatus($appointmentId, $newStatus)
    {

        try {
            $appointment = Appointment::find($appointmentId);
            if ($appointment) {

                $appointment->update(['status' => $newStatus]);
                session()->flash('message', 'Estado actualizado exitosamente.');
                $this->loadAppointments();
                $this->loadStats();

                if($newStatus=='checked-in'){
                    $this->dispatch('showToastr'.$appointmentId,
                        type: 'success',
                        message: '¡Espere por favor en unos segundos empezara su consulta!'
                    );
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el estado.');
        }
    }

    // ===========================================
    // MÉTODOS DE FILTROS
    // ===========================================

    public function updatedSelectedDoctor()
    {
        $this->loadAppointments();
    }

    public function updatedSelectedStatus()
    {
        $this->loadAppointments();
    }

    public function updatedSearchTerm()
    {
        $this->loadAppointments();
    }

    public function clearFilters()
    {
        $this->selectedDoctor = '';
        $this->selectedStatus = '';
        $this->searchTerm = '';
        $this->loadAppointments();
    }

    // ===========================================
    // MÉTODOS AUXILIARES
    // ===========================================

    private function checkAvailability()
    {
        $startTime = Carbon::parse($this->appointment_date.' '.$this->appointment_time);
        $endTime = $startTime->copy()->addMinutes($this->duration);

        $query = Appointment::where('practitioner_id', $this->doctor_id)
            ->where('start', $this->appointment_date)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($startTime, $endTime) {
                $q->where(function($q2) use ($startTime, $endTime) {
                    $q2->where('start', '<', $endTime)
                        ->where('end', '>', $startTime);
                });
            });

        if ($this->editingAppointment) {
            $query->where('id', '!=', $this->editingAppointment);
        }

        return $query->count() === 0;
    }

    private function getCurrentPeriod()
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        if ($this->currentView === 'monthly') {
            return $months[$this->currentDate->month] . ' ' . $this->currentDate->year;
        } elseif ($this->currentView === 'weekly') {
            $startOfWeek = $this->currentDate->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = $this->currentDate->copy()->endOfWeek(Carbon::SUNDAY);
            return $startOfWeek->day . ' - ' . $endOfWeek->day . ' ' . $months[$this->currentDate->month] . ' ' . $this->currentDate->year;
        } elseif ($this->currentView === 'daily') {
            $days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            return $days[$this->currentDate->dayOfWeek] . ', ' . $this->currentDate->day . ' ' . $months[$this->currentDate->month] . ' ' . $this->currentDate->year;
        }
    }

    private function getCalendarData()
    {
        if ($this->currentView === 'monthly') {
            return $this->getMonthlyData();
        } elseif ($this->currentView === 'weekly') {
            return $this->getWeeklyData();
        } elseif ($this->currentView === 'daily') {
            return $this->getDailyData();
        }
    }

    private function getMonthlyData()
    {
        $startOfMonth = $this->currentDate->copy()->startOfMonth();
        $endOfMonth = $this->currentDate->copy()->endOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $weeks = [];
        $current = $startOfCalendar->copy();

        while ($current->lte($endOfCalendar)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dayAppointments = array_filter($this->appointments, function($appointment) use ($current) {
                    return Carbon::parse($appointment['start'])->isSameDay($current);
                });

                $week[] = [
                    'date' => $current->copy(),
                    'isCurrentMonth' => $current->month === $this->currentDate->month,
                    'isToday' => $current->isToday(),
                    'appointments' => array_values($dayAppointments)
                ];
                $current->addDay();
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    private function getWeeklyData()
    {
        $startOfWeek = $this->currentDate->copy()->startOfWeek(Carbon::MONDAY);
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $dayAppointments = array_filter($this->appointments, function($appointment) use ($day) {
                return Carbon::parse($appointment['start'])->isSameDay($day);
            });

            $days[] = [
                'date' => $day,
                'isToday' => $day->isToday(),
                'appointments' => array_values($dayAppointments),
                'timeSlots' => $this->generateTimeSlots()
            ];
        }

        return $days;
    }

    private function getDailyData()
    {
        $dayAppointments = array_filter($this->appointments, function($appointment) {
            return Carbon::parse($appointment['start'])->isSameDay($this->currentDate);
        });

        // Ordenar citas por hora
        $sortedAppointments = collect($dayAppointments)->sortBy('formatted_time')->values()->all();

        // Encontrar la cita más cercana
        $now = Carbon::now();
        $nextAppointment = null;
        $nextAppointmentIndex = -1;

        foreach ($sortedAppointments as $index => $appointment) {
            $appointmentDateTime = Carbon::parse($appointment['formatted_time']);

            // Si es hoy y la cita es futura, o si la cita está en progreso
            if ($appointmentDateTime->isToday() &&
                ($appointmentDateTime->isFuture() || $appointment['status'] === 'checked-in')) {
                $nextAppointment = $appointment;
                $nextAppointmentIndex = $index;
                break;
            }
        }

        // Generar timeline con indicador de tiempo actual
        $currentTimePosition = $this->calculateCurrentTimePosition();

        return [
            'date' => $this->currentDate,
            'appointments' => $sortedAppointments,
            'nextAppointment' => $nextAppointment,
            'nextAppointmentIndex' => $nextAppointmentIndex,
            'currentTimePosition' => $currentTimePosition,
            'isToday' => $this->currentDate->isToday()
        ];
    }

    public function changeDoctor(){

        $practitioner= Practitioner::find($this->doctor_id);
        if($practitioner){
            $clientId=null;
            $userClient = UserClient::whereUserId($practitioner->user_id)->first();
            if($userClient) $clientId= $userClient->client_id;

            $this->consultorios =   ConsultingRoom::whereHas('branch',function ($q) use($clientId){
                $q->whereClientId($clientId);
            })->pluck('name','id')->toArray();

            $this->especialidades = \App\Models\MedicalSpeciality::whereIn('id',$practitioner->qualifications->pluck('medical_speciality_id'))->pluck('name','id')->toArray();
        }else{
            $this->consultorios=[];
            $this->especialidades=[];
        }

    }

    // ===========================================
    // EXPORTACIÓN Y SINCRONIZACIÓN
    // ===========================================

    public function exportFHIR()
    {
        $appointments = Appointment::all();
        $fhirData = $appointments->map(function($appointment) {
            return $appointment->toFHIR();
        });

        $filename = 'appointments_fhir_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->streamDownload(function() use ($fhirData) {
            echo json_encode($fhirData, JSON_PRETTY_PRINT);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function syncWithServer()
    {
        try {
            // Aquí iría la lógica de sincronización con servidor FHIR
            session()->flash('message', 'Sincronización completada exitosamente.');
            $this->loadAppointments();
        } catch (\Exception $e) {
            session()->flash('error', 'Error en la sincronización: ' . $e->getMessage());
        }
    }

    // ===========================================
    // NUEVOS MÉTODOS PARA CONFIGURACIÓN
    // ===========================================

    public function toggleTimeBlockConfig()
    {
        $this->showTimeBlockConfig = !$this->showTimeBlockConfig;
    }

    public function updateTimeBlockConfig()
    {
        // Validar los valores
        $this->validate([
            'timeBlockMinutes' => 'required|in:15,30,60',
            'startHour' => 'required|integer|min:0|max:23',
            'endHour' => 'required|integer|min:1|max:24|gt:startHour'
        ]);

        // Guardar en session
        session([
            'calendar.time_block_minutes' => $this->timeBlockMinutes,
            'calendar.start_hour' => $this->startHour,
            'calendar.end_hour' => $this->endHour
        ]);

        // Recargar datos
        $this->loadAppointments();
        session()->flash('message', 'Configuración actualizada exitosamente.');
        $this->showTimeBlockConfig = false;
    }

    private function generateTimeSlots()
    {
        $slots = [];
        $blocksPerHour = 60 / $this->timeBlockMinutes;

        for ($hour = $this->startHour; $hour < $this->endHour; $hour++) {
            for ($block = 0; $block < $blocksPerHour; $block++) {
                $minutes = $block * $this->timeBlockMinutes;
                $time = Carbon::createFromTime($hour, $minutes);

                $slots[] = [
                    'time' => $time->format('H:i'),
                    'display' => $time->format('H:i'),
                    'hour' => $hour,
                    'minutes' => $minutes,
                    'isHourStart' => $minutes === 0
                ];
            }
        }

        return $slots;
    }

    // ===========================================
    // MÉTODO AUXILIAR: getAppointmentsByTimeSlot()
    // ===========================================

    public function getAppointmentsByTimeSlot($appointments, $hour, $minutes)
    {
        return array_filter($appointments, function($appointment) use ($hour, $minutes) {
            $appointmentTime = Carbon::parse($appointment['start']);
            $appointmentHour = $appointmentTime->hour;
            $appointmentMinutes = $appointmentTime->minute;

            // Verificar si la cita cae en este bloque de tiempo
            $blockStart = $hour * 60 + $minutes;
            $blockEnd = $blockStart + $this->timeBlockMinutes;
            $appointmentStart = $appointmentHour * 60 + $appointmentMinutes;

            return $appointmentStart >= $blockStart && $appointmentStart < $blockEnd;
        });
    }

    // ===========================================
    // MÉTODO AUXILIAR: calculateAppointmentPosition()
    // ===========================================

    public function calculateAppointmentPosition($appointment, $blockHeight = 60)
    {
        $appointmentTime = Carbon::parse($appointment['start']);
        $appointmentMinutes = $appointmentTime->minute;

        // Calcular posición dentro del bloque
        $minutesIntoBlock = $appointmentMinutes % $this->timeBlockMinutes;
        $position = ($minutesIntoBlock / $this->timeBlockMinutes) * $blockHeight;

        // Calcular altura basada en duración
        $durationBlocks = ceil($appointment['minutes_duration'] / $this->timeBlockMinutes);
        $height = min($durationBlocks * $blockHeight, $blockHeight * 4); // Máximo 4 bloques

        return [
            'top' => $position,
            'height' => $height
        ];
    }

    // ===========================================
    // NUEVO MÉTODO: calculateCurrentTimePosition()
    // ===========================================

    private function calculateCurrentTimePosition()
    {
        if (!$this->currentDate->isToday()) {
            return null;
        }

        $now = Carbon::now();
        $dayStart = Carbon::parse($this->startHour . ':00');
        $dayEnd = Carbon::parse($this->endHour . ':00');

        if ($now->lt($dayStart) || $now->gt($dayEnd)) {
            return null;
        }

        $totalMinutes = $dayEnd->diffInMinutes($dayStart);
        $currentMinutes = $now->diffInMinutes($dayStart);

        return ($currentMinutes / $totalMinutes) * 100; // Porcentaje de posición
    }

    // ===========================================
    // NUEVO MÉTODO: getAppointmentStatus()
    // ===========================================

    public function getAppointmentStatus($appointment, $isNext = false)
    {
        $appointmentDateTime = Carbon::parse($appointment['formatted_date'] . ' ' . $appointment['formatted_time']);
        $now = Carbon::now();

        if ($appointment['status'] === 'fulfilled') {
            return 'fulfilled';
        } elseif ($appointment['status'] === 'cancelled') {
            return 'cancelled';
        } elseif ($appointment['status'] === 'checked-in') {
            return 'current';
        } elseif ($appointmentDateTime->isPast() && $appointment['status'] !== 'fulfilled') {
            return 'overdue';
        } elseif ($isNext) {
            return 'next';
        } elseif ($now->diffInMinutes($appointmentDateTime, false) <= 15 && $appointmentDateTime->isFuture()) {
            return 'upcoming';
        } else {
            return 'scheduled';
        }
    }
}
