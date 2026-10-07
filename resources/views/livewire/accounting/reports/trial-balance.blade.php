<div>
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Balance de Comprobación</h4>
            <p class="text-muted">Período: {{ $start_date }} a {{ $end_date }}</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="input-block local-forms">
                <x-input-label for="start_date" :value="__('Fecha de Inicio')" required="true" />
                <x-text-input wire:model.live="start_date" id="start_date" type="date" class="form-control" />
                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-block local-forms">
                <x-input-label for="end_date" :value="__('Fecha de Fin')" required="true" />
                <x-text-input wire:model.live="end_date" id="end_date" type="date" class="form-control" />
                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
            </div>
        </div>
    </div>

    @if(isset($report['error']))
        <div class="alert alert-danger">
            {{ $report['error'] }}
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Código</th>
                                        <th>Cuenta</th>
                                        <th class="text-end">Débito</th>
                                        <th class="text-end">Crédito</th>
                                        <th class="text-end">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($report['details'] ?? [] as $detail)
                                        <tr>
                                            <td>{{ $detail['account_code'] }}</td>
                                            <td>{{ $detail['account_name'] }}</td>
                                            <td class="text-end">{{ number_format($detail['debit'], 2, '.', ',') }}</td>
                                            <td class="text-end">{{ number_format($detail['credit'], 2, '.', ',') }}</td>
                                            <td class="text-end">
                                                <strong>{{ number_format($detail['balance'], 2, '.', ',') }}</strong>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                No hay movimientos en este período.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light">
                                    <tr class="fw-bold">
                                        <td colspan="2" class="text-end">Totales:</td>
                                        <td class="text-end">
                                            {{ number_format($report['total_debit'] ?? 0, 2, '.', ',') }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($report['total_credit'] ?? 0, 2, '.', ',') }}
                                        </td>
                                        <td class="text-end">
                                            <span class="badge {{ ($report['is_balanced'] ?? false) ? 'bg-success' : 'bg-danger' }}">
                                                {{ ($report['is_balanced'] ?? false) ? '✓ Balanceado' : '✗ Desbalanceado' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td colspan="4" class="text-end">Diferencia:</td>
                                        <td class="text-end">
                                            {{ number_format($report['difference'] ?? 0, 2, '.', ',') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
