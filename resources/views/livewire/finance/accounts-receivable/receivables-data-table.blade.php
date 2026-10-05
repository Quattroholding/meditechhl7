<div class="space-y-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Cuentas por Cobrar</h2>
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <input
                type="text"
                wire:model.live="search"
                placeholder="Buscar por número o paciente..."
                class="w-full px-4 py-2 border rounded-lg"
            />
        </div>
        <div>
            <select wire:model.live="status" class="w-full px-4 py-2 border rounded-lg">
                <option value="">Todos los estados</option>
                <option value="pending">Pendiente</option>
                <option value="partial">Parcial</option>
                <option value="paid">Pagada</option>
                <option value="overdue">Vencida</option>
                <option value="cancelled">Cancelada</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Factura #</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Paciente</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Fecha</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Vencimiento</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Total</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Pagado</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Saldo</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Estado</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($receivables as $receivable)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium">{{ $receivable->invoice_number }}</td>
                        <td class="px-6 py-4 text-sm">{{ $receivable->patient->full_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $receivable->invoice_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm">{{ $receivable->due_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right text-sm">{{ number_format($receivable->original_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm">{{ number_format($receivable->paid_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-semibold">
                            {{ number_format($receivable->balance, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium
                                @switch($receivable->status->value)
                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                    @case('partial') bg-blue-100 text-blue-800 @break
                                    @case('paid') bg-green-100 text-green-800 @break
                                    @case('overdue') bg-red-100 text-red-800 @break
                                    @case('cancelled') bg-gray-100 text-gray-800 @break
                                @endswitch
                            "
                            >
                                {{ match($receivable->status->value) {
                                    'pending' => 'Pendiente',
                                    'partial' => 'Parcial',
                                    'paid' => 'Pagada',
                                    'overdue' => 'Vencida',
                                    'cancelled' => 'Cancelada',
                                    default => $receivable->status->value
                                } }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            @if ($receivable->status->value !== 'paid' && $receivable->status->value !== 'cancelled')
                                <button
                                    wire:click="openPaymentModal({{ $receivable->id }})"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Aplicar Pago
                                </button>
                            @endif
                            @if ($receivable->status->value !== 'paid')
                                <button
                                    wire:click="cancel({{ $receivable->id }})"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Cancelar
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                            No hay cuentas por cobrar registradas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $receivables->links() }}
    </div>

    {{-- Payment Modal --}}
    @if ($showPaymentModal && $selectedReceivable)
        @livewire('finance.accounts-receivable.payment-application-modal', ['receivable' => $selectedReceivable], key('payment-modal-' . $selectedReceivable->id))
    @endif
</div>
