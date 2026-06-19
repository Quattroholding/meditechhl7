<div class="space-y-6"
     x-data="{ clearPreviewsOnUpload: false }"
     @files-uploaded.window="if (clearPreviewsOnUpload) { clearPreviewsOnUpload = false; }"
     @notify.window="
        if ($event.detail.type === 'success') {
            toastr.success($event.detail.message);
        } else if ($event.detail.type === 'error') {
            toastr.error($event.detail.message);
        }
     ">
    <!-- Alerta si no hay almacenamiento externo configurado -->
    @if(!$hasExternalStorage)
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 p-4 mb-6 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700 dark:text-red-200">
                        <strong>Almacenamiento externo requerido:</strong> No se puede subir archivos sin configurar un proveedor externo (Dropbox).
                        @if(auth()->user()->canPaySubscription())
                            <a href="{{ route('setting.external_storage') }}" class="font-medium underline hover:text-red-600 dark:hover:text-red-300">
                                Configurar almacenamiento externo ahora
                            </a>
                        @else
                            Contacte al administrador para configurar el almacenamiento.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Formulario de subida -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 {{ !$hasExternalStorage ? 'opacity-60' : '' }}">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            Subir Archivos de Consulta
            @if($hasExternalStorage)
                <span class="ml-2 text-xs font-normal text-green-600 dark:text-green-400">
                    <svg class="inline w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Dropbox conectado
                </span>
            @endif
        </h3>

        <form wire:submit="uploadFiles" class="{{ !$hasExternalStorage ? 'pointer-events-none' : '' }}">
            <!-- Selector de categoría -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Categoría del Archivo *
                </label>
                <select wire:model="fileCategory"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('fileCategory')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Área de subida de archivos -->
            <div class="mb-4"
                 x-data="{
                     isDragging: false,
                     previews: [],
                     handleDrop(e) {
                         this.isDragging = false;
                         const input = this.$refs.fileInput;
                         input.files = e.dataTransfer.files;
                         input.dispatchEvent(new Event('change', { bubbles: true }));
                         // generatePreviews se llama automáticamente por el evento change
                     },
                     handleFileSelect(e) {
                         this.generatePreviews(e.target.files);
                     },
                     generatePreviews(files) {
                         this.previews = [];
                         const timestamp = Date.now();
                         Array.from(files).forEach((file, index) => {
                             if (file.type.startsWith('image/')) {
                                 const reader = new FileReader();
                                 const uniqueId = `${timestamp}-${index}`;
                                 reader.onload = (e) => {
                                     this.previews.push({
                                         id: uniqueId,
                                         name: file.name,
                                         size: this.formatFileSize(file.size),
                                         url: e.target.result
                                     });
                                 };
                                 reader.readAsDataURL(file);
                             }
                         });
                     },
                     formatFileSize(bytes) {
                         if (bytes === 0) return '0 Bytes';
                         const k = 1024;
                         const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                         const i = Math.floor(Math.log(bytes) / Math.log(k));
                         return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                     },
                     clearPreviews() {
                         this.previews = [];
                     }
                 }"
                 @files-uploaded.window="clearPreviews()">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Archivos (Solo imágenes JPG, PNG, GIF - Máx. 10MB) *
                </label>
                <div class="flex items-center justify-center w-full"
                     @dragover.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="handleDrop($event)">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
                           :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600'">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg wire:loading.remove wire:target="files" class="w-8 h-8 mb-2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <div wire:loading wire:target="files" class="flex flex-col items-center">
                                <svg class="animate-spin w-8 h-8 mb-2 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Cargando archivos...</p>
                            </div>
                            <p wire:loading.remove wire:target="files" class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-semibold">Click para seleccionar</span> o arrastra archivos aquí
                            </p>
                        </div>
                        <input type="file" wire:model="files" multiple accept="image/jpeg,image/png,image/gif" class="hidden" x-ref="fileInput" @change="handleFileSelect($event)">
                    </label>
                </div>

                @if(!empty($files))
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ count($files) }} archivo(s) seleccionado(s)
                    </div>
                @endif

                <!-- Preview de archivos seleccionados -->
                <template x-if="previews.length > 0">
                    <div x-transition class="mt-4">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Vista previa:</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <template x-for="preview in previews" :key="preview.id">
                                <div class="relative group">
                                    <img :src="preview.url" :alt="preview.name" class="w-full h-24 object-cover rounded-lg border-2 border-gray-200 dark:border-gray-600">
                                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-75 text-white text-xs p-1 rounded-b-lg">
                                        <p class="truncate" x-text="preview.name"></p>
                                        <p class="text-gray-300" x-text="preview.size"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                @error('files.*')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nota opcional -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nota (opcional)
                </label>
                <textarea wire:model="fileNote"
                          rows="2"
                          placeholder="Agregue una nota descriptiva sobre estos archivos..."
                          class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                @error('fileNote')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botón de subida -->
            <div class="flex justify-end">
                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="uploadFiles,files"
                        @if(!$hasExternalStorage) disabled @endif
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg wire:loading.remove wire:target="uploadFiles" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <svg wire:loading wire:target="uploadFiles" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Subir Archivos
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de archivos subidos -->
    @if(!empty($uploadedMedia))
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Archivos Subidos ({{ count($uploadedMedia) }})
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($uploadedMedia as $media)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <!-- Preview de imagen -->
                        @if($media['content_type'] && str_starts_with($media['content_type'], 'image/'))
                            <div class="mb-3">
                                <img src="{{ $media['url'] ?? '#' }}"
                                     alt="{{ $media['file_name'] }}"
                                     class="w-full h-32 object-cover rounded">
                            </div>
                        @endif

                        <!-- Información del archivo -->
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $media['file_name'] }}">
                                {{ $media['file_name'] }}
                            </h4>

                            @if($media['modality'])
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $categories[$media['modality']] ?? $media['modality'] }}
                                </span>
                            @endif

                            @if($media['note'])
                                <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ $media['note'] }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $media['file_size_human'] ?? 'N/A' }}{{$media['created_at']}}</span>

                                <span>{{ \Carbon\Carbon::parse($media['created_at'])->diffForHumans() }}</span>
                            </div>

                            <!-- Acciones -->
                            <div class="flex gap-2 pt-2">
                                <a href="{{ $media['url'] ?? '#' }}"
                                   target="_blank"
                                   class="flex-1 text-center px-3 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 border border-blue-600 dark:border-blue-400 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                    Ver
                                </a>
                                <button wire:click="deleteFile({{ $media['id'] }})"
                                        wire:confirm="¿Está seguro de eliminar este archivo?"
                                        class="flex-1 px-3 py-1 text-xs font-medium text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 border border-red-600 dark:border-red-400 rounded hover:bg-red-50 dark:hover:bg-red-900/20">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
