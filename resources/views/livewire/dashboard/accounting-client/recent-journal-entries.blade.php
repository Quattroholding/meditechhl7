<div class="card flex-fill">
    <div class="card-header">
        <h5 class="card-title">Últimos Asientos Contables</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="d-flex justify-content-between">
                    <span>Asientos Contabilizados</span>
                    <span class="badge bg-success">{{ $totalPosted }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between">
                    <span>Asientos en Borrador</span>
                    <span class="badge bg-warning">{{ $totalDraft }}</span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        <tr>
                            <td><small>{{ $entry['number'] }}</small></td>
                            <td><small>{{ Str::limit($entry['description'], 30) }}</small></td>
                            <td><small>{{ $entry['date'] }}</small></td>
                            <td>
                                @if($entry['status'] === 'posted')
                                    <span class="badge bg-success">Contabilizado</span>
                                @else
                                    <span class="badge bg-warning">Borrador</span>
                                @endif
                            </td>
                            <td><small class="fw-bold">${{ number_format($entry['total'], 2) }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay asientos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>