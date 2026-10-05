<div class="space-y-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Facturas a Crédito</h2>
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <th class="px-6 py-3 text-left text-sm font-semibold">Fecha Emisión</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Vencimiento</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Total</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Pagado</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Saldo</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4 text-sm">{{ $invoice->patient->full_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $invoice->issue_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm">{{ $invoice->due_date?->format('d/m/Y') ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-right text-sm">{{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm">{{ number_format($invoice->amount_paid, 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm font-semibold">
                            {{ number_format($invoice->accountsReceivable->balance ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium
                                @if ($invoice->payment_status->value === 'pending')
                                    bg-yellow-100 text-yellow-800
                                @elseif ($invoice->payment_status->value === 'partial')
                                    bg-blue-100 text-blue-800
                                @elseif ($invoice->payment_status->value === 'paid')
                                    bg-green-100 text-green-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif
                            "
                            >
                                {{ ucfirst($invoice->payment_status->value) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            No hay facturas a crédito registradas
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
</div>
