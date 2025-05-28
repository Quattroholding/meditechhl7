<div class="medical-requests-content">
    @if($sectionData && count($sectionData) > 0)
        <div class="requests-grid" style="display: grid; gap: 20px;">
            @foreach($sectionData['types'] as $type=>$value)
                @foreach($value as $request)
                    <div class="request-card" style="background: white; border: 2px solid #f1f5f9; border-radius: 16px; padding: 25px; transition: all 0.3s ease;">
                        <div class="request-header" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                            <div>
                                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                                    @if($type=='medications')
                                        💊 Prescripción de Medicamento
                                    @elseif($type=='services')
                                        @switch($request->cpt->type)
                                            @case('laboratory')
                                                🧪 Examen de Laboratorio
                                                @break
                                            @case('images')
                                                📸 Estudio de Imagen
                                                @break
                                            @case('procedure')
                                                🩺 Procedimiento
                                                @break
                                            @default
                                                📋 Orden Médica
                                        @endswitch
                                    @elseif($type=='referrals')
                                        👨‍⚕️ Referencia Médica
                                    @elseif($type=='procedures')
                                        🏥 Procedimiento Médico
                                    @endif
                                </h3>
                                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <span class="badge badge-{{ $request->status === 'completed' ? 'resolved' : ($request->status === 'cancelled' ? 'inactive' : ($request->status === 'in-progress' ? 'active' : 'pending')) }}">
                                    @switch($request->status)
                                        @case('pending')
                                            ⏳ Pendiente
                                            @break
                                        @case('approved')
                                            ✅ Aprobada
                                            @break
                                        @case('in-progress')
                                            🔄 En Proceso
                                            @break
                                        @case('completed')
                                            ✔️ Completada
                                            @break
                                        @case('cancelled')
                                            ❌ Cancelada
                                            @break
                                        @case('rejected')
                                            ⛔ Rechazada
                                            @break
                                        @default
                                            {{ ucfirst($request->status) }}
                                    @endswitch
                                </span>
                                    @if($request->priority)
                                        <span class="badge" style="background:
                                        @if($request->priority === 'urgent') #fee2e2; color: #dc2626;
                                        @elseif($request->priority === 'high') #fef3c7; color: #92400e;
                                        @elseif($request->priority === 'stat') #f3e8ff; color: #7c3aed;
                                        @else #f0fdf4; color: #166534; @endif">
                                        @switch($request->priority)
                                                @case('urgent')
                                                    🚨 Urgente
                                                    @break
                                                @case('high')
                                                    ⚡ Alta
                                                    @break
                                                @case('stat')
                                                    ⚡⚡ STAT
                                                    @break
                                                @case('routine')
                                                    📅 Rutina
                                                    @break
                                                @default
                                                    {{ ucfirst($request->priority) }}
                                            @endswitch
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div style="text-align: right; font-size: 12px; color: #64748b;">
                                <div><strong>Solicitado:</strong> {{ Carbon\Carbon::parse($request->created_at)->format('d/m/Y H:i') }}</div>
                                @if($request->practitioner)
                                    <div><strong>Por:</strong> {{ $request->practitioner->name }}</div>
                                    @if($request->encounter->medicalSpeciality)
                                        <div><strong>Especialidad:</strong> {{ $request->encounter->medicalSpeciality->name }}</div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Detalles de la Orden -->
                        <div class="request-details" style="background: #f8fafc; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 10px;">
                                📝 Detalles de la Orden
                            </div>
                            <div style="color: #1e293b; line-height: 1.6;">
                                {{ $request->dosage ?? $request->request_details }}
                            </div>
                        </div>

                        <!-- Información Específica por Tipo de Orden -->
                        @if($type=='services' && $request->cpt->type == 'laboratory')
                            <div class="lab-details" style="margin-bottom: 15px;">
                                <div style="font-size: 14px; font-weight: 600; color: #0891b2; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                    📋 Laboratorio Solicitado
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">

                                    @if($request->cpt)
                                        <div>
                                            <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                                {{$request->cpt->description_es}}
                                            </div>
                                        </div>
                                    @endif
                                    @if($request->sample_type)
                                        <div>
                                            <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">🩸 Tipo de Muestra</div>
                                            <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px;">
                                                {{ $request->sample_type }}
                                            </div>
                                        </div>
                                    @endif
                                    @if($request->fasting_required)
                                        <div>
                                            <div style="font-size: 12px; color: #f59e0b; font-weight: 600; margin-bottom: 5px;">⚠️ Preparación</div>
                                            <div style="background: #fffbeb; padding: 10px; border-radius: 8px; font-size: 13px;">
                                                Ayuno de {{ $request->fasting_hours ?? 12 }} horas
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($type=='services' && $request->cpt->type =='images')
                            <div class="imaging-details" style="margin-bottom: 15px;">
                                <div style="font-size: 14px; font-weight: 600; color: #059669; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                    📸 Estudio Solicitado
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                    @if($request->cpt)
                                        <div>
                                            <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px;">
                                                {{ $request->cpt->description_es }}
                                            </div>
                                        </div>
                                    @endif
                                    @if($request->body_region)
                                        <div>
                                            <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px;">📍 Región Corporal</div>
                                            <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                                {{ $request->body_region }}
                                            </div>
                                        </div>
                                    @endif
                                    @if($request->contrast_required)
                                        <div>
                                            <div style="font-size: 12px; color: #ea580c; font-weight: 600; margin-bottom: 5px;">💉 Contraste</div>
                                            <div style="background: #fff7ed; padding: 10px; border-radius: 8px; font-size: 13px;">
                                                {{ $request->contrast_type ?? 'Requerido' }}
                                            </div>
                                        </div>
                                    @endif
                                    @if($request->preparation_instructions)
                                        <div style="grid-column: 1 / -1;">
                                            <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">📋 Preparación</div>
                                            <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px;">
                                                {{ $request->preparation_instructions }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($type=='medications')
                            <div class="medication-details" style="background: #f0fdf4; padding: 15px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #059669;">
                                <div style="font-size: 14px; font-weight: 600; color: #065f46; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                    💊 Prescripción de Medicamento
                                </div>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                    @if($request->medication_name)
                                        <div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">💊 Medicamento</div>
                                            <div style="font-weight: 600; color: #1e293b;">{{ $request->medicine->full_name }}</div>
                                        </div>
                                    @endif
                                    @if($request->dosage)
                                        <div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">📏 Dosis</div>
                                            <div>{{ $request->dosage_text }}</div>
                                        </div>
                                    @endif
                                    @if($request->frequency)
                                        <div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">⏰ Frecuencia</div>
                                            <div>{{ $request->frequency }}</div>
                                        </div>
                                    @endif
                                    @if($request->duration)
                                        <div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">📅 Duración</div>
                                            <div>{{ $request->duration }}</div>
                                        </div>
                                    @endif
                                    @if($request->route)
                                        <div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">🎯 Vía</div>
                                            <div>{{ $request->route }}</div>
                                        </div>
                                    @endif
                                    @if($request->dosage_text)
                                        <div style="grid-column: 1 / -1;">
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">📋 Instrucciones</div>
                                            <div style="margin-top: 5px; font-style: italic;">{{ $request->dosage_text }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Justificación Clínica -->
                        @if($request->clinical_justification)
                            <div style="margin-bottom: 15px;">
                                <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                    🎯 Justificación Clínica
                                </div>
                                <div style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); padding: 15px; border-radius: 10px; color: #1e40af; border-left: 4px solid #3b82f6;">
                                    {{ $request->clinical_justification }}
                                </div>
                            </div>
                        @endif

                        <!-- Timeline de Estados -->
                        <div class="request-timeline" style="background: #f8fafc; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 10px;">
                                ⏱️ Timeline de la Orden
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                @if($request->created_at)
                                    <div>
                                        <div style="font-size: 12px; color: #3b82f6; font-weight: 600;">📅 Programado</div>
                                        <div style="font-size: 13px; color: #1e293b;">{{ Carbon\Carbon::parse($request->created_at)->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif
                                @if($request->started_date)
                                    <div>
                                        <div style="font-size: 12px; color: #f59e0b; font-weight: 600;">▶️ Iniciado</div>
                                        <div style="font-size: 13px; color: #1e293b;">{{ Carbon\Carbon::parse($request->started_date)->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif
                                @if($request->completed_date)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600;">✅ Completado</div>
                                        <div style="font-size: 13px; color: #1e293b;">{{ Carbon\Carbon::parse($request->completed_date)->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif
                                @if($request->results_available_date)
                                    <div>
                                        <div style="font-size: 12px; color: #7c3aed; font-weight: 600;">📊 Resultados</div>
                                        <div style="font-size: 13px; color: #1e293b;">{{ Carbon\Carbon::parse($request->results_available_date)->format('d/m/Y') }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Resultados -->
                        @if($request->results)
                            <div style="margin-bottom: 15px;">
                                <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                    📊 Resultados
                                </div>
                                <div style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); padding: 15px; border-radius: 10px; color: #065f46; border-left: 4px solid #059669;">
                                    {{ $request->results }}
                                </div>
                            </div>
                        @endif

                        <!-- Footer -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b;">
                            <div>
                                @if($request->encounter)
                                    <span>📅 Consulta: {{ Carbon\Carbon::parse($request->encounter->encounter_date)->format('d/m/Y') }}</span>
                                @endif
                            </div>
                            <div>
                                @if($request->reference_number)
                                    <span>🔖 Ref: {{ $request->reference_number }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
        {{--}}
        <div style="margin-top: 20px;">
            {{ $sectionData->links() }}
        </div>
        {{--}}
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">📋</div>
            <h3>No hay órdenes médicas registradas</h3>
            <p>Este paciente no tiene órdenes médicas en el período seleccionado.</p>
        </div>
    @endif
</div>
