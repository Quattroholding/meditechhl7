<div class="encounters-content">
    @if($sectionData && count($sectionData) > 0)
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Fecha</th>
                    {{--}}
                    <th>Tipo</th>
                    {{--}}
                    <th>Profesional</th>
                    <th>Diagnóstico</th>
                    <th>Estado</th>
                    {{--}}
                    <th>Ubicación</th>
                    {{--}}
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($sectionData as $encounter)
                    <tr>
                        <td>
                            <strong>{{ Carbon\Carbon::parse($encounter->created_at)->format('d/m/Y') }}</strong>
                            <br><small>{{ Carbon\Carbon::parse($encounter->created_at)->format('H:i') }}</small>
                        </td>
                        {{--}}
                        <td>
                                <span class="badge badge-{{ $encounter->type === 'emergency' ? 'pending' : 'active' }}">
                                    {{ ucfirst($encounter->type ?? 'Consulta') }}
                                </span>
                        </td>
                        {{--}}
                        <td>
                            <strong>{{ $encounter->practitioner->name ?? 'No asignado' }}</strong>
                            <br><small>{{ $encounter->medicalSpeciality->name ?? '' }}</small>
                        </td>
                        <td>
                            @foreach($encounter->diagnoses()->get() as $diag)
                                {{$diag->condition->icd10Code->description_es }}
                            @endforeach
                        </td>
                        <td>
                            {!!  ucfirst($encounter->status ?? 'Activo') !!}
                        </td>
                        {{--}}
                        <td>{{ $encounter->location->name ?? 'No especificada' }}</td>
                        {{--}}
                        <td>
                            <a  href="{{route('consultation.download_resumen',$encounter->appointment_id)}}" target="_blank" class="btn" style="background: #3b82f6; color: white; padding: 6px 12px; font-size: 12px;">
                                👁️ Ver Detalles
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{--}}
        <!-- Paginación -->
        <div style="margin-top: 20px;">
            {{ $sectionData->links() }}
        </div>
        {{--}}
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">🏥</div>
            <h3>No hay consultas registradas</h3>
            <p>Este paciente no tiene consultas médicas en el período seleccionado.</p>
        </div>
    @endif
</div>
