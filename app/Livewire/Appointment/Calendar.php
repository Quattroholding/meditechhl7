<?php

namespace App\Livewire\Appointment;

use App\Models\Appointment;
use App\Models\ConsultingRoom;
use App\Models\MedicalSpeciality;
use App\Models\Practitioner;
use App\Models\User;
use App\Models\UserClient;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class Calendar extends Component
{
    // Propiedades del calendario
    public $currentView = 'daily';
    public $currentDate;
    public $selectedDate;

    // Modal y formulario
    public $showModal = false;
    public $appointment_date = '';
    public $appointment_time = '';
    public $modalTitle='Nueva Cita';

    // Filtros
    public $selectedDoctor = '';
    public $selectedStatus = '';
    public $searchTerm = '';

    // Datos
    public $appointments = [];
    public $doctors = [];
    public $stats = [];



    // NUEVAS PROPIEDADES PARA CONFIGURACIÓN DE TIEMPO
    public $timeBlockMinutes = 30; // Bloques de 30 minutos por defecto
    public $startHour = 6; // Hora de inicio del calendario
    public $endHour = 21; // Hora de fin del calendario
    public $showTimeBlockConfig = false; // Para mostrar/ocultar configuración

    public $timeBlockOptions = [
        15 => '15 minutos',
        30 => '30 minutos',
        60 => '60 minutos'
    ];

    // Agregar estas propiedades al componente MedicalCalendar
    public $autoUpdateEnabled = true;
    public $currentTimePosition = null;
    public $lastTimeUpdate = null;
    public $showDebug=true;

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->appointment_date = Carbon::now()->format('Y-m-d');

        $this->timeBlockMinutes = session('calendar.time_block_minutes', 30);
        $this->startHour = session('calendar.start_hour', 6);
        $this->endHour = session('calendar.end_hour', 21);
        if(auth()->user()->hasRole('paciente')){
            $this->patient_id = auth()->user()->patient->id;
            $this->currentView = 'monthly';
        }
        $this->loadEspecialidades();
        $this->loadDoctors();
        $this->loadAppointments();
        $this->loadStats();

        $this->updateCurrentTimePosition();

    }

    public function render()
    {

        if(auth()->user()->hasRole('paciente')){
            $this->patient_id = auth()->user()->patient->id;
            $this->currentView = 'monthly';
        }

        if(auth()->user()->hasRole('doctor')){
            $this->doctor_id = auth()->user()->practitioner->id;
            $practitioner = Practitioner::find($this->doctor_id);

            $clientId=null;
            $userClient = UserClient::whereUserId($practitioner->user_id)->first();
            if($userClient) $clientId= $userClient->client_id;

            $this->consultorios =   ConsultingRoom::whereHas('branch',function ($q) use($clientId){
                $q->whereClientId($clientId);
            })->pluck('name','id')->toArray();

            $this->especialidades = \App\Models\MedicalSpeciality::whereIn('id',$practitioner->qualifications->pluck('medical_speciality_id'))->pluck('name','id')->toArray();
        }

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
        $this->doctors = Practitioner::get()->pluck('name','id')->toArray();
    }

    public function loadEspecialidades()
    {
        $this->especialidades = MedicalSpeciality::pluck('name','id')->toArray();
    }

    #[On('loadAppointments')]
    public function loadAppointments()
    {
       $notStatuses =
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
        if($this->currentView=='daily')
            $query->whereNotIn('status',['pending','whaitlist','noshow','cancelled']);

        $this->appointments = $query->orderBy('start')->get()->toArray();
    }
    #[On('loadStats')]
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

    public function openModal($date = null, $time = null,$modalTitle='Nueva Cita')
    {
        $this->showModal = true;
        $this->modalTitle = $modalTitle;

        if ($date) {
            $this->appointment_date = $date;
        }
        if ($time) {
            $this->appointment_time = $time;
        }
    }

    public function editAppointment($appointmentId)
    {
        $this->modalTitle = 'Actualizar Cita';
        $this->dispatch('editAppointmentModal',$appointmentId);
    }


    public function deleteAppointment($appointmentId)
    {
        try {
            $appointment = Appointment::find($appointmentId);
            if ($appointment) {
                $appointment->delete();
                session()->flash('message.success', 'Cita eliminada exitosamente.');
                $this->loadAppointments();
                $this->loadStats();
            }
        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al eliminar la cita.');
        }
    }

    public function updateStatus($appointmentId, $newStatus)
    {

        try {
            $appointment = Appointment::find($appointmentId);
            $current_status = $appointment->status;
            if ($appointment) {

                $appointment->update(['status' => $newStatus]);
                session()->flash('message.success', 'Estado actualizado exitosamente.');
                $this->loadAppointments();
                $this->loadStats();

                if($current_status=='proposed' && $newStatus=='booked'){
                   $appointment->notifyPatientAboutConfirmation();
                }

                if($newStatus=='checked-in'){
                    $this->dispatch('showToastr'.$appointmentId,
                        type: 'success',
                        message: '¡Espere por favor en unos segundos empezara su consulta!'
                    );
                }
            }
        } catch (\Exception $e) {
            session()->flash('message.error', 'Error al actualizar el estado.');
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
        $sortedAppointments = collect($dayAppointments)->sortBy('start')->values()->all();

        // Encontrar la cita más cercana
        $now = Carbon::now();
        $nextAppointment = null;
        $nextAppointmentIndex = -1;

        foreach ($sortedAppointments as $index => $appointment) {
            $appointmentStart = Carbon::parse($appointment['start']);
            $appointmentEnd = Carbon::parse($appointment['end']);

            // Si es hoy y la cita es futura, o si la cita está en progreso
            if ($this->currentDate->isToday()) {
                // Si la cita está en progreso
                if ($now->between($appointmentStart, $appointmentEnd)) {
                    $nextAppointment = $appointment;
                    $nextAppointmentIndex = $index;
                    break;
                }
                // Si la cita es futura
                elseif ($appointmentStart->isFuture()) {
                    $nextAppointment = $appointment;
                    $nextAppointmentIndex = $index;
                    break;
                }
            }
        }

        // Agregar información de formato para cada cita
        $sortedAppointments = collect($sortedAppointments)->map(function($appointment) {
            $start = Carbon::parse($appointment['start']);
            $end = Carbon::parse($appointment['end']);

            $appointment['formatted_start_time'] = $appointment['formatted_time'];
            $appointment['formatted_time'] = $start->format('H:i') . ' - ' . $end->format('H:i');
            $appointment['formatted_date'] = $start->format('Y-m-d');

            return $appointment;
        })->toArray();

        // Calcular posición del indicador de tiempo
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
            session()->flash('message.success', 'Sincronización completada exitosamente.');
            $this->loadAppointments();
        } catch (\Exception $e) {
            session()->flash('message.error', 'Error en la sincronización: ' . $e->getMessage());
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
        session()->flash('message.success', 'Configuración actualizada exitosamente.');
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
        $appointmentTime = Carbon::parse($this->appointment_date.' '.$this->appointment_time);
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

    /**
     * Calcula la posición actual del tiempo basándose en las citas del día
     */
    private function calculateCurrentTimePosition()
    {
        if (!$this->currentDate->isToday()) {
            return null;
        }

        $now = Carbon::now();

        // Obtener las citas del día actual
        $todayAppointments = collect($this->appointments)->filter(function($appointment) {
            return Carbon::parse($appointment['start'])->isToday();
        })->sortBy('start');

        if ($todayAppointments->isEmpty()) {
            return null;
        }

        // Encontrar la primera y última cita del día
        $firstAppointment = $todayAppointments->first();
        $lastAppointment = $todayAppointments->last();

        $firstStart = Carbon::parse($firstAppointment['start']);
        $lastEnd = Carbon::parse($lastAppointment['end']);

        // Si el tiempo actual está antes de la primera cita
        if ($now->lt($firstStart)) {
            return 0;
        }

        // Si el tiempo actual está después de la última cita
        if ($now->gt($lastEnd)) {
            return 100;
        }

        // Calcular la posición relativa entre la primera y última cita
        $totalMinutes = $lastEnd->diffInMinutes($firstStart);
        $currentMinutes = $now->diffInMinutes($firstStart);

        return ($currentMinutes / $totalMinutes) * 100;
    }

    // ===========================================
    // NUEVO MÉTODO: getAppointmentStatus()
    // ===========================================

    public function getAppointmentStatus($appointment, $isNext = false)
    {

        //$appointmentDateTime = Carbon::parse($appointment['start']);
        //$now = Carbon::now();

        $start = Carbon::parse($appointment['start']);
        $end = Carbon::parse($appointment['end']);
        $now = Carbon::now();

        if ($appointment['status'] === 'fulfilled') {
            return 'fulfilled';
        }elseif ($appointment['status'] === 'pending') {
            return 'pending';
        } elseif ($appointment['status'] === 'cancelled') {
            return 'cancelled';
        } elseif ($appointment['status'] === 'checked-in') {
            return 'checked-in';
        } if ($now->between($start, $end)) {
            // La cita está en progreso ahora mismo
            return 'current';
        }elseif ($start->isPast() && $appointment['status'] !== 'fulfilled') {
            return 'overdue';
        }elseif ($now->diffInMinutes($start, false) <= 15 && $start->isFuture()) {
            return 'upcoming';
        } elseif ($isNext) {
            return 'next';
        } else {
            return 'scheduled';
        }
    }

    public function updateCurrentTimePosition()
    {
        if (!$this->currentDate->isToday()) {
            $this->currentTimePosition = null;
            return;
        }

        $position = $this->calculateCurrentTimePosition();
        $this->currentTimePosition = $position;
        $this->lastTimeUpdate = now()->format('H:i:s');

        // Obtener información adicional para debug
        $debugInfo = $this->getTimelineDebugInfo();

        // Emitir evento con la nueva posición
        $this->dispatch('timePositionUpdated', [
            'position' => $this->currentTimePosition,
            'currentTime' => now()->format('H:i'),
            'timestamp' => now()->timestamp,
            'debug' => $debugInfo
        ]);
    }

    // Método mejorado para refrescar automáticamente
    public function refreshTimePosition()
    {
        // Solo actualizar si estamos en daily view y es hoy
        if ($this->currentView === 'daily' && $this->currentDate->isToday()) {
            $this->updateCurrentTimePosition();
        }
    }

    private function getTimelineDebugInfo()
    {
        $now = Carbon::now();
        $todayAppointments = collect($this->appointments)->filter(function($appointment) {
            return Carbon::parse($appointment['start'])->isToday();
        })->sortBy('start');

        if ($todayAppointments->isEmpty()) {
            return [
                'message' => 'No hay citas hoy',
                'currentTime' => $now->format('H:i:s')
            ];
        }

        $firstAppointment = $todayAppointments->first();
        $lastAppointment = $todayAppointments->last();

        return [
            'now' => $now->format('H:i:s'),
            'firstAppointmentStart' => Carbon::parse($firstAppointment['start'])->format('H:i:s'),
            'lastAppointmentEnd' => Carbon::parse($lastAppointment['end'])->format('H:i:s'),
            'totalAppointments' => $todayAppointments->count(),
            'positionPercent' => $this->currentTimePosition,
            'appointments' => $todayAppointments->map(function($apt) {
                return [
                    'start' => Carbon::parse($apt['start'])->format('H:i'),
                    'end' => Carbon::parse($apt['end'])->format('H:i'),
                    'status' => $apt['status']
                ];
            })->values()->toArray()
        ];
    }


// Método para toggle auto-update
    public function toggleAutoUpdate()
    {
        $this->autoUpdateEnabled = !$this->autoUpdateEnabled;

        if ($this->autoUpdateEnabled) {
            $this->updateCurrentTimePosition();
            $this->dispatch('startAutoUpdate');
        } else {
            $this->dispatch('stopAutoUpdate');
        }
    }

}
