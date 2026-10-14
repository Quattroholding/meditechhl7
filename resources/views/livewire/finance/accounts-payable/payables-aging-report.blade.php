<div class="space-y-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">

        <div class="flex gap-2 items-center">
            <label class="text-sm font-medium">A partir de:</label>
            <input
                type="date"
                wire:model.live="asOfDate"
                class="px-4 py-2 border rounded-lg"
            />
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-gray-600 text-sm">0-30 días</p>
            <p class="text-2xl font-bold">{{ number_format($summary['0-30'], 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-gray-600 text-sm">31-60 días</p>
            <p class="text-2xl font-bold">{{ number_format($summary['31-60'], 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-gray-600 text-sm">61-90 días</p>
            <p class="text-2xl font-bold">{{ number_format($summary['61-90'], 2) }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-gray-600 text-sm">Más de 90 días</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($summary['90+'], 2) }}</p>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg shadow border border-blue-200">
            <p class="text-gray-600 text-sm font-semibold">Total CxP</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($summary['total'], 2) }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive mt-5">
            <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                <thead>
                <tr>
                    <th class="">Proveedor</th>
                    <th class="">Factura #</th>
                    <th class="">Fecha Factura</th>
                    <th class="">Vencimiento</th>
                    <th class="">Balance</th>
                    <th class="">Días Vencido</th>
                    <th class="">Rango</th>
                    <th class="">Estado</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                @forelse($ageingData as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium">{{ $item['supplier'] }}</td>
                        <td class="px-6 py-4 text-sm">{{ $item['invoice_number'] }}</td>
                        <td class="px-6 py-4 text-sm">{{ $item['invoice_date'] }}</td>
                        <td class="px-6 py-4 text-sm">{{ $item['due_date'] }}</td>
                        <td class="px-6 py-4 text-right text-sm font-semibold">
                            {{ number_format($item['balance'], 2) }}
                        </td>
                        <td class="px-6 py-4 text-center text-sm">
                            <span
                                class="{{ $item['days_overdue'] > 90 ? 'font-bold text-red-600' : '' }}"
                            >
                                {{ $item['days_overdue'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium
                                @switch($item['range'])
                                    @case('0-30') bg-green-100 text-green-800 @break
                                    @case('31-60') bg-yellow-100 text-yellow-800 @break
                                    @case('61-90') bg-orange-100 text-orange-800 @break
                                    @case('90+') bg-red-100 text-red-800 @break
                                @endswitch
                            "
                            >
                                {{ $item['range'] }} días
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium
                                @if ($item['status'] === 'overdue')
                                    bg-red-100 text-red-800
                                @elseif ($item['status'] === 'partial')
                                    bg-yellow-100 text-yellow-800
                                @else
                                    bg-blue-100 text-blue-800
                                @endif
                            "
                            >
                                {{ ucfirst($item['status']) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            No hay cuentas por pagar pendientes
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
</div>
