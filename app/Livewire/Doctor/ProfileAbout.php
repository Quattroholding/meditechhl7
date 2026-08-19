<?php

namespace App\Livewire\Doctor;

use App\Models\Practitioner;
use App\Models\PractitionerQualification;
use Livewire\Attributes\On;
use Livewire\Component;

class ProfileAbout extends Component
{
    public $practitioner_id;

    public $data;

    public $qualifications;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->data = Practitioner::find($this->practitioner_id);
        $this->loadQualifications();
    }

    public function render()
    {
        return view('livewire.doctor.profile-about');
    }

    #[On('loadQualifications')]
    public function loadQualifications()
    {
        $this->qualifications = $this->data->qualifications()->get();
    }

    public function setDefaultSpecialty($id)
    {
        // Quitar el default de todas las especialidades del doctor
        PractitionerQualification::where('practitioner_id', $this->practitioner_id)->update(['default' => 0]);
        // Marcar la especialidad seleccionada como default
        PractitionerQualification::where('id', $id)->update(['default' => 1]);

        $this->loadQualifications();

        session()->flash('message.success', 'Especialidad predeterminada actualizada correctamente.');
    }

    public function deleteQualification($id)
    {
        PractitionerQualification::find($id)->delete();
        $this->loadQualifications();

        session()->flash('message.success', 'Especialidad eliminada correctamente.');
    }
}
