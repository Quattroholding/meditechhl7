<?php

namespace App\Livewire;

use Livewire\Component;

class DiagnosticsBySpecialties extends Component
{

     public $top_specialties;

    public function mount()
    {
        $total = DB::table('practitioner_qualifications')->count();
        $this->top_specialties = DB::table('practitioner_qualifications')
            ->select('medical_speciality_id', DB::raw('COUNT(*) as total'), DB::raw("ROUND(COUNT(*) / $total * 100, 2) as percentage"))
            ->groupBy('medical_speciality_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

    }

    public function render()
    {
        return view('livewire.diagnostics-by-specialties');
    }
}
