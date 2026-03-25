<?php

namespace App\Livewire\Subscription;

use App\Models\ClientInvoice;
use App\Notifications\InvoiceGeneratedNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceDataTable extends Component
{
    use WithPagination;

    protected $listeners = ['paymentSaved' => '$refresh'];

    public $search = '';

    public $statusFilter = '';

    public $perPage = 10;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
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
        $this->resetPage();
    }

    public function getInvoicesProperty()
    {
        return ClientInvoice::query()
            ->with(['client', 'subscription.package', 'payments'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('client', function ($clientQuery) {
                            $clientQuery->where('name', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function resendNotification($invoiceId)
    {
        try {
            // Verificar que el usuario tenga permisos de administrador
            if (! auth()->user()->hasRole(['admin', 'contabilidad'])) {
                session()->flash('error', 'No tienes permisos para realizar esta acción.');

                return;
            }

            $invoice = ClientInvoice::with(['client', 'subscription.package'])->findOrFail($invoiceId);

            // Obtener el usuario a notificar
            $notifiableUser = InvoiceGeneratedNotification::getNotifiableUser($invoice);

            if (! $notifiableUser) {
                session()->flash('error', 'No se encontró un usuario válido para enviar la notificación.');

                return;
            }

            // Enviar la notificación
            $notifiableUser->notify(new InvoiceGeneratedNotification($invoice));

            \Log::info('Notificación de factura reenviada manualmente', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'user_id' => auth()->id(),
                'notifiable_user_id' => $notifiableUser->id,
            ]);

            session()->flash('success', 'Notificación enviada exitosamente a '.$notifiableUser->email);
        } catch (\Exception $e) {
            \Log::error('Error al reenviar notificación de factura', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Error al enviar la notificación: '.$e->getMessage());
        }
    }

    public function render()
    {
        try {
            $invoices = $this->invoices;

            return view('livewire.subscription.invoice-data-table', [
                'invoices' => $invoices,
            ]);
        } catch (\Exception $e) {
            \Log::error('Subscription Invoice DataTable Error: '.$e->getMessage());

            return view('livewire.subscription.invoice-data-table', [
                'invoices' => new LengthAwarePaginator([], 0, 10),
            ]);
        }
    }
}
