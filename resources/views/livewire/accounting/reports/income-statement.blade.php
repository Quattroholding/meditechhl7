<div>
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Estado de Resultados</h4>
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
                        <table class="table table-sm">
                            <tbody>
                                <!-- INGRESOS -->
                                <tr class="table-light fw-bold">
                                    <td colspan="2">INGRESOS</td>
                                </tr>
                                @forelse($report['income'] ?? [] as $income)
                                    <tr>
                                        <td class="ps-4">{{ $income['account_name'] }}</td>
                                        <td class="text-end">{{ number_format($income['balance'], 2, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="ps-4 text-muted">Sin ingresos</td>
                                    </tr>
                                @endforelse
                                <tr class="table-light fw-bold">
                                    <td class="ps-4">Total Ingresos</td>
                                    <td class="text-end">{{ number_format($report['income_total'] ?? 0, 2, '.', ',') }}</td>
                                </tr>

                                <!-- COSTOS -->
                                <tr class="table-light fw-bold mt-3">
                                    <td colspan="2">COSTOS</td>
                                </tr>
                                @forelse($report['costs'] ?? [] as $cost)
                                    <tr>
                                        <td class="ps-4">{{ $cost['account_name'] }}</td>
                                        <td class="text-end">{{ number_format($cost['balance'], 2, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="ps-4 text-muted">Sin costos</td>
                                    </tr>
                                @endforelse
                                <tr class="table-light fw-bold">
                                    <td class="ps-4">Total Costos</td>
                                    <td class="text-end">{{ number_format($report['costs_total'] ?? 0, 2, '.', ',') }}</td>
                                </tr>

                                <!-- UTILIDAD BRUTA -->
                                <tr class="fw-bold bg-info text-white">
                                    <td class="ps-4">Utilidad Bruta</td>
                                    <td class="text-end">{{ number_format($report['gross_profit'] ?? 0, 2, '.', ',') }}</td>
                                </tr>

                                <!-- GASTOS -->
                                <tr class="table-light fw-bold mt-3">
                                    <td colspan="2">GASTOS</td>
                                </tr>
                                @forelse($report['expenses'] ?? [] as $expense)
                                    <tr>
                                        <td class="ps-4">{{ $expense['account_name'] }}</td>
                                        <td class="text-end">{{ number_format($expense['balance'], 2, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="ps-4 text-muted">Sin gastos</td>
                                    </tr>
                                @endforelse
                                <tr class="table-light fw-bold">
                                    <td class="ps-4">Total Gastos</td>
                                    <td class="text-end">{{ number_format($report['expenses_total'] ?? 0, 2, '.', ',') }}</td>
                                </tr>

                                <!-- UTILIDAD NETA -->
                                @php
                                    $netIncomeValue = $report['net_income'] ?? 0;
                                    $netIncomeClass = $netIncomeValue >= 0 ? 'bg-success' : 'bg-danger';
                                @endphp
                                <tr class="fw-bold {{ $netIncomeClass }} text-white">
                                    <td class="ps-4">Utilidad Neta</td>
                                    <td class="text-end">{{ number_format($netIncomeValue, 2, '.', ',') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
