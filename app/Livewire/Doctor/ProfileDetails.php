<?php

namespace App\Livewire\Doctor;

use App\Models\Practitioner;
use Livewire\Component;

class ProfileDetails extends Component
{
    public $practitioner_id;

    public $data;

    public function loadData()
    {
        $this->data = Practitioner::find($this->practitioner_id);
    }

    public function render()
    {
        return view('livewire.doctor.profile-details');
    }
}
