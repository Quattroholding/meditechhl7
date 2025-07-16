<div>
   @include('partials.message')

    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" wire:model.live="search" class="form-control" placeholder="Buscar encuestas...">
        </div>
        <div class="col-md-3">
            <select wire:model.live="statusFilter" class="form-control">
                <option value="">Todos los estados</option>
                <option value="draft">Borrador</option>
                <option value="active">Activo</option>
                <option value="inactive">Inactivo</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Activa</th>
                    <th>Preguntas</th>
                    <th>Respuestas</th>
                    <th>Creada por</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surveys as $survey)
                    <tr>
                        <td>
                            <strong>{{ $survey->title }}</strong>
                            @if($survey->description)
                                <br><small class="text-muted">{{ Str::limit($survey->description, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $survey->status == 'active' ? 'success' : ($survey->status == 'draft' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($survey->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $survey->is_active ? 'success' : 'danger' }}">
                                {{ $survey->is_active ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td>{{ $survey->questions->count() }}</td>
                        <td>{{ $survey->responses->where('status', 'completed')->count() }}</td>
                        <td>{{ $survey->creator->full_name ?? 'N/A' }}</td>
                        <td>{{ $survey->created_at }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('surveys.show', $survey->id) }}" class="btn btn-info btn-sm">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('surveys.edit', $survey->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button wire:click="delete({{ $survey->id }})"
                                        onclick="return confirm('¿Está seguro de eliminar esta encuesta?')"
                                        class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No se encontraron encuestas</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $surveys->links() }}
    </div>
</div>
