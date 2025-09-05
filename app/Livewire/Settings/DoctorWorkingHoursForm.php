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
    public $consulting_room_id;
    public $branch_id;
    public $branches=[];
    public $rooms=[];

    protected $rules = [
        'branch_id' => 'required|exists:branches,id',
        'consulting_room_id' => 'required|exists:consulting_rooms,id',
    ];

    protected $messages = [
        // 'patient_id.required' => 'Debe seleccionar un paciente.',
        'branch_id.required' => 'Debe seleccionar una sucursal.',
        'consulting_room_id.required' => 'Debe seleccionar un consultorio.',
    ];

    public function mount()
    {
        if (auth()->user()->clients()->first()) {
            $this->clientId = auth()->user()->clients()->first()->id;
            $this->client = auth()->user()->clients()->first();

            $this->loadBranches();
            $this->loadRooms();

        }
        $days = [__('lunes'), __('martes'), __('miercoles'), __('jueves'), __('viernes'), __('sabado'), __('domingo')];
        foreach ($days as $day) {
            $this->workingHours[$day] = [
                'enabled' => false,
                // 'start' => Carbon::parse('7:00')->format('h:i A'),
                // 'end' =>  Carbon::parse('18:00')->format('h:i A'),
                'start' => '08:00',
                'end' => '18:00',
            ];
        }

        // Opcional: cargar los horarios actuales del médico
        $existing = UserWorkingHour::where('user_id', auth()->id())->get();
        foreach ($existing as $item) {
            $this->workingHours[$item->day_of_week] = [
                'enabled' => true,
                'start' => substr($item->start_time, 0, 5),
                'end' => substr($item->end_time, 0, 5),
            ];
        }

    }

    public function loadBranches(){
        $this->branches =Branch::pluck('name','id')->toArray();
    }

    public function loadRooms(){
        $this->rooms = ConsultingRoom::pluck('name','id')->toArray();
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
                    'branch_id' => $this->branch_id,
                    'consulting_room_id' => $this->consulting_room_id,
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

    public function setStartDayTime($day)
    {
        $this->workingHours[$day]['start'] = $this->workingHours[$day]['start'];
    }

    public function filterRooms()
    {
        if($this->branch_id){
            $this->rooms = ConsultingRoom::whereBranchId($this->branch_id)->pluck('name','id')->toArray();
        }else{
            $this->loadRooms();
        }

    }

    public function render()
    {
        return view('livewire.settings.doctor-working-hours-form');
    }
}
