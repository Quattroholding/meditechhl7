<div class="space-y-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Facturas de Proveedores</h2>
        <button
            wire:click="openModal"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
            + Nueva Factura
        </button>
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <input
                type="text"
                wire:model.live="search"
                placeholder="Buscar por número o proveedor..."
                class="w-full px-4 py-2 border rounded-lg"
            />
        </div>
        <div>
            <select wire:model.live="status" class="w-full px-4 py-2 border rounded-lg">
                <option value="">Todos los estados</option>
                <option value="draft">Borrador</option>
                <option value="approved">Aprobada</option>
                <option value="partial">Parcialmente Pagada</option>
                <option value="paid">Pagada</option>
                <option value="overdue">Vencida</option>
            </select>
        </div>
        <div colspan="2">
            <select wire:model.live="supplier" class="w-full px-4 py-2 border rounded-lg">
                <option value="">Todos los proveedores</option>
                @foreach (\App\Models\Supplier::where('client_id', auth()->user()->getCurrentClient()->id)->get() as $sup)
                    <option value="{{ $sup->id }}">{{ $sup->legal_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Factura #</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Proveedor</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Fecha</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Vencimiento</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Total</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Pagado</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Estado</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4 text-sm">{{ $invoice->supplier->legal_name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm">{{ $invoice->due_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right text-sm">{{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm">{{ number_format($invoice->paid_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium
                                @switch($invoice->status)
                                    @case('draft') bg-gray-100 text-gray-800 @break
                                    @case('approved') bg-blue-100 text-blue-800 @break
                                    @case('partial') bg-yellow-100 text-yellow-800 @break
                                    @case('paid') bg-green-100 text-green-800 @break
                                    @case('overdue') bg-red-100 text-red-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch
                            "
                            >
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            @if ($invoice->status === 'draft')
                                <button
                                    wire:click="openModal({{ $invoice->id }})"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Editar
                                </button>
                                <button
                                    wire:click="approve({{ $invoice->id }})"
                                    class="text-green-600 hover:text-green-900"
                                >
                                    Aprobar
                                </button>
                            @elseif($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                                <button
                                    wire:click="$dispatch('openPaymentSchedule', { invoiceId: {{ $invoice->id }} })"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Pagar
                                </button>
                            @endif
                            @if ($invoice->status === 'draft')
                                <button
                                    wire:click="cancel({{ $invoice->id }})"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Cancelar
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            No hay facturas registradas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $invoices->links() }}
    </div>

    {{-- Modal --}}
    @if ($showModal)
        @livewire('finance.accounts-payable.supplier-invoice-modal', ['invoice' => $editingInvoice], key('invoice-modal-' . ($editingInvoice?->id ?? 'new')))
    @endif
</div>
