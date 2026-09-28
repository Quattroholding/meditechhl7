<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Documentos para Revisar</h2>
    </div>

    <!-- Filtros -->
    <div class="mb-6 flex gap-4">
        <select
            wire:model.live="filter"
            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <option value="parsed">Procesados (Pendientes)</option>
            <option value="approved">Aprobados</option>
            <option value="rejected">Rechazados</option>
            <option value="pending">Pendientes de Procesar</option>
            <option value="all">Todos</option>
        </select>

        <input
            type="text"
            wire:model.live="search"
            placeholder="Buscar por nombre de archivo..."
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-700 cursor-pointer hover:bg-gray-100" wire:click="setSortBy('original_filename')">
                        Archivo
                        @if ($sortBy === 'original_filename')
                            <span class="ml-1">@if ($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                        @endif
                    </th>
                    <th class="px-4 py-3 text-left font-medium text-gray-700">Tipo</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-700">Estado</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-700">Confianza</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-700 cursor-pointer hover:bg-gray-100" wire:click="setSortBy('created_at')">
                        Fecha
                        @if ($sortBy === 'created_at')
                            <span class="ml-1">@if ($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                        @endif
                    </th>
                    <th class="px-4 py-3 text-left font-medium text-gray-700">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($documents as $document)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="text-gray-900">{{ Str::limit($document->original_filename, 30) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-600">{{ $document->document_type->label() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if ($document->status->value === 'parsed') bg-blue-100 text-blue-800
                                @elseif ($document->status->value === 'approved') bg-green-100 text-green-800
                                @elseif ($document->status->value === 'rejected') bg-red-100 text-red-800
                                @elseif ($document->status->value === 'processing') bg-yellow-100 text-yellow-800
                                @elseif ($document->status->value === 'processed') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ $document->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($document->parseResult)
                                <div class="flex items-center gap-2">
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div
                                            class="bg-blue-600 h-2 rounded-full"
                                            style="width: {{ ($document->parseResult->confidence_score ?? 0) * 100 }}%"
                                        ></div>
                                    </div>
                                    <span class="text-xs text-gray-600">{{ round(($document->parseResult->confidence_score ?? 0) * 100) }}%</span>
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-600">{{ $document->created_at }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($document->status->value === 'parsed')
                                <button
                                    wire:click="selectDocument({{ $document->id }})"
                                    class="text-blue-600 hover:text-blue-700 font-medium text-sm"
                                >
                                    Revisar
                                </button>
                            @elseif ($document->status->value === 'parsing')
                                <span class="text-yellow-600 text-sm">Procesando...</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            No hay documentos para mostrar
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if ($documents->hasPages())
        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    @endif

    <!-- Modal de Aprobación -->
    @livewire('documents.document-approval-modal')
</div>
