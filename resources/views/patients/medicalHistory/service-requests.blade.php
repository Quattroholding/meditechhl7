<div class="service-requests-content">
    @if($sectionData && count($sectionData) > 0)
        <div class="service-requests-grid" style="display: grid; gap: 20px;">
            @foreach($sectionData as $serviceRequest)
                <div class="service-request-card" style="background: white; border: 2px solid #f1f5f9; border-radius: 16px; padding: 25px; transition: all 0.3s ease;">
                    <div class="service-header" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                                @switch($serviceRequest->service_type)
                                    @case('rehabilitation')
                                        🏃‍♂️ {{ __('patient.medical_history.service_requests.rehabilitation') }}
                                        @break
                                    @case('nursing')
                                        👩‍⚕️ {{ __('patient.medical_history.service_requests.nursing') }}
                                        @break
                                    @case('nutrition')
                                        🥗 {{ __('patient.medical_history.service_requests.nutrition') }}
                                        @break
                                    @case('psychology')
                                        🧠 {{ __('patient.medical_history.service_requests.psychology') }}
                                        @break
                                    @case('social_work')
                                        🤝 {{ __('patient.medical_history.service_requests.social_work') }}
                                        @break
                                    @case('physiotherapy')
                                        💪 {{ __('patient.medical_history.service_requests.physiotherapy') }}
                                        @break
                                    @case('occupational_therapy')
                                        🎯 {{ __('patient.medical_history.service_requests.occupational_therapy') }}
                                        @break
                                    @case('speech_therapy')
                                        🗣️ {{ __('patient.medical_history.service_requests.speech_therapy') }}
                                        @break
                                    @default
                                        🧪 {{ __('patient.medical_history.service_requests.service_specialized') }}
                                @endswitch
                            </h3>
                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <span class="badge badge-{{ $serviceRequest->status === 'completed' ? 'resolved' : ($serviceRequest->status === 'cancelled' ? 'inactive' : ($serviceRequest->status === 'in-progress' ? 'active' : 'pending')) }}">
                                    @switch($serviceRequest->status)
                                        @case('requested')
                                            📝 {{ __('patient.medical_history.service_requests.requested') }}
                                            @break
                                        @case('scheduled')
                                            📅 {{ __('patient.medical_history.service_requests.scheduled') }}
                                            @break
                                        @case('in-progress')
                                            🔄 {{ __('patient.medical_history.service_requests.in_progress') }}
                                            @break
                                        @case('completed')
                                            ✅ {{ __('patient.medical_history.service_requests.completed') }}
                                            @break
                                        @case('cancelled')
                                            ❌ {{ __('patient.medical_history.service_requests.cancelled') }}
                                            @break
                                        @case('on-hold')
                                            ⏸️ {{ __('patient.medical_history.service_requests.on_hold') }}
                                            @break
                                        @default
                                            {{ ucfirst($serviceRequest->status) }}
                                    @endswitch
                                </span>
                                @if($serviceRequest->urgency)
                                    <span class="badge" style="background:
                                        @if($serviceRequest->urgency === 'urgent') #fee2e2; color: #dc2626;
                                        @elseif($serviceRequest->urgency === 'high') #fef3c7; color: #92400e;
                                        @else #f0fdf4; color: #166534; @endif">
                                        @switch($serviceRequest->urgency)
                                            @case('urgent')
                                                🚨 {{ __('patient.medical_history.service_requests.urgent') }}
                                                @break
                                            @case('high')
                                                ⚡ {{ __('patient.medical_history.service_requests.high_urgency') }}
                                                @break
                                            @case('routine')
                                                📅 {{ __('patient.medical_history.service_requests.routine_urgency') }}
                                                @break
                                            @default
                                                {{ ucfirst($serviceRequest->urgency) }}
                                        @endswitch
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div style="text-align: right; font-size: 12px; color: #64748b;">
                            <div><strong>{{ __('patient.medical_history.service_requests.requested_date') }}:</strong> {{ Carbon\Carbon::parse($serviceRequest->request_date)->format('d/m/Y H:i') }}</div>
                            @if($serviceRequest->practitioner)
                                <div><strong>{{ __('patient.medical_history.service_requests.requested_by') }}:</strong> {{ $serviceRequest->practitioner->name }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Razón del Servicio -->
                    <div class="service-reason" style="background: #f8fafc; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                        <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 10px;">
                            📝 {{ __('patient.medical_history.service_requests.reason_service') }}
                        </div>
                        <div style="color: #1e293b; line-height: 1.6;">
                            {{ $serviceRequest->reason ?? $serviceRequest->description }}
                        </div>
                    </div>

                    <!-- Resultados de Laboratorio (Observaciones) -->
                    @if($serviceRequest->observations()->count() > 0)
                        <div class="lab-results" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 2px solid #3b82f6;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                                <div style="font-size: 16px; font-weight: 700; color: #1e40af;">
                                    🧪 {{ __('patient.medical_history.service_requests.laboratory_results') }}
                                </div>
                                <span class="badge" style="background: #dbeafe; color: #1e40af; font-size: 12px; padding: 4px 10px;">
                                    {{ $serviceRequest->observations->count() }} {{ __('patient.medical_history.service_requests.results_count') }}
                                </span>
                            </div>

                            @if(in_array($serviceRequest->code, ['85025', '85027']))
                                <!-- Código HemoScreen para CBC -->
                                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 12px 16px; border-radius: 8px; margin-bottom: 15px; display: inline-flex; align-items: center; gap: 10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="white" viewBox="0 0 16 16">
                                        <path d="M8.06 6.5a.5.5 0 0 1 .5.5v.776a11.5 11.5 0 0 1-.552 3.519l-1.331 4.14a.5.5 0 0 1-.952-.305l1.33-4.141a10.5 10.5 0 0 0 .504-3.213V7a.5.5 0 0 1 .5-.5Z"/>
                                        <path d="M6.06 7a2 2 0 1 1 4 0 .5.5 0 1 1-1 0 1 1 0 1 0-2 0v.332q0 .613-.066 1.221A.5.5 0 0 1 6 8.447q.06-.555.06-1.115zm3.509 1a.5.5 0 0 1 .5.5v.67q0 .613-.066 1.221a.5.5 0 1 1-.994-.112q.06-.555.06-1.109V8.5a.5.5 0 0 1 .5-.5"/>
                                    </svg>
                                    <div>
                                        <div style="color: white; font-size: 11px; opacity: 0.9;">{{ __('patient.medical_history.service_requests.code_hemoscreen') }}</div>
                                        <div style="color: white; font-size: 16px; font-weight: 700; letter-spacing: 2px; font-family: monospace;">
                                            {{ $serviceRequest->hemo_identification ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                                @foreach($serviceRequest->observations as $observation)
                                    <div style="background: white; padding: 12px; border-radius: 8px; border: 1px solid #bfdbfe;">
                                        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 8px;">
                                            <div style="font-size: 11px; color: #6b7280; font-weight: 600; letter-spacing: 0.5px;">
                                                {{ \App\Enums\LoincCode::getShortLabel($observation->code) }}
                                            </div>
                                            @if($observation->status === 'final')
                                                <span style="font-size: 10px; background: #d1fae5; color: #065f46; padding: 2px 6px; border-radius: 4px; margin-left: auto;">
                                                    ✓ {{ __('patient.medical_history.service_requests.final_status') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">
                                            {{ $observation->value }}
                                            <span style="font-size: 14px; color: #64748b; font-weight: 500;">{{ $observation->unit }}</span>
                                        </div>
                                        @if($observation->interpretation)
                                            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                                                {{ $observation->interpretation }}
                                            </div>
                                        @endif
                                        <div style="font-size: 10px; color: #94a3b8; margin-top: 6px;">
                                            📅 {{ Carbon\Carbon::parse($observation->effective_date)->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($serviceRequest->observations->first() && $serviceRequest->observations->first()->issued_date)
                                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #bfdbfe; font-size: 11px; color: #64748b;">
                                    <strong>{{ __('patient.medical_history.service_requests.results_issued') }}:</strong> {{ Carbon\Carbon::parse($serviceRequest->observations->first()->issued_date)->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Detalles Específicos del Servicio -->
                    <div class="service-details" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                        @if($serviceRequest->sessions_requested)
                            <div>
                                <div style="font-size: 12px; color: #3b82f6; font-weight: 600; margin-bottom: 5px;">🔢 {{ __('patient.medical_history.service_requests.sessions_requested') }}</div>
                                <div style="background: #dbeafe; padding: 10px; border-radius: 8px; font-size: 13px; color: #1e40af;">
                                    {{ $serviceRequest->sessions_requested }} {{ __('patient.medical_history.service_requests.sessions') }}
                                </div>
                            </div>
                        @endif

                        @if($serviceRequest->frequency)
                            <div>
                                <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">📅 {{ __('patient.medical_history.service_requests.frequency') }}</div>
                                <div style="background: #d1fae5; padding: 10px; border-radius: 8px; font-size: 13px; color: #065f46;">
                                    {{ $serviceRequest->frequency }}
                                </div>
                            </div>
                        @endif

                        @if($serviceRequest->duration_per_session)
                            <div>
                                <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px;">⏱️ {{ __('patient.medical_history.service_requests.duration_per_session') }}</div>
                                <div style="background: #ede9fe; padding: 10px; border-radius: 8px; font-size: 13px; color: #5b21b6;">
                                    {{ $serviceRequest->duration_per_session }} {{ __('patient.medical_history.service_requests.minutes') }}
                                </div>
                            </div>
                        @endif

                        @if($serviceRequest->location_preference)
                            <div>
                                <div style="font-size: 12px; color: #ea580c; font-weight: 600; margin-bottom: 5px;">📍 {{ __('patient.medical_history.service_requests.location_preference') }}</div>
                                <div style="background: #fed7aa; padding: 10px; border-radius: 8px; font-size: 13px; color: #9a3412;">
                                    {{ $serviceRequest->location_preference }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Objetivos del Tratamiento -->
                    @if($serviceRequest->treatment_goals)
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                🎯 {{ __('patient.medical_history.service_requests.treatment_goals') }}
                            </div>
                            <div style="background: linear-gradient(135deg, #fef3c7, #fcd34d); padding: 15px; border-radius: 10px; color: #92400e;">
                                {{ $serviceRequest->treatment_goals }}
                            </div>
                        </div>
                    @endif

                    <!-- Información de Programación -->
                    @if($serviceRequest->scheduled_date || $serviceRequest->assigned_provider)
                        <div class="scheduling-info" style="background: #f0f9ff; padding: 15px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #3b82f6;">
                            <div style="font-size: 14px; font-weight: 600; color: #1e40af; margin-bottom: 10px;">
                                📅 {{ __('patient.medical_history.service_requests.scheduling_info') }}
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                                @if($serviceRequest->scheduled_date)
                                    <div>
                                        <div style="font-size: 12px; color: #1e40af; font-weight: 600;">{{ __('patient.medical_history.service_requests.scheduled_date') }}</div>
                                        <div style="color: #1e293b;">{{ Carbon\Carbon::parse($serviceRequest->scheduled_date)->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif
                                @if($serviceRequest->assigned_provider)
                                    <div>
                                        <div style="font-size: 12px; color: #1e40af; font-weight: 600;">{{ __('patient.medical_history.service_requests.assigned_provider') }}</div>
                                        <div style="color: #1e293b;">{{ $serviceRequest->assigned_provider }}</div>
                                    </div>
                                @endif
                                @if($serviceRequest->estimated_duration)
                                    <div>
                                        <div style="font-size: 12px; color: #1e40af; font-weight: 600;">{{ __('patient.medical_history.service_requests.estimated_duration') }}</div>
                                        <div style="color: #1e293b;">{{ $serviceRequest->estimated_duration }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Progreso del Tratamiento -->
                    @if($serviceRequest->sessions_completed || $serviceRequest->progress_notes)
                        <div class="treatment-progress" style="background: #f0fdf4; padding: 15px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #059669;">
                            <div style="font-size: 14px; font-weight: 600; color: #065f46; margin-bottom: 10px;">
                                📊 {{ __('patient.medical_history.service_requests.treatment_progress') }}
                            </div>
                            @if($serviceRequest->sessions_completed && $serviceRequest->sessions_requested)
                                <div style="margin-bottom: 10px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                        <span style="font-size: 12px; color: #065f46; font-weight: 600;">{{ __('patient.medical_history.service_requests.sessions_completed') }}</span>
                                        <span style="font-size: 12px; color: #065f46;">{{ $serviceRequest->sessions_completed }}/{{ $serviceRequest->sessions_requested }}</span>
                                    </div>
                                    <div style="background: #dcfce7; height: 8px; border-radius: 4px; overflow: hidden;">
                                        <div style="background: #16a34a; height: 100%; width: {{ ($serviceRequest->sessions_completed / $serviceRequest->sessions_requested) * 100 }}%; transition: width 0.3s ease;"></div>
                                    </div>
                                </div>
                            @endif
                            @if($serviceRequest->progress_notes)
                                <div>
                                    <div style="font-size: 12px; color: #065f46; font-weight: 600; margin-bottom: 5px;">{{ __('patient.medical_history.service_requests.progress_notes') }}</div>
                                    <div style="color: #374151; font-size: 13px; line-height: 1.5;">{{ $serviceRequest->progress_notes }}</div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Resultados y Evaluación Final -->
                    @if($serviceRequest->outcome || $serviceRequest->final_assessment)
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                📋 {{ __('patient.medical_history.service_requests.final_assessment') }}
                            </div>
                            <div style="background: linear-gradient(135d, #d1fae5, #a7f3d0); padding: 15px; border-radius: 10px; color: #065f46;">
                                {{ $serviceRequest->outcome ?? $serviceRequest->final_assessment }}
                            </div>
                        </div>
                    @endif

                    <!-- Footer -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #64748b;">
                        <div>
                            @if($serviceRequest->encounter)
                                <span>📅 {{ __('patient.medical_history.service_requests.consultation_date') }}: {{ Carbon\Carbon::parse($serviceRequest->encounter->encounter_date)->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        <div>
                            @if($serviceRequest->reference_id)
                                <span>🔖 {{ __('patient.medical_history.service_requests.id_reference') }}: {{ $serviceRequest->reference_id }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{--}}
        <div style="margin-top: 20px;">
            {{ $sectionData->links() }}
        </div>
        {{--}}
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">🧪</div>
            <h3>{{ __('patient.medical_history.service_requests.no_service_requests') }}</h3>
            <p>{{ __('patient.medical_history.service_requests.no_service_requests_message') }}</p>
        </div>
    @endif
</div>
