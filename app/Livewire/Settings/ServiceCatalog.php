<?php

namespace App\Livewire\Settings;

use App\Models\CptCode;
use App\Models\ServiceCatalog as ServiceCatalogModel;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceCatalog extends Component
{
    use WithPagination;

    public $clientId;

    public $practitionerId;

    public $created = [];

    // Data table properties
    public $search = '';

    public $perPage = 5;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    // Fields for CPT-based services
    public $query;

    public $results = [];

    public $cpt_id;

    public $cpt_price;

    public $cpt_specialty;

    public $cpt_complexity = 'medium';

    public $cpt_duration;

    public $cpt_requires_auth = false;

    public $cpt_covered_insurance = true;

    public $cpt_patient_copay = 0;

    // Fields for custom services
    public $custom_name;

    public $custom_description;

    public $custom_service_type;

    public $custom_price;

    public $custom_specialty;

    public $custom_complexity = 'medium';

    public $custom_duration;

    public $custom_requires_auth = false;

    public $custom_covered_insurance = true;

    public $custom_patient_copay = 0;

    public $custom_revenue_code;

    // Edit mode
    public $editingId = null;

    public $editingService = [];

    protected $rules = [
        'cpt_id' => 'required',
        'cpt_price' => 'required|numeric|min:0',
        'cpt_specialty' => 'nullable|string|max:100',
        'cpt_complexity' => 'required|in:low,medium,high',
        'cpt_duration' => 'nullable|numeric|min:0',
        'cpt_requires_auth' => 'boolean',
        'cpt_covered_insurance' => 'boolean',
        'cpt_patient_copay' => 'nullable|numeric|min:0',
        'custom_name' => 'required|string|max:500',
        'custom_service_type' => 'required|string',
        'custom_price' => 'required|numeric|min:0'
    ];

    protected $messages = [
        'cpt_id.required' => 'Debe seleccionar un procedimiento.',
        'cpt_price.required' => 'El precio es obligatorio.',
        'cpt_price.numeric' => 'El precio debe ser un número.',
        'cpt_price.min' => 'El precio debe ser mayor a 0.',
        'custom_name.required' => 'El nombre del servicio es obligatorio.',
        'custom_service_type.required' => 'El tipo de servicio es obligatorio.',
        'custom_price.required' => 'El precio es obligatorio.',
        'custom_price.numeric' => 'El precio debe ser un número.',
        'custom_name.required' => 'El nombre del servicio es obligatorio.',
        'custom_name.max' => 'El nombre no puede exceder los 500 caracteres.'
    ];

    public function mount()
    {
        if (auth()->user()->hasRole('doctor')) {
            $this->practitionerId = auth()->user()->practitioner->id;
        }
        if (auth()->user()->clients()->first()) {
            $this->clientId = auth()->user()->clients()->first()->id;
        }
        $this->loadServices();
    }

    public function render()
    {
        return view('livewire.settings.service-catalog');
    }

    public function loadServices()
    {
        $this->created = ServiceCatalogModel::where('client_id', $this->clientId)->orderBy('created_at', 'desc')->get();
    }

    public function getServicesProperty()
    {
        return ServiceCatalogModel::where('client_id', $this->clientId)
            ->when($this->search, function ($query) {
                return $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->orWhere('cpt_code', 'like', '%'.$this->search.'%')
                    ->orWhere('specialty', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];

            return;
        }

        $response = Http::get(url('api/cpts/procedure'), [
            'dropdown' => true,
            'q' => $this->query,
        ]);

        $this->results = $response->json() ?? [];
    }

    public function selectOption($option)
    {
        $this->cpt_id = $option;
        $this->query = $option['name'];
        $this->results = [];
    }

    public function saveCptService()
    {
        $this->validate([
            'cpt_id' => 'required',
            'cpt_price' => 'required|numeric|min:0',
            'cpt_complexity' => 'required|in:low,medium,high',
            'cpt_duration' => 'nullable|numeric|min:0',
            'cpt_requires_auth' => 'boolean',
            'cpt_covered_insurance' => 'boolean',
            'cpt_patient_copay' => 'nullable|numeric|min:0',
        ], [
            'cpt_id.required' => 'Debe seleccionar un procedimiento.',
            'cpt_price.required' => 'El precio es obligatorio.',
            'cpt_price.numeric' => 'El precio debe ser un número.',
            'cpt_price.min' => 'El precio debe ser mayor a 0.',
        ]);

        $cpt = CptCode::whereId($this->cpt_id)->first();

        ServiceCatalogModel::create([
            'name' => $cpt->tpye.' '.$cpt->code,
            'description' => $cpt->description_es ?? $cpt->description,
            'cpt_code' => $cpt->code,
            'service_type' => 'procedure',
            'base_price' => $this->cpt_price,
            'specialty' => $this->cpt_specialty,
            'complexity' => $this->cpt_complexity,
            'duration_minutes' => $this->cpt_duration,
            'requires_authorization' => $this->cpt_requires_auth,
            'covered_by_insurance' => $this->cpt_covered_insurance,
            'patient_copay' => $this->cpt_patient_copay ?? 0,
            'is_active' => true,
            'effective_date' => now(),
            'client_id' => $this->clientId,
            'practitioner_id' => $this->practitionerId,
            'created_by' => auth()->id(),
        ]);

        // Dispatch event to refresh setup reminders
        $this->dispatch('refreshSetupReminders');


        $this->dispatch('showToastrServiceCatalog',
            type: 'success',
            message: '¡Servicio guardado exitosamente!',
        );
        $this->loadServices();
        $this->resetCptForm();
    }

    public function saveCustomService()
    {
        $this->validate([
            'custom_name' => 'required|string|max:255',
            'custom_service_type' => 'required|string',
            'custom_price' => 'required|numeric|min:0',
            'custom_complexity' => 'required|in:low,medium,high',
            'custom_duration' => 'nullable|numeric|min:0',
            'custom_requires_auth' => 'boolean',
            'custom_covered_insurance' => 'boolean',
            'custom_patient_copay' => 'nullable|numeric|min:0',
        ], [
            'custom_name.required' => 'El nombre del servicio es obligatorio.',
            'custom_service_type.required' => 'El tipo de servicio es obligatorio.',
            'custom_price.required' => 'El precio es obligatorio.',
            'custom_price.numeric' => 'El precio debe ser un número.',
            'custom_price.min' => 'El precio debe ser mayor a 0.',
        ]);

        ServiceCatalogModel::create([
            'name' => $this->custom_name,
            'description' => $this->custom_description,
            'service_type' => $this->custom_service_type,
            'base_price' => $this->custom_price,
            'specialty' => $this->custom_specialty,
            'complexity' => $this->custom_complexity,
            'duration_minutes' => $this->custom_duration,
            'requires_authorization' => $this->custom_requires_auth,
            'covered_by_insurance' => $this->custom_covered_insurance,
            'patient_copay' => $this->custom_patient_copay ?? 0,
            'revenue_code' => $this->custom_revenue_code,
            'is_active' => true,
            'effective_date' => now(),
            'created_by' => auth()->id(),
            'client_id' => $this->clientId,
            'practitioner_id' => $this->practitionerId,
        ]);

        // Dispatch event to refresh setup reminders
        $this->dispatch('refreshSetupReminders');

        $this->dispatch('showToastrServiceCatalog',
            type: 'success',
            message: '¡Servicio personalizado guardado exitosamente!'
        );

        $this->loadServices();
        $this->resetCustomForm();
    }

    public function editService($id)
    {
        $service = ServiceCatalogModel::find($id);

        if (! $service || $service->created_by !== auth()->id()) {
            return;
        }

        $this->editingId = $id;
        $this->editingService = [
            'name' => $service->name,
            'description' => $service->description,
            'base_price' => $service->base_price,
            'specialty' => $service->specialty,
            'complexity' => $service->complexity,
            'duration_minutes' => $service->duration_minutes,
            'requires_authorization' => $service->requires_authorization,
            'covered_by_insurance' => $service->covered_by_insurance,
            'patient_copay' => $service->patient_copay,
            'revenue_code' => $service->revenue_code,
        ];
    }

    public function updateService()
    {
        $this->validate([
            'editingService.name' => 'required|string|max:255',
            'editingService.base_price' => 'required|numeric|min:0',
            'editingService.complexity' => 'required|in:low,medium,high',
            'editingService.duration_minutes' => 'nullable|numeric|min:0',
            'editingService.patient_copay' => 'nullable|numeric|min:0',
        ]);

        $service = ServiceCatalogModel::find($this->editingId);

        if (! $service || $service->created_by !== auth()->id()) {
            return;
        }

        $service->update([
            'name' => $this->editingService['name'],
            'description' => $this->editingService['description'],
            'base_price' => $this->editingService['base_price'],
            'specialty' => $this->editingService['specialty'],
            'complexity' => $this->editingService['complexity'],
            'duration_minutes' => $this->editingService['duration_minutes'],
            'requires_authorization' => $this->editingService['requires_authorization'],
            'covered_by_insurance' => $this->editingService['covered_by_insurance'],
            'patient_copay' => $this->editingService['patient_copay'] ?? 0,
            'revenue_code' => $this->editingService['revenue_code'],
            'updated_by' => auth()->id(),
        ]);


        $this->dispatch('showToastrServiceCatalog',
            type: 'success',
            message: '¡Servicio actualizado exitosamente!',
        );

        $this->loadServices();
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->editingId = null;
        $this->editingService = [];
    }

    public function toggleActive($id)
    {
        $service = ServiceCatalogModel::find($id);

        if (! $service || $service->created_by !== auth()->id()) {
            return;
        }

        $service->update([
            'is_active' => ! $service->is_active,
            'updated_by' => auth()->id(),
        ]);


        $this->dispatch('showToastrServiceCatalog',
            type: 'info',
            message: $service->is_active ? 'Servicio activado' : 'Servicio desactivado',
        );


        $this->loadServices();
    }

    public function deleteService($id)
    {
        $service = ServiceCatalogModel::find($id);

        if (! $service || $service->created_by !== auth()->id()) {
            return;
        }

        $service->delete();


        $this->dispatch('showToastrServiceCatalog',
            type: 'error',
            message: '¡Servicio eliminado exitosamente!',
        );

        $this->loadServices();
    }

    public function resetCptForm()
    {
        $this->reset([
            'cpt_id', 'cpt_price', 'cpt_specialty', 'cpt_complexity',
            'cpt_duration', 'cpt_requires_auth', 'cpt_covered_insurance',
            'cpt_patient_copay', 'query',
        ]);
        $this->cpt_complexity = 'medium';
        $this->cpt_covered_insurance = true;
    }

    public function resetCustomForm()
    {
        $this->reset([
            'custom_name', 'custom_description', 'custom_service_type',
            'custom_price', 'custom_specialty', 'custom_complexity',
            'custom_duration', 'custom_requires_auth', 'custom_covered_insurance',
            'custom_patient_copay', 'custom_revenue_code',
        ]);
        $this->custom_complexity = 'medium';
        $this->custom_covered_insurance = true;
    }

    public function getServiceTypes()
    {
        return [
            'consultation' => 'Consulta',
            'procedure' => 'Procedimiento',
            // 'diagnostic' => 'Diagnóstico',
            'therapeutic' => 'Terapéutico',
            'surgical' => 'Quirúrgico',
            'laboratory' => 'Laboratorio',
            'imaging' => 'Imagenología',
            // 'medication' => 'Medicamento',
            'supply' => 'Suministro',
            // 'facility' => 'Facilidad',
            'other' => 'Otro',
        ];
    }

    public function getComplexityTypes()
    {
        return [
            'low' => 'Baja',
            'medium' => 'Media',
            'high' => 'Alta',
        ];
    }
}
