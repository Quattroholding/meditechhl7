<div>
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Balance General</h4>
            <p class="text-muted">Al: {{ $as_of_date }}</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="input-block local-forms">
                <x-input-label for="as_of_date" :value="__('Fecha')" required="true" />
                <x-text-input wire:model.live="as_of_date" id="as_of_date" type="date" class="form-control" />
                <x-input-error :messages="$errors->get('as_of_date')" class="mt-2" />
            </div>
        </div>
    </div>

    @if(isset($report['error']))
        <div class="alert alert-danger">
            {{ $report['error'] }}
        </div>
    @else
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">ACTIVOS</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <thead>
                                <tr>
                                    <th>Cuenta</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report['assets']['details'] ?? [] as $asset)
                                    <tr>
                                        <td>{{ $asset['name'] }}</td>
                                        <td class="text-end">{{ number_format($asset['balance'], 2, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            Sin activos
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>TOTAL ACTIVOS</td>
                                    <td class="text-end">{{ number_format($report['assets']['total'] ?? 0, 2, '.', ',') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">PASIVOS Y PATRIMONIO</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mt-3 mb-2">Pasivos</h6>
                        <table class="table table-sm table-borderless">
                            <tbody>
                                @forelse($report['liabilities']['details'] ?? [] as $liability)
                                    <tr>
                                        <td>{{ $liability['name'] }}</td>
                                        <td class="text-end">{{ number_format($liability['balance'], 2, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted text-sm">
                                            Sin pasivos
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>TOTAL PASIVOS</td>
                                    <td class="text-end">{{ number_format($report['liabilities']['total'] ?? 0, 2, '.', ',') }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        <h6 class="mt-4 mb-2">Patrimonio</h6>
                        <table class="table table-sm table-borderless">
                            <tbody>
                                @forelse($report['equity']['details'] ?? [] as $item)
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td class="text-end">{{ number_format($item['balance'], 2, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted text-sm">
                                            Sin patrimonio
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>TOTAL PATRIMONIO</td>
                                    <td class="text-end">{{ number_format($report['equity']['total'] ?? 0, 2, '.', ',') }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        <hr>
                        <table class="table table-sm table-borderless">
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>TOTAL PASIVO + PATRIMONIO</td>
                                    <td class="text-end">{{ number_format(($report['liabilities']['total'] ?? 0) + ($report['equity']['total'] ?? 0), 2, '.', ',') }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        @php
                            $difference = ($report['assets']['total'] ?? 0) - (($report['liabilities']['total'] ?? 0) + ($report['equity']['total'] ?? 0));
                        @endphp
                        @if(abs($difference) < 0.01)
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle"></i> Balance ecuacionado
                            </div>
                        @else
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-exclamation-circle"></i> Diferencia: {{ number_format($difference, 2, '.', ',') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
