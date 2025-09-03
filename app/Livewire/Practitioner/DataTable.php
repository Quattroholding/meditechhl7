<?php

namespace App\Livewire\Practitioner;

use App\Actions\CreatePractitionerUserAction;
use App\Models\Practitioner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search;

    public $sortDirection = 'desc';

    public $sortField = 'id';

    public $pagination = 10;

    public $route_name = 'practitioner';

    public $showModal = false;

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function createUser($practitionerId)
    {
        if (! auth()->user()->hasRole('admin')) {
            session()->flash('error', 'No tienes permisos para realizar esta acción.');

            return;
        }

        try {
            $practitioner = Practitioner::findOrFail($practitionerId);

            $action = new CreatePractitionerUserAction;
            $user = $action->execute($practitioner);

            session()->flash('message', 'Usuario creado exitosamente. Contraseña temporal: '.$user->temporary_password);
            session()->flash('temporary_password', $user->temporary_password);

        } catch (ValidationException $e) {
            session()->flash('error', collect($e->errors())->flatten()->first());
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el usuario: '.$e->getMessage());
        }
    }

    public function render()
    {
        $data = Practitioner::selectRaw('practitioners.*')
            ->leftJoin('practitioner_qualifications', 'practitioners.id', '=', 'practitioner_qualifications.practitioner_id')
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) { // Asegura que las condiciones sean correctas
                    $q->orWhere('birth_date', 'like', '%'.$this->search.'%');
                    $q->orWhere('identifier', 'like', '%'.$this->search.'%');
                    $q->orWhere('email', 'like', '%'.$this->search.'%');
                    $q->orWhere('name', 'like', '%'.$this->search.'%');
                    $q->orWhere('display', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        return view('livewire.practitioner.data-table', ['data' => $data]);
    }
}
