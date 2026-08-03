<?php

namespace App\Livewire\Doctor;

use App\Models\Practitioner;
use App\Services\FileService;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Head extends Component
{
    use WithFileUploads;

    public $practitioner_id;

    public $data;

    #[Validate('image|max:1024')] // 1MB Max
    public $avatar;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->data = Practitioner::find($this->practitioner_id);
    }

    public function render()
    {
        return view('livewire.doctor.head');
    }

    public function updatedAvatar()
    {
        $service = new FileService;
        $data['record_id'] = $this->practitioner_id;
        $data['folder'] = 'practitioners';
        $data['type'] = 'avatar';
        $service->guardarArchivos([$this->avatar], $data);

        $this->data->user->profile_picture = $this->data->avatar()->path;
        $this->data->user->save();

    }
}
