<?php

namespace App\Livewire\Doctor;

use App\Models\Practitioner;
use App\Services\FileService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class Head extends Component
{
    use WithFileUploads;

    public $practitioner_id;
    public $data;
    #[Validate('image|max:1024')] // 1MB Max
    public $avatar;
    public function render()
    {
        $this->data = Practitioner::find($this->practitioner_id);
        return view('livewire.doctor.head');
    }

    public function updatedAvatar()
    {
        $service = new FileService();
        $data['record_id'] =$this->practitioner_id;
        $data['folder'] = 'practitioners';
        $data['type']='avatar';
        $service->guardarArchivos([$this->avatar],$data);

        $this->data->user->profile_picture = $this->data->avatar()->path;
        $this->data->user->save();

    }
}
