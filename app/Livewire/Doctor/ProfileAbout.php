<?php

namespace App\Livewire\Doctor;

use App\Models\Practitioner;
use Livewire\Attributes\On;
use Livewire\Component;

class ProfileAbout extends Component
{
    public $practitioner_id;
    public $data;
    public $qualifications;
    public function render()
    {
        $this->data = Practitioner::find($this->practitioner_id);
        $this->loadQualifications();

        return view('livewire.doctor.profile-about');
    }
    #[On('loadQualifications')]
    public function loadQualifications(){
        $this->qualifications = $this->data->qualifications()->get();
    }
}
