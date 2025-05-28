<div class="vital-signs-content">
    @if($sectionData && count($sectionData) > 0)
        {{--}}
        <div class="vital-signs-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
            @foreach($sectionData->take(9) as $vitalSign)
                <div class="vital-sign-card" style="background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%); border: 2px solid #e0f2fe; border-radius: 16px; padding: 20px;">
                    <div class="vital-header" style="display: flex; justify-content: between; align-items: center; margin-bottom: 15px;">
                        <div style="font-weight: 600; color: #0f172a;">
                            📅 {{ Carbon\Carbon::parse($vitalSign->effective_date)->format('d/m/Y H:i') }}
                        </div>
                        <div style="font-size: 12px; color: #64748b;">
                            <br/>{{ $vitalSign->encounter->encounter_type ?? 'Consulta' }}
                        </div>
                    </div>
                    <div class="vitals-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="vital-item">
                            <div style="font-size: 12px; color: #64748b; margin-bottom: 5px;">{{$vitalSign->observationType->name}}</div>
                            <div style="font-size: 18px; font-weight: 700; color: #dc2626;">
                                {{ $vitalSign->value }}
                            </div>
                            <div style="font-size: 10px; color: #64748b;">{{$vitalSign->observationType->default_unit}}</div>
                        </div>
                    </div>
                    @if($vitalSign->notes)
                        <div style="margin-top: 15px; padding: 10px; background: rgba(59, 130, 246, 0.1); border-radius: 8px; font-size: 12px; color: #1e40af;">
                            📝 {{ $vitalSign->notes }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        {{--}}
        <!-- Tabla completa -->
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Presión Arterial</th>
                    <th>Frecuencia Cardíaca</th>
                    <th>Temperatura</th>
                    <th>Saturación O2</th>
                    <th>Peso</th>
                    <th>Talla</th>
                </tr>
                </thead>
                <tbody>
                @foreach($sectionData->groupBy('encounter_id') as $e)
                    <tr>
                        <td>
                            <strong>{{ Carbon\Carbon::parse($e->first()->effective_date)->format('d/m/Y') }}</strong>
                            <br><small>{{ Carbon\Carbon::parse($e->first()->effective_date)->format('H:i') }}</small>
                        </td>
                        <td>
                            @if($e->where('code','8480-6')->first() && $e->where('code','8462-4')->first())
                                <span style="color: #dc2626; font-weight: 600;">
                                        {{ $e->where('code','8480-6')->first()->value }}/{{ $e->where('code','8462-4')->first()->value }}
                                    </span>
                                <br><small>mmHg</small>
                            @else
                                <span style="color: #9ca3af;">No registrado</span>
                            @endif
                        </td>
                        <td>
                            @if($e->where('code','8867-4')->first())
                                <span style="color: #dc2626; font-weight: 600;"> {{ $e->where('code','8867-4')->first()->value }}</span>
                                <br><small>bpm</small>
                            @else
                                <span style="color: #9ca3af;">No registrado</span>
                            @endif
                        </td>
                        <td>
                            @if($e->where('code','8310-5')->first())
                                <span style="color: #f59e0b; font-weight: 600;">{{ $e->where('code','8310-5')->first()->value }}°C</span>
                            @else
                                <span style="color: #9ca3af;">No registrado</span>
                            @endif
                        </td>
                        <td>
                            @if($e->where('code','2708-6')->first())
                                <span style="color: #059669; font-weight: 600;">{{ $e->where('code','2708-6')->first()->value }}%</span>
                            @else
                                <span style="color: #9ca3af;">No registrado</span>
                            @endif
                        </td>
                        <td>
                            @if($e->where('code','29463-7')->first())
                                <span style="font-weight: 600;">{{ $e->where('code','29463-7')->first()->value }} kg</span>
                            @else
                                <span style="color: #9ca3af;">No registrado</span>
                            @endif
                        </td>
                        <td>
                            @if($e->where('code','8302-2')->first())
                                <span style="font-weight: 600;">{{ $e->where('code','8302-2')->first()->value }} cm</span>
                            @else
                                <span style="color: #9ca3af;">No registrado</span>
                            @endif
                        </td>
                @endforeach
                </tbody>
            </table>
        </div>
        {{--}}
        <div style="margin-top: 20px;">
            {{ $sectionData->links() }}
        </div>
        {{--}}
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">❤️</div>
            <h3>No hay signos vitales registrados</h3>
            <p>Este paciente no tiene signos vitales en el período seleccionado.</p>
        </div>
    @endif
</div>
