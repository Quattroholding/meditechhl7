<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" wire:poll="$refresh">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Gestión de Documentos</h2>
            <p class="text-sm text-gray-500 mt-1">Total: {{ $documents->total() }} documentos</p>
        </div>
        <div class="text-xs text-gray-400">
            <i class="fa fa-sync-alt"></i> Actualizando automáticamente...
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <select
                wire:model.live="statusFilter"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
            <input
                type="text"
                wire:model.live="search"
                placeholder="Nombre de archivo..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-700 cursor-pointer hover:bg-gray-100" wire:click="setSortBy('original_filename')">
                        <div class="flex items-center gap-2">
                            Archivo
                            @if ($sortBy === 'original_filename')
                                <span>@if ($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                            @endif
                        </div>
                    </th>
                    <th class="px-4 py-3 text-left font-medium text-gray-700">Tipo</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-700">Estado</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-700">Confianza</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-700">Subido Por</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-700 cursor-pointer hover:bg-gray-100" wire:click="setSortBy('created_at')">
                        <div class="flex items-center gap-2">
                            Fecha
                            @if ($sortBy === 'created_at')
                                <span>@if ($sortDirection === 'asc') ↑ @else ↓ @endif</span>
                            @endif
                        </div>
                    </th>
                    <th class="px-4 py-3 text-left font-medium text-gray-700">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($documents as $document)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <i class="fa fa-file-pdf text-red-500"></i>
                                <span class="text-gray-900 font-medium">{{ Str::limit($document->original_filename, 25) }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-600 text-xs">
                                @if ($document->document_type)
                                    {{ $document->document_type->label() }}
                                @else
                                    -
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-medium
                                @if ($document->status->value === 'pending_parsing') bg-orange-100 text-orange-800
                                @elseif ($document->status->value === 'parsing') bg-yellow-100 text-yellow-800
                                @elseif ($document->status->value === 'parsing_failed') bg-red-100 text-red-800
                                @elseif ($document->status->value === 'parsed') bg-blue-100 text-blue-800
                                @elseif ($document->status->value === 'approved') bg-green-100 text-green-800
                                @elseif ($document->status->value === 'processing') bg-indigo-100 text-indigo-800
                                @elseif ($document->status->value === 'processed') bg-green-100 text-green-800
                                @elseif ($document->status->value === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ $document->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($document->parseResult && $document->parseResult->confidence_score)
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                        <div
                                            class="@if ($document->parseResult->confidence_score >= 0.8) bg-green-500 @elseif ($document->parseResult->confidence_score >= 0.6) bg-yellow-500 @else bg-red-500 @endif h-1.5 rounded-full"
                                            style="width: {{ ($document->parseResult->confidence_score * 100) }}%"
                                        ></div>
                                    </div>
                                    <span class="text-xs text-gray-600 w-8">{{ round($document->parseResult->confidence_score * 100) }}%</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-600 text-xs">
                                @if ($document->uploadedByUser)
                                    {{ $document->uploadedByUser->name }}
                                @else
                                    -
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-600 text-xs">{{ $document->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                @if ($document->status->value === 'parsed')
                                    <a href="{{ route('documents.detail', $document) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm" title="Revisar documento">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                @elseif ($document->status->value === 'approved' || $document->status->value === 'processing' || $document->status->value === 'processed')
                                    <a href="{{ route('documents.detail', $document) }}" class="text-green-600 hover:text-green-700 font-medium text-sm" title="Ver detalles">
                                        <i class="fa fa-check"></i>
                                    </a>
                                @elseif ($document->status->value === 'parsing')
                                    <span class="text-yellow-600 text-xs flex items-center gap-1">
                                        <i class="fa fa-spinner fa-spin"></i> Procesando
                                    </span>
                                @elseif ($document->status->value === 'parsing_failed')
                                    <span class="text-red-600 text-xs">
                                        <i class="fa fa-exclamation-circle"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fa fa-inbox text-2xl mb-2 block"></i>
                            No hay documentos para mostrar
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if ($documents->hasPages())
        <div class="mt-6 flex justify-center">
            <nav class="inline-flex gap-2">
                {{ $documents->links() }}
            </nav>
        </div>
    @endif
</div>
