<div wire:poll.2s="checkApprovalStatus">
    <div class="grid grid-cols-12 gap-6 mb-6">
        <!-- PDF Preview -->
        <div class="col-span-12 lg:col-span-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Vista Previa del Documento</h5>
                </div>
                <div class="card-body p-0">
                    @if ($pdfUrl)
                        <embed src="{{ $pdfUrl }}" type="application/pdf" class="w-full" style="height: 600px;" />
                    @else
                        <div class="h-96 flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 16.5a1 1 0 11-2 0 1 1 0 012 0zM15 7a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p>Documento no disponible</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Document Info -->
        <div class="col-span-12 lg:col-span-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información del Documento</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-sm text-gray-600">Nombre</label>
                        <p class="font-semibold">{{ $document->original_filename }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-sm text-gray-600">Tipo</label>
                        <p class="font-semibold">
                            <span class="badge bg-{{ in_array($document->document_type->value, ['inventory', 'inventory_ai']) ? 'primary' : 'secondary' }}">
                                {{ $document->document_type->label() }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-sm text-gray-600">Estado</label>
                        <p class="font-semibold">
                            <span class="badge bg-{{ match($document->status->value) {
                                'pending' => 'warning',
                                'parsed' => 'info',
                                'approved' => 'success',
                                'processed' => 'success',
                                'rejected' => 'danger',
                                default => 'secondary'
                            } }}">
                                {{ $document->status->label() }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-sm text-gray-600">Subido el</label>
                        <p class="font-semibold">{{ $document->created_at }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Confianza de Parsing</label>
                        <div class="flex items-center gap-3 mt-2">
                            <div class="flex-1 h-2 bg-gray-200 rounded-full">
                                <div
                                    class="h-2 rounded-full"
                                    style="width: {{ $confidenceScore * 100 }}%; background-color: {{ $confidenceScore >= 0.8 ? '#10b981' : ($confidenceScore >= 0.6 ? '#f59e0b' : '#ef4444') }};"
                                ></div>
                            </div>
                            <span class="text-sm font-medium">{{ round($confidenceScore * 100) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($validationMessages)
            <div class="card border-l-4 border-yellow-400">
                <div class="card-header bg-yellow-50">
                    <h5 class="card-title mb-0 text-yellow-900">⚠️ Advertencias</h5>
                </div>
                <div class="card-body text-sm text-yellow-700">
                    <ul class="space-y-1">
                        @foreach ($validationMessages as $message)
                            <li>• {{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Document Detail - Include based on Type -->
    @if (in_array($document->document_type->value, ['inventory', 'inventory_ai']))
        @include('livewire.documents.document-detail-inventory')
    @else
        <!-- IA-Processed Document -->
        @include('livewire.documents.document-detail-ia')
    @endif

    <!-- Totals (Inventory Documents) -->
    @if (!in_array($document->document_type->value, ['inventory', 'inventory_ai']))
    <!-- Notes Section (Utility Bills) -->
    <div class="card mb-6">
        <div class="card-header">
            <h5 class="card-title mb-0">Notas (Opcional)</h5>
        </div>
        <div class="card-body">
            <textarea
                wire:model="notes"
                placeholder="Añade notas sobre este documento..."
                rows="4"
                class="form-control"
                @disabled($isApproving)
            ></textarea>
        </div>
    </div>
    @endif

    <!-- Rejection Form -->
    @if ($isRejecting)
    <div class="card border-l-4 border-danger mb-6">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">Rechazar Documento</h5>
        </div>
        <div class="card-body">
            <textarea
                wire:model="rejectionReason"
                placeholder="Explica por qué rechazas este documento (mínimo 10 caracteres)..."
                rows="4"
                class="form-control @error('rejectionReason') is-invalid @enderror"
            ></textarea>
            @error('rejectionReason')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="d-flex gap-2 justify-content-end align-items-center">
        <a href="{{ route('documents.index') }}" class="btn btn-secondary" @if($isApproving) onclick="return false;" @endif>
            <i class="feather icon-x"></i> Cancelar
        </a>

        @if (in_array($document->status->value, ['processed', 'rejected', 'approved', 'processing']))
            <div class="alert alert-info mb-0">
                <i class="feather icon-info"></i>
                @if ($document->status->value === 'processed')
                    Documento ya procesado
                @elseif ($document->status->value === 'approved')
                    Documento aprobado - en procesamiento
                @elseif ($document->status->value === 'processing')
                    Documento en procesamiento
                @elseif ($document->status->value === 'rejected')
                    Documento rechazado
                @endif
            </div>
        @elseif ($isRejecting)
            <button wire:click="cancelRejection" class="btn btn-warning" @disabled($isApproving)>
                <i class="feather icon-arrow-left"></i> Volver
            </button>
            <button wire:click="confirmReject" class="btn btn-danger" @disabled($isApproving)>
                <i class="feather icon-trash-2"></i> Confirmar Rechazo
            </button>
        @else
            <button wire:click="startRejecting" class="btn btn-danger">
                <i class="feather icon-x-circle"></i> Rechazar
            </button>
            <button wire:click="approve" class="btn btn-primary" @disabled($isApproving) wire:loading.attr="disabled">
                @if ($isApproving)
                    <span wire:loading.remove>
                        <i class="feather icon-check-circle"></i> Aprobar
                    </span>
                    <span wire:loading>
                        <i class="spinner-border spinner-border-sm me-2" role="status"></i> Procesando...
                    </span>
                @else
                    <i class="feather icon-check-circle"></i> Aprobar
                @endif
            </button>
        @endif
    </div>
    <p>&nbsp;</p>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (event) => {
                console.log('Toastr event received:', event);
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
        });
    </script>
</div>
