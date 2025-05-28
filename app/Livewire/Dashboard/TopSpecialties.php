<?php

namespace App\Livewire\Dashboard;

use App\Models\PractitionerQualification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopSpecialties extends Component
{
    public $data=[];
    public $top_specialties;
    public function mount(){
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
        //$encounter

        return view('livewire.dashboard.top-specialties');
    }
}
