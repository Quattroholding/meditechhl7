<div class="encounters-content">
    @if($sectionData && (isset($sectionData['data']) ? count($sectionData['data']) > 0 : count($sectionData) > 0))
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
                @foreach((isset($sectionData['data']) ? $sectionData['data'] : $sectionData) as $encounter)
                    <tr>
                        <td>
                            <strong>{{ Carbon\Carbon::parse($encounter->end)->format('d/m/Y') }}</strong>
                            <br><small>{{ Carbon\Carbon::parse($encounter->end)->format('H:i') }}</small>
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
                            @foreach($encounter->diagnoses as $diag)
                                {{$diag->condition->icd10Code ? $diag->condition->icd10Code->description_es :  $diag->condition->onset_info}}<br/>
                            @endforeach
                        </td>
                        <td>
                            {!!  ucfirst($encounter->status ?? 'Activo') !!}
                        </td>
                        {{--}}
                        <td>{{ $encounter->location->name ?? 'No especificada' }}</td>
                        {{--}}
                        <td>
                            @if(auth()->user()->can('view',$encounter))
                            <a  href="{{route('consultation.download_resumen',$encounter->appointment_id)}}" target="_blank" class="btn" style="background: #3b82f6; color: white; padding: 6px 12px; font-size: 12px;">
                                👁️ Ver Detalles
                            </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        @if(isset($sectionData['last_page']) && $sectionData['last_page'] > 1)
            <div style="margin-top: 30px; display: flex; justify-content: center;">
                <nav style="display: flex; align-items: center; gap: 10px;">
                    <!-- Previous Button -->
                    @if($sectionData['current_page'] > 1)
                        <button wire:click="previousEncountersPage"
                                class="pagination-btn"
                                style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
                            ← Anterior
                        </button>
                    @else
                        <span style="background: #e2e8f0; color: #9ca3af; padding: 8px 16px; border-radius: 8px; font-size: 14px;">
                            ← Anterior
                        </span>
                    @endif

                    <!-- Page Numbers -->
                    <div style="display: flex; align-items: center; gap: 5px;">
                        @foreach(range(1, $sectionData['last_page']) as $page)
                            @if($page == $sectionData['current_page'])
                                <span style="background: #3b82f6; color: white; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; min-width: 40px; text-align: center;">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoEncountersPage({{ $page }})"
                                        style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 14px; min-width: 40px; text-align: center; transition: all 0.3s ease;">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <!-- Next Button -->
                    @if($sectionData['current_page'] < $sectionData['last_page'])
                        <button wire:click="nextEncountersPage"
                                class="pagination-btn"
                                style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: background 0.3s ease;">
                            Siguiente →
                        </button>
                    @else
                        <span style="background: #e2e8f0; color: #9ca3af; padding: 8px 16px; border-radius: 8px; font-size: 14px;">
                            Siguiente →
                        </span>
                    @endif
                </nav>
            </div>

            <!-- Pagination Info -->
            <div style="margin-top: 15px; text-align: center; font-size: 13px; color: #64748b;">
                Mostrando consultas {{ $sectionData['from'] ?? 0 }} a {{ $sectionData['to'] ?? 0 }}
                de {{ $sectionData['total'] ?? 0 }} total
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">🏥</div>
            <h3>No hay consultas registradas</h3>
            <p>Este paciente no tiene consultas médicas en el período seleccionado.</p>
        </div>
    @endif
</div>
