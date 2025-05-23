<?php

namespace App\Livewire\Doctor;

use App\Models\Practitioner;
use Livewire\Component;

class ProfileDetails extends Component
{
    public $practitioner_id;
    public $data;
    public function render()
    {
        $this->data = Practitioner::find($this->practitioner_id);
        return view('livewire.doctor.profile-details');
    }
}
