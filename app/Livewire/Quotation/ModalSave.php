<?php

namespace App\Livewire\Quotation;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\ServiceType;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalSave extends Component
{
    #[Modelable]
    public $showModal = false;

    public $quotation_id;

    public $title = 'Crear Cotización';

    public $buttonSaveTitle = 'Guardar Cotización';

    // Datos del cliente
    public $client_company_name = '';

    public $client_ruc = '';

    public $client_phone = '';

    public $client_email = '';

    public $client_address = '';

    // Fechas
    public $issue_date = '';

    public $expiration_date = '';

    // Items
    public $items = [];

    // Totales (calculados)
    public $subtotal = 0;

    public $itbms = 0;

    public $itbms_rate = 7.00;

    public $total = 0;

    // Servicios disponibles
    public $availableServices = [];

    // Notas
    public $notes = '';

    public function mount(): void
    {
        $this->loadAvailableServices();
        $this->issue_date = now()->format('Y-m-d');
        $this->expiration_date = now()->addDays(30)->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'client_company_name' => 'required|string|max:255',
            'client_ruc' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiration_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1',
            'items.*.service_type_id' => 'required|exists:service_types,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    protected $messages = [
        'client_company_name.required' => 'La razón social es obligatoria.',
        'client_ruc.required' => 'El RUC es obligatorio.',
        'issue_date.required' => 'La fecha de emisión es obligatoria.',
        'expiration_date.required' => 'La fecha de vencimiento es obligatoria.',
        'expiration_date.after_or_equal' => 'La fecha de vencimiento debe ser posterior a la fecha de emisión.',
        'items.required' => 'Debe agregar al menos un servicio.',
        'items.min' => 'Debe agregar al menos un servicio.',
        'items.*.service_type_id.required' => 'Debe seleccionar un tipo de servicio.',
        'items.*.quantity.required' => 'La cantidad es obligatoria.',
        'items.*.quantity.min' => 'La cantidad debe ser al menos 1.',
        'items.*.unit_price.required' => 'El precio unitario es obligatorio.',
        'items.*.unit_price.min' => 'El precio debe ser mayor o igual a 0.',
    ];

