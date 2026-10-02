<div class="">
    @if ($successMessage)
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm text-green-800">{{ $successMessage }}</span>
        </div>
    @endif

    @if ($errorMessage)
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm text-red-800">{{ $errorMessage }}</span>
        </div>
    @endif

    <form wire:submit="uploadDocument" class="space-y-6">
        <!-- Cliente -->
        <div>
            <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
            <select
                id="client_id"
                wire:model="client_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
                <option value="">Selecciona un cliente</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
            @error('client_id')
                <span class="text-red-600 text-sm block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Sucursal -->
        <div>
            <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">Sucursal</label>
            <select
                id="branch_id"
                wire:model.defer="branch_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @disabled(!$client_id || $branches->isEmpty())
            >
                <option value="">Selecciona una sucursal</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
            @error('branch_id')
                <span class="text-red-600 text-sm block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Tipo de Documento -->
        <div>
            <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Documento</label>
            <select
                id="document_type"
                wire:model.defer="document_type"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
                <option value="">Selecciona un tipo</option>
                @foreach ($documentTypes as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </select>
            @error('document_type')
                <span class="text-red-600 text-sm block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- File Upload (Multiple) -->
        <div>
            <label for="files" class="block text-sm font-medium text-gray-700 mb-2">Archivos PDF (máximo 10)</label>

            <div class="mt-2">
                <!-- Drag & Drop Zone -->
                <div x-data="{ dragging: false }"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; Livewire.dispatch('file-selected', {files: $event.dataTransfer.files})"
                     :class="{ 'ring-2 ring-blue-500 bg-blue-50': dragging }"
                     class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors cursor-pointer hover:border-gray-400">

                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-8l-3.172-3.172a4 4 0 00-5.656 0L28 28M9 20h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                    <p class="mt-2 text-sm text-gray-600">
                        <span class="font-medium text-blue-600 hover:text-blue-500">Haz click para seleccionar</span>
                        o arrastra archivos aquí
                    </p>
                    <p class="text-xs text-gray-500 mt-1">PDF de hasta 10MB cada uno</p>

                    <input
                        x-ref="fileInput"
                        type="file"
                        id="files"
                        wire:model.live="files"
                        accept=".pdf"
                        multiple
                        class="hidden"
                    />
                </div>

                <!-- Click to select -->
                <script>
                    document.querySelector('[x-ref="fileInput"]')?.closest('[x-data]')?.addEventListener('click', (e) => {
                        if (e.target.closest('[x-ref]')) return;
                        document.querySelector('#files').click();
                    });
                </script>

                @error('files')
                    <span class="text-red-600 text-sm block mt-2">{{ $message }}</span>
                @enderror

                @if($files && count($files) > 0)
                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Archivos seleccionados ({{ count($files) }}/10)</h3>
                        <ul class="space-y-2">
                            @foreach($files as $index => $file)
                                <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center gap-2 flex-1">
                                        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm0 2h12v10H4V5z" />
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $file->getClientOriginalName() }}</p>
                                            <p class="text-xs text-gray-500">{{ round($file->getSize() / 1024 / 1024, 2) }} MB</p>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        @click="$wire.removeFile({{ $index }})"
                                        class="ml-2 text-red-600 hover:text-red-800 text-sm font-medium"
                                    >
                                        Eliminar
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        <div class="flex items-center justify-end mt-6">
            <!-- Submit Button -->
            <button
                type="submit"
                class="btn btn-primary me-2"
                wire:loading.attr="disabled"
                :disabled="!$files || $files.length === 0"
            >
                <span wire:loading.remove>
                    @if(count($files ?? []) > 0)
                        Subir {{ count($files) }} {{ count($files) === 1 ? 'documento' : 'documentos' }}
                    @else
                        Selecciona archivos para subir
                    @endif
                </span>
                <span wire:loading class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando...
                </span>
            </button>
            <a class="btn btn-secondary me-2" href="{{ route('patient.index') }}">{{ __('button.cancel') }}</a>
        </div>
    </form>
</div>
