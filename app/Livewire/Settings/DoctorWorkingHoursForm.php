<?php

namespace App\Livewire\Settings;

use App\Models\Branch;
use App\Models\ConsultingRoom;
use App\Models\UserWorkingHour;
use Carbon\Carbon;
use Livewire\Component;

class DoctorWorkingHoursForm extends Component
{
    public $workingHours = [];

    public $star_times = [];

    public $end_times = [];

    public $clientId;

    public $client;

    public $branches = [];

    public $roomsByBranch = [];

    public $days = [];

    protected function rules()
    {
        $rules = [];
        foreach ($this->workingHours as $day => $schedules) {
            foreach ($schedules as $index => $config) {
                if ($config['enabled']) {
                    $rules["workingHours.{$day}.{$index}.branch_id"] = 'required|exists:branches,id';
                    $rules["workingHours.{$day}.{$index}.consulting_room_id"] = 'required|exists:consulting_rooms,id';
                    $rules["workingHours.{$day}.{$index}.start"] = 'required';
                    $rules["workingHours.{$day}.{$index}.end"] = 'required|after:workingHours.'.$day.'.'.$index.'.start';
                }
            }
        }

        return $rules;
    }

    protected $messages = [
        'workingHours.*.*.branch_id.required' => 'Debe seleccionar una sucursal.',
        'workingHours.*.*.consulting_room_id.required' => 'Debe seleccionar un consultorio.',
        'workingHours.*.*.start.required' => 'Debe especificar la hora de entrada.',
        'workingHours.*.*.end.required' => 'Debe especificar la hora de salida.',
        'workingHours.*.*.end.after' => 'La hora de salida debe ser posterior a la hora de entrada.',
    ];

    public function mount()
    {
        if (auth()->user()->clients()->first()) {
            $this->clientId = auth()->user()->clients()->first()->id;
            $this->client = auth()->user()->clients()->first();

            $this->loadBranches();
            $this->loadAllRooms();
        }

        $this->days = [__('lunes'), __('martes'), __('miercoles'), __('jueves'), __('viernes'), __('sabado'), __('domingo')];

        // Inicializar cada día con un array vacío de horarios
        foreach ($this->days as $day) {
            $this->workingHours[$day] = [];
        }

        // Cargar los horarios actuales del médico con sus sucursales/consultorios
        $existing = UserWorkingHour::where('user_id', auth()->id())
            ->with(['branch', 'consultingRoom'])
            ->orderBy('start_time')
            ->get();

        foreach ($existing as $item) {
            $this->workingHours[$item->day_of_week][] = [
                'enabled' => true,
                'start' => substr($item->start_time, 0, 5),
                'end' => substr($item->end_time, 0, 5),
                'branch_id' => $item->branch_id,
                'consulting_room_id' => $item->consulting_room_id,
            ];
        }

        // Si un día no tiene horarios, agregar uno vacío deshabilitado
        foreach ($this->days as $day) {
            if (empty($this->workingHours[$day])) {
                $this->workingHours[$day][] = $this->getEmptySchedule();
            }
        }
    }

    protected function getEmptySchedule()
    {
        return [
            'enabled' => false,
            'start' => '08:00',
            'end' => '18:00',
            'branch_id' => null,
            'consulting_room_id' => null,
        ];
    }

    public function loadBranches()
    {
        $this->branches = Branch::pluck('name', 'id')->toArray();
    }

    public function loadAllRooms()
    {
        // Cargar consultorios agrupados por sucursal
        $rooms = ConsultingRoom::with('branch')->get();

        foreach ($rooms as $room) {
            $this->roomsByBranch[$room->branch_id][$room->id] = $room->name;
        }
    }

    public function updatedWorkingHours($value, $key)
    {
        // Cuando cambia la sucursal de un horario, resetear el consultorio
        if (str_ends_with($key, '.branch_id')) {
            $parts = explode('.', $key);
            $day = $parts[0];
            $index = $parts[1];
            $this->workingHours[$day][$index]['consulting_room_id'] = null;
        }
    }

    public function getRoomsForSchedule($day, $index)
    {
        $branchId = $this->workingHours[$day][$index]['branch_id'] ?? null;

        if (! $branchId || ! isset($this->roomsByBranch[$branchId])) {
            return [];
        }

        return $this->roomsByBranch[$branchId];
    }

    public function addSchedule($day)
    {
        $this->workingHours[$day][] = [
            'enabled' => true,
            'start' => '08:00',
            'end' => '18:00',
            'branch_id' => null,
            'consulting_room_id' => null,
        ];
    }

    public function removeSchedule($day, $index)
    {
        unset($this->workingHours[$day][$index]);
        $this->workingHours[$day] = array_values($this->workingHours[$day]);

        // Si no quedan horarios, agregar uno vacío
        if (empty($this->workingHours[$day])) {
            $this->workingHours[$day][] = $this->getEmptySchedule();
        }
    }

    public function toggleDay($day, $index)
    {
        $this->workingHours[$day][$index]['enabled'] = ! $this->workingHours[$day][$index]['enabled'];
    }

    public function save()
    {
        $this->validate();

        UserWorkingHour::where('user_id', auth()->id())->delete();

        foreach ($this->workingHours as $day => $schedules) {
            foreach ($schedules as $config) {
                if ($config['enabled']) {
                    UserWorkingHour::create([
                        'user_id' => auth()->id(),
                        'client_id' => $this->clientId,
                        'branch_id' => $config['branch_id'],
                        'consulting_room_id' => $config['consulting_room_id'],
                        'day_of_week' => $day,
                        'start_time' => Carbon::parse($config['start'])->format('H:i'),
                        'end_time' => Carbon::parse($config['end'])->format('H:i'),
                    ]);
                }
            }
        }

        // Dispatch event to refresh setup reminders
        $this->dispatch('refreshSetupReminders');

        session()->flash('message.success', 'Horario actualizado con éxito.');
    }

    public function changeEnabled($day, $index = 0)
    {
        $this->workingHours[$day][$index]['enabled'] = $this->workingHours[$day][$index]['enabled'];
    }

    public function hasEnabledSchedule($day)
    {
        return collect($this->workingHours[$day] ?? [])->contains('enabled', true);
    }

    public function render()
    {
        return view('livewire.settings.doctor-working-hours-form');
    }
}