    public function loadAvailableServices(): void
    {
        $this->availableServices = ServiceType::where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'id' => null,
            'service_type_id' => '',
            'service_name' => '',
            'service_description' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'subtotal' => 0,
            'notes' => '',
        ];
    }

    public function removeItem($index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->calculateTotals();
        }
    }

    public function updatedItems($value, $key): void
    {
        // Cuando cambia el servicio seleccionado
        if (str_contains($key, 'service_type_id')) {
            $index = (int) explode('.', $key)[0];
            $serviceType = ServiceType::find($this->items[$index]['service_type_id']);

            if ($serviceType) {
                $this->items[$index]['service_name'] = $serviceType->name;
                $this->items[$index]['service_description'] = $serviceType->description;
                $this->items[$index]['unit_price'] = $serviceType->base_cost;
            }
        }

        // Recalcular subtotal del item
        if (str_contains($key, 'quantity') || str_contains($key, 'unit_price')) {
            $index = (int) explode('.', $key)[0];
            $this->items[$index]['subtotal'] =
                $this->items[$index]['quantity'] * $this->items[$index]['unit_price'];
        }

        $this->calculateTotals();
    }

    public function updatedIssueDate(): void
    {
        if ($this->issue_date) {
            $this->expiration_date = Carbon::parse($this->issue_date)->addDays(30)->format('Y-m-d');
        }
    }

    public function calculateTotals(): void
    {
        $this->subtotal = collect($this->items)->sum('subtotal');
        $this->itbms = $this->subtotal * ($this->itbms_rate / 100);
        $this->total = $this->subtotal + $this->itbms;
    }

    public function render()
    {
        return view('livewire.quotation.modal-save');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    #[On('openQuotationModal')]
    public function openModal($title): void
    {
        $this->resetForm();
        $this->showModal = true;
        $this->title = $title;
        $this->loadAvailableServices();
    }

    public function resetForm(): void
    {
        $this->quotation_id = null;
        $this->client_company_name = '';
        $this->client_ruc = '';
        $this->client_phone = '';
        $this->client_email = '';
        $this->client_address = '';
        $this->issue_date = now()->format('Y-m-d');
        $this->expiration_date = now()->addDays(30)->format('Y-m-d');
        $this->items = [
            [
                'id' => null,
                'service_type_id' => '',
                'service_name' => '',
                'service_description' => '',
                'quantity' => 1,
                'unit_price' => 0,
                'subtotal' => 0,
                'notes' => '',
            ],
        ];
        $this->notes = '';
        $this->calculateTotals();
        $this->buttonSaveTitle = 'Guardar Cotización';
    }

    public function save(): void
    {
        $this->validate();

        try {
            $quotationData = [
                'client_company_name' => $this->client_company_name,
                'client_ruc' => $this->client_ruc,
                'client_phone' => $this->client_phone,
                'client_email' => $this->client_email,
                'client_address' => $this->client_address,
                'issue_date' => $this->issue_date,
                'expiration_date' => $this->expiration_date,
                'currency' => 'USD',
                'subtotal' => $this->subtotal,
                'itbms' => $this->itbms,
                'itbms_rate' => $this->itbms_rate,
                'total' => $this->total,
                'notes' => $this->notes,
                'updated_by' => auth()->id(),
            ];

            if ($this->quotation_id) {
                // Actualizar cotización existente
                $quotation = Quotation::findOrFail($this->quotation_id);

                if (! $quotation->canBeEdited()) {
                    throw new \Exception('Esta cotización no puede ser editada.');
                }

                $quotation->update($quotationData);

                // Actualizar items
                $existingIds = $quotation->items()->pluck('id')->toArray();
                $updatedIds = [];

                foreach ($this->items as $itemData) {
                    if (! empty($itemData['id'])) {
                        // Actualizar item existente
                        QuotationItem::where('id', $itemData['id'])->update([
                            'service_type_id' => $itemData['service_type_id'],
                            'service_name' => $itemData['service_name'],
                            'service_description' => $itemData['service_description'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['unit_price'],
                            'subtotal' => $itemData['subtotal'],
                            'notes' => $itemData['notes'],
                        ]);
                        $updatedIds[] = $itemData['id'];
                    } else {
                        // Crear nuevo item
                        $newItem = $quotation->items()->create([
                            'service_type_id' => $itemData['service_type_id'],
                            'service_name' => $itemData['service_name'],
                            'service_description' => $itemData['service_description'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['unit_price'],
                            'subtotal' => $itemData['subtotal'],
                            'notes' => $itemData['notes'],
                        ]);
                        $updatedIds[] = $newItem->id;
                    }
                }

                // Eliminar items removidos
                $toDelete = array_diff($existingIds, $updatedIds);
                if (! empty($toDelete)) {
                    QuotationItem::whereIn('id', $toDelete)->delete();
                }

                $this->dispatch('showToastr',
                    type: 'success',
                    message: '¡Actualizado exitosamente!'
                );
            } else {
                // Crear nueva cotización
                $quotationData['fhir_id'] = 'quotation-'.Str::uuid();
                $quotationData['created_by'] = auth()->id();
                $quotationData['status'] = 'draft';

                $quotation = Quotation::create($quotationData);

                // Crear items
                foreach ($this->items as $itemData) {
                    $quotation->items()->create([
                        'service_type_id' => $itemData['service_type_id'],
                        'service_name' => $itemData['service_name'],
                        'service_description' => $itemData['service_description'],
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'subtotal' => $itemData['subtotal'],
                        'notes' => $itemData['notes'],
                    ]);
                }

                $this->dispatch('showToastr',
                    type: 'success',
                    message: '¡Creado exitosamente!'
                );
            }

            $this->closeModal();
            $this->dispatch('refreshQuotations');

        } catch (\Exception $e) {
            $this->dispatch('showToastr',
                type: 'error',
                message: 'Error al guardar: '.$e->getMessage()
            );
        }
    }

    #[On('editQuotationModal')]
    public function edit($quotation_id): void
    {
        $quotation = Quotation::with('items')->find($quotation_id);

        if ($quotation) {
            $this->quotation_id = $quotation_id;
            $this->title = 'Actualizar Cotización';
            $this->buttonSaveTitle = 'Actualizar Cotización';

            $this->client_company_name = $quotation->client_company_name;
            $this->client_ruc = $quotation->client_ruc;
            $this->client_phone = $quotation->client_phone;
            $this->client_email = $quotation->client_email;
            $this->client_address = $quotation->client_address;
            $this->issue_date = $quotation->issue_date->format('Y-m-d');
            $this->expiration_date = $quotation->expiration_date->format('Y-m-d');
            $this->notes = $quotation->notes;

            // Cargar items
            $this->items = $quotation->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'service_type_id' => $item->service_type_id,
                    'service_name' => $item->service_name,
                    'service_description' => $item->service_description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                    'notes' => $item->notes,
                ];
            })->toArray();

            if (empty($this->items)) {
                $this->items = [
                    [
                        'id' => null,
                        'service_type_id' => '',
                        'service_name' => '',
                        'service_description' => '',
                        'quantity' => 1,
                        'unit_price' => 0,
                        'subtotal' => 0,
                        'notes' => '',
                    ],
                ];
            }

            $this->calculateTotals();
            $this->showModal = true;
        }
    }
}
