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

    protected function rules()
    {
        $rules = [];
        foreach ($this->workingHours as $day => $config) {
            if ($config['enabled']) {
                $rules["workingHours.{$day}.branch_id"] = 'required|exists:branches,id';
                $rules["workingHours.{$day}.consulting_room_id"] = 'required|exists:consulting_rooms,id';
                $rules["workingHours.{$day}.start"] = 'required';
                $rules["workingHours.{$day}.end"] = 'required|after:workingHours.'.$day.'.start';
            }
        }

        return $rules;
    }

    protected $messages = [
        'workingHours.*.branch_id.required' => 'Debe seleccionar una sucursal.',
        'workingHours.*.consulting_room_id.required' => 'Debe seleccionar un consultorio.',
        'workingHours.*.start.required' => 'Debe especificar la hora de entrada.',
        'workingHours.*.end.required' => 'Debe especificar la hora de salida.',
        'workingHours.*.end.after' => 'La hora de salida debe ser posterior a la hora de entrada.',
    ];

    public function mount()
    {
        if (auth()->user()->clients()->first()) {
            $this->clientId = auth()->user()->clients()->first()->id;
            $this->client = auth()->user()->clients()->first();

            $this->loadBranches();
            $this->loadAllRooms();
        }

        $days = [__('lunes'), __('martes'), __('miercoles'), __('jueves'), __('viernes'), __('sabado'), __('domingo')];
        foreach ($days as $day) {
            $this->workingHours[$day] = [
                'enabled' => false,
                'start' => '08:00',
                'end' => '18:00',
                'branch_id' => null,
                'consulting_room_id' => null,
            ];
        }

        // Cargar los horarios actuales del médico con sus sucursales/consultorios
        $existing = UserWorkingHour::where('user_id', auth()->id())
            ->with(['branch', 'consultingRoom'])
            ->get();

        foreach ($existing as $item) {
            $this->workingHours[$item->day_of_week] = [
                'enabled' => true,
                'start' => substr($item->start_time, 0, 5),
                'end' => substr($item->end_time, 0, 5),
                'branch_id' => $item->branch_id,
                'consulting_room_id' => $item->consulting_room_id,
            ];
        }
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
        // Cuando cambia la sucursal de un día, resetear el consultorio
        if (str_ends_with($key, '.branch_id')) {
            $day = explode('.', $key)[0];
            $this->workingHours[$day]['consulting_room_id'] = null;
        }
    }

    public function getRoomsForDay($day)
    {
        $branchId = $this->workingHours[$day]['branch_id'] ?? null;

        if (! $branchId || ! isset($this->roomsByBranch[$branchId])) {
            return [];
        }

        return $this->roomsByBranch[$branchId];
    }

    public function save()
    {
        $this->validate();

        UserWorkingHour::where('user_id', auth()->id())->delete();

        foreach ($this->workingHours as $day => $config) {
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

        session()->flash('message.success', 'Horario actualizado con éxito.');
    }

    public function changeEnabled($day)
    {
        $this->workingHours[$day]['enabled'] = $this->workingHours[$day]['enabled'];
    }

    public function render()
    {
        return view('livewire.settings.doctor-working-hours-form');
    }
}
