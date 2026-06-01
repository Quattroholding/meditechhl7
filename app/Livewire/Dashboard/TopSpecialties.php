<?php

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TopSpecialties extends Component
{
    public $data = [];

    public $top_specialties;

    public function mount()
    {
        // Cache por 24 horas - las especialidades raramente cambian
        $this->top_specialties = Cache::tags(['dashboard', 'specialties'])->remember('top_specialties', 86400, function () {
            $total = DB::table('practitioner_qualifications')->count();

            if ($total === 0) {
                return collect([]);
            }

            return DB::table('practitioner_qualifications')
                ->select('medical_speciality_id', DB::raw('COUNT(*) as total'), DB::raw("ROUND(COUNT(*) / $total * 100, 2) as percentage"))
                ->groupBy('medical_speciality_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        });

    }

    public function render()
    {
        // $encounter

        return view('livewire.dashboard.top-specialties');
    }
}
