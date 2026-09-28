<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Subir Documento</h2>

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
                wire:model.defer="client_id"
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

        <!-- File Upload -->
        <div>
            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Archivo PDF</label>
            <input
                type="file"
                id="file"
                wire:model.defer="file"
                accept=".pdf"
                class="w-full block text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-md file:border-0
                  file:text-sm file:font-semibold
                  file:bg-blue-50 file:text-blue-700
                  hover:file:bg-blue-100"
            />
            @error('file')
                <span class="text-red-600 text-sm block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium py-2 px-4 rounded-lg transition-colors"
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>Subir Documento</span>
            <span wire:loading class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Subiendo...
            </span>
        </button>
    </form>
</div>
