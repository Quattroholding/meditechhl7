<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('patient.files.title') }}</h5>
        </div>
        <div class="card-body">


            <!-- Upload Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="mb-3">{{ __('patient.files.upload_new') }}</h6>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="input-block">
                                        <label for="archivos" class="form-label">{{ __('patient.files.select_files') }}</label>
                                        <input
                                            type="file"
                                            wire:model="archivos"
                                            multiple
                                            class="form-control @error('archivos.*') is-invalid @enderror"
                                            id="archivos"
                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                        >
                                        <small class="text-muted d-block mt-1">{{ __('patient.files.upload_help_short') }}</small>
                                        @error('archivos.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        <!-- Loading Indicator -->
                                        <div wire:loading wire:target="archivos" class="mt-2">
                                            <span class="text-primary">
                                                <i class="fas fa-spinner fa-spin"></i> {{ __('patient.files.loading') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-center">
                                    <button
                                        type="button"
                                        wire:click="uploadFiles"
                                        class="btn btn-primary w-100"
                                        wire:loading.attr="disabled"
                                        wire:target="uploadFiles"
                                        @if(empty($archivos)) disabled @endif
                                    >
                                        <span wire:loading.remove wire:target="uploadFiles">
                                            <i class="feather-upload me-2"></i>{{ __('patient.files.upload_button') }}
                                        </span>
                                        <span wire:loading wire:target="uploadFiles">
                                            <i class="fas fa-spinner fa-spin me-2"></i>{{ __('patient.files.uploading') }}
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <!-- Preview Selected Files -->
                            @if($archivos)
                                <div class="mt-3">
                                    <h6 class="mb-2">{{ __('patient.files.selected_files', ['count' => count($archivos)]) }}</h6>
                                    <ul class="list-group">
                                        @foreach($archivos as $index => $archivo)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="fas fa-file me-2 text-primary"></i>
                                                    {{ $archivo->getClientOriginalName() }}
                                                </span>
                                                <span class="badge bg-secondary">
                                                    {{ number_format($archivo->getSize() / 1024, 2) }} KB
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Files List -->
            <div class="row">
                <div class="col-12">
                    <h6 class="mb-3">{{ __('patient.files.saved_files', ['count' => $files->count()]) }}</h6>

                    @if($files->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">{{ __('patient.files.type') }}</th>
                                        <th>{{ __('patient.files.file_name') }}</th>
                                        <th style="width: 120px;">{{ __('patient.files.size') }}</th>
                                        <th style="width: 150px;">{{ __('patient.files.upload_date') }}</th>
                                        <th style="width: 150px;" class="text-center">{{ __('patient.files.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $file)
                                        <tr wire:key="file-{{ $file->id }}">
                                            <td class="text-center">
                                                @php
                                                    $extension = strtolower($file->extention ?? $file->type ?? 'file');
                                                    $iconClass = match($extension) {
                                                        'pdf' => 'fas fa-file-pdf text-danger',
                                                        'doc', 'docx' => 'fas fa-file-word text-primary',
                                                        'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                                        'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-info',
                                                        default => 'fas fa-file text-secondary'
                                                    };
                                                @endphp
                                                <i class="{{ $iconClass }} fa-2x"></i>
                                            </td>
                                            <td>
                                                <strong>{{ $file->name }}</strong>
                                                @if($file->type)
                                                    <br><small class="text-muted">{{ __('patient.files.type') }}: {{ ucfirst($file->type) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($file->path && Storage::disk('public')->exists($file->path))
                                                    {{ number_format(Storage::disk('public')->size($file->path) / 1024, 2) }} KB
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $file->created_at }}</small>
                                                <br>

                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a
                                                        href="{{ Storage::disk('public')->url($file->path) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="{{ __('patient.files.view_download') }}"
                                                    >
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <button
                                                        type="button"
                                                        wire:click="deleteFile({{ $file->id }})"
                                                        wire:confirm="{{ __('patient.files.confirm_delete') }}"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="{{ __('button.delete') }}"
                                                        wire:loading.attr="disabled"
                                                    >
                                                        <span wire:loading.remove wire:target="deleteFile({{ $file->id }})">
                                                            <i class="fa fa-trash"></i>
                                                        </span>
                                                        <span wire:loading wire:target="deleteFile({{ $file->id }})">
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        </span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="feather-info me-2"></i>
                            {{ __('patient.files.no_files') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
