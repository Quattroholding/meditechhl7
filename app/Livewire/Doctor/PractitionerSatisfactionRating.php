<?php

namespace App\Livewire\Doctor;

use App\Models\Practitioner;
use Livewire\Component;

class PractitionerSatisfactionRating extends Component
{
    public Practitioner $practitioner;

    public function mount(int $practitioner_id): void
    {
        $this->practitioner = Practitioner::findOrFail($practitioner_id);
    }

    public function render()
    {
        $rating = $this->practitioner->getSatisfactionRating(1); // survey_id = 1

        return view('livewire.doctor.practitioner-satisfaction-rating', compact('rating'));
    }
}
