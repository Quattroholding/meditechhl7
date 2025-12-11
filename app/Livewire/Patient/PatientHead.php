<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use App\Services\FileService;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class PatientHead extends Component
{
    use WithFileUploads;

    public $data;

    public $showModal;

    public $typeNote='medical';

    #[Validate('image|max:1024')] // 1MB Max
    public $avatar;

    public function render()
    {
        return view('livewire.patient.patient-head');
    }

    public function mount($patient_id)
    {
        $this->data = Patient::find($patient_id);

        if(Route::currentRouteName()<>'consultation.show')
            $this->typeNote='private';
    }

    public function updatedAvatar()
    {
        $service = new FileService;
        $data['record_id'] = $this->data->id;
        $data['folder'] = 'patients';
        $data['type'] = 'avatar';
        $service->guardarArchivos([$this->avatar], $data);

        $this->data->user->profile_picture = $this->data->avatar()->path;
        $this->data->user->save();
    }

    public function openModalNote($patientId,$type='private')
    {
        $practitioner_id = null;
        if (auth()->user()->hasRole('doctor')) {
            $practitioner_id = auth()->user()->practitioner->id;
        }

        $this->dispatch('openModal', $patientId, $practitioner_id,$type);
    }
}
