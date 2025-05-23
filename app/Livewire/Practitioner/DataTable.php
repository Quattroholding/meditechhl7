<?php

namespace App\Livewire\Practitioner;

use App\Models\Patient;
use App\Models\Practitioner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search;
    public $sortDirection='asc';
    public $sortField='name';
    public $pagination=10;
    public $route_name='practitioner';
    public $showModal=false;

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    public function render()
    {
        $data = Practitioner::selectRaw('practitioners.*')
            ->leftJoin('practitioner_qualifications','practitioners.id','=','practitioner_qualifications.practitioner_id')
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) { // Asegura que las condiciones sean correctas
                    $q->orWhere('birth_date', 'like', '%' . $this->search . '%');
                    $q->orWhere('identifier', 'like', '%' . $this->search . '%');
                    $q->orWhere('email', 'like', '%' . $this->search . '%');
                    $q->orWhere('name', 'like', '%' . $this->search . '%');
                    $q->orWhere('display', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.practitioner.data-table', [ 'data' => $data]);
    }

}
