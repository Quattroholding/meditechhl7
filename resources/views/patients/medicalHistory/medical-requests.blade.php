<div class="medical-requests-content">
    @if($sectionData && count($sectionData) > 0)
        <div class="requests-grid" style="display: grid; gap: 20px;">
            @foreach($sectionData['types'] as $type=>$value)
                @foreach($value as $request)
                    <?php

use App\Models\Scopes\EncouterScope;

                        $encounter = $request->encounter()->withoutGlobalScope(EncouterScope::class)->first();
                    ?>
                    <div class="request-card" style="background: white; border: 2px solid #f1f5f9; border-radius: 16px; padding: 25px; transition: all 0.3s ease;">
                        <div class="request-header" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                            <div>
                                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                                    @if($type=='medications')
                                        💊 {{ __('patient.medical_history.medical_requests.medication_prescription') }}
                                    @elseif($type=='services')
                                        @switch($request->cpt->type)
                                            @case('laboratory')
                                                🧪 {{ __('patient.medical_history.medical_requests.laboratory_exam') }}
                                                @break
                                            @case('images')
                                                📸 {{ __('patient.medical_history.medical_requests.imaging_study') }}
                                                @break
                                            @case('procedure')
                                                🩺 {{ __('patient.medical_history.medical_requests.procedure') }}
                                                @break
                                            @default
                                                📋 {{ __('patient.medical_history.medical_requests.medical_order') }}
                                        @endswitch
                                    @elseif($type=='referrals')
                                        👨‍⚕️ {{ __('patient.medical_history.medical_requests.medical_referral') }}
                                    @elseif($type=='procedures')
                                        🏥 {{ __('patient.medical_history.medical_requests.medical_procedure') }}
                                    @endif
                                </h3>
                                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <span class="badge badge-{{ $request->status === 'completed' ? 'resolved' : ($request->status === 'cancelled' ? 'inactive' : ($request->status === 'in-progress' ? 'active' : 'pending')) }}">
                                    @switch($request->status)
                                        @case('pending')
                                            ⏳ {{ __('patient.medical_history.medical_requests.status_pending') }}
                                            @break
                                        @case('approved')
                                            ✅ {{ __('patient.medical_history.medical_requests.status_approved') }}
                                            @break
                                        @case('in-progress')
                                            🔄 {{ __('patient.medical_history.medical_requests.status_in_progress') }}
                                            @break
                                        @case('completed')
                                            ✔️ {{ __('patient.medical_history.medical_requests.status_completed') }}
                                            @break
                                        @case('cancelled')
                                            ❌ {{ __('patient.medical_history.medical_requests.status_cancelled') }}
                                            @break
                                        @case('rejected')
                                            ⛔ {{ __('patient.medical_history.medical_requests.status_rejected') }}
                                            @break
                                        @default
                                            {!!  ucfirst($request->status)  !!}
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
                                                    🚨 {{ __('patient.medical_history.medical_requests.priority_urgent') }}
                                                    @break
                                                @case('high')
                                                    ⚡ {{ __('patient.medical_history.medical_requests.priority_high') }}
                                                    @break
                                                @case('stat')
                                                    ⚡⚡ {{ __('patient.medical_history.medical_requests.priority_stat') }}
                                                    @break
                                                @case('routine')
                                                    📅 {{ __('patient.medical_history.medical_requests.priority_routine') }}
                                                    @break
                                                @default
                                                    {{ ucfirst($request->priority) }}
                                            @endswitch
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div style="text-align: right; font-size: 12px; color: #64748b;">

                                <div><strong>{{ __('patient.medical_history.medical_requests.requested') }}:</strong> {{ Carbon\Carbon::parse($request->created_at)->format('d/m/Y H:i') }}</div>
                                @if($request->practitioner)
                                    <div><strong>{{ __('patient.medical_history.medical_requests.by') }}:</strong> {{ $request->practitioner->name }}</div>
                                    @if($encounter->medicalSpeciality)
                                        <div><strong>{{ __('patient.medical_history.medical_requests.specialty') }}:</strong> {{ $encounter->medicalSpeciality->name }}</div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Detalles de la Orden -->
                        <div class="request-details" style="background: #f8fafc; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 10px;">
                                📝 {{ __('patient.medical_history.medical_requests.order_details') }}
                            </div>
                            <div style="color: #1e293b; line-height: 1.6;">
                                {{ $request->dosage ?? $request->request_details }}
                            </div>
                        </div>

                        <!-- Información Específica por Tipo de Orden -->
                        @if($type=='services' && $request->cpt->type == 'laboratory')
                            <div class="lab-details" style="margin-bottom: 15px;">
                                <div style="font-size: 14px; font-weight: 600; color: #0891b2; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                    📋 {{ __('patient.medical_history.medical_requests.requested_laboratory') }}
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
                                            <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">🩸 {{ __('patient.medical_history.medical_requests.sample_type') }}</div>
                                            <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px;">
                                                {{ $request->sample_type }}
                                            </div>
                                        </div>
                                    @endif
                                    @if($request->fasting_required)
                                        <div>
                                            <div style="font-size: 12px; color: #f59e0b; font-weight: 600; margin-bottom: 5px;">⚠️ {{ __('patient.medical_history.medical_requests.preparation') }}</div>
                                            <div style="background: #fffbeb; padding: 10px; border-radius: 8px; font-size: 13px;">
                                                {{ __('patient.medical_history.medical_requests.fasting_hours', ['hours' => $request->fasting_hours ?? 12]) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($type=='services' && $request->cpt->type =='images')
                            <div class="imaging-details" style="margin-bottom: 15px;">
                                <div style="font-size: 14px; font-weight: 600; color: #059669; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                    📸 {{ __('patient.medical_history.medical_requests.requested_study') }}
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
                                            <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px;">📍 {{ __('patient.medical_history.medical_requests.body_region') }}</div>
                                            <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                                {{ $request->body_region }}
                                            </div>
                                        </div>
                                    @endif
                                    @if($request->contrast_required)
                                        <div>
                                            <div style="font-size: 12px; color: #ea580c; font-weight: 600; margin-bottom: 5px;">💉 {{ __('patient.medical_history.medical_requests.contrast') }}</div>
                                            <div style="background: #fff7ed; padding: 10px; border-radius: 8px; font-size: 13px;">
                                                {{ $request->contrast_type ?? __('patient.medical_history.medical_requests.required') }}
                                            </div>
                                        </div>
                                    @endif
                                    @if($request->preparation_instructions)
                                        <div style="grid-column: 1 / -1;">
                                            <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">📋 {{ __('patient.medical_history.medical_requests.preparation') }}</div>
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
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">

                                        <div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">💊 {{ __('patient.medical_history.medical_requests.medication') }}</div>
                                            <div style="font-weight: 600; color: #1e293b;">
                                                @if($request->medication2)
                                                    @php
                                                        $ingredient = $request->medication2->ingredients->first();
                                                        $strength = $ingredient ? $ingredient->strength_value . ' ' . $ingredient->strength_unit : '';
                                                    @endphp
                                                    {{ $request->medication2->display }} {{ $strength }} {{ $request->medication2->form }}
                                                @elseif($request->medicine)
                                                    {{ $request->medicine->full_name }}
                                                @else
                                                    {{ $request->medication }}
                                                @endif
                                            </div>
                                        </div>

                                    @if($request->dosage_text)
                                        <div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">📏 {{ __('patient.medical_history.medical_requests.dose') }}</div>
                                            <div>{{ $request->dosage_text }}</div>
                                        </div>
                                    @endif
                                    @if($request->dosage_instruction && is_array($request->dosage_instruction))
                                        <div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">📋 {{ __('patient.medical_history.medical_requests.dosage_instructions') }}</div>
                                            <div>{{ $request->dosage_instruction['text'] ?? json_encode($request->dosage_instruction) }}</div>
                                        </div>
                                    @endif
                                    @if($request->frequency)
                                        <div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">⏰ {{ __('patient.medical_history.medical_requests.frequency') }}</div>
                                            <div>{{ $request->frequency }}</div>
                                        </div>
                                    @endif
                                    @if($request->duration)
                                        <div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">📅 {{ __('patient.medical_history.medical_requests.duration') }}</div>
                                            <div>{{ $request->duration }}</div>
                                        </div>
                                    @endif
                                    @if($request->route)
                                        <div>
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">🎯 {{ __('patient.medical_history.medical_requests.route') }}</div>
                                            <div>{{ $request->route }}</div>
                                        </div>
                                    @endif
                                    @if($request->dosage_text)
                                        <div style="grid-column: 1 / -1;">
                                            <div style="font-size: 12px; color: #059669; font-weight: 600;">📋 {{ __('patient.medical_history.medical_requests.instructions') }}</div>
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
                                    🎯 {{ __('patient.medical_history.medical_requests.clinical_justification') }}
                                </div>
                                <div style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); padding: 15px; border-radius: 10px; color: #1e40af; border-left: 4px solid #3b82f6;">
                                    {{ $request->clinical_justification }}
                                </div>
                            </div>
                        @endif

                        <!-- Timeline de Estados -->
                        <div class="request-timeline" style="background: #f8fafc; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 10px;">
                                ⏱️ {{ __('patient.medical_history.medical_requests.order_timeline') }}
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                @if($request->created_at)
                                    <div>
                                        <div style="font-size: 12px; color: #3b82f6; font-weight: 600;">📅 {{ __('patient.medical_history.medical_requests.scheduled') }}</div>
                                        <div style="font-size: 13px; color: #1e293b;">{{ Carbon\Carbon::parse($request->created_at)->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif
                                @if($request->started_date)
                                    <div>
                                        <div style="font-size: 12px; color: #f59e0b; font-weight: 600;">▶️ {{ __('patient.medical_history.medical_requests.started') }}</div>
                                        <div style="font-size: 13px; color: #1e293b;">{{ Carbon\Carbon::parse($request->started_date)->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif
                                @if($request->completed_date)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600;">✅ {{ __('patient.medical_history.medical_requests.completed_date') }}</div>
                                        <div style="font-size: 13px; color: #1e293b;">{{ Carbon\Carbon::parse($request->completed_date)->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif
                                @if($request->results_available_date)
                                    <div>
                                        <div style="font-size: 12px; color: #7c3aed; font-weight: 600;">📊 {{ __('patient.medical_history.medical_requests.results') }}</div>
                                        <div style="font-size: 13px; color: #1e293b;">{{ Carbon\Carbon::parse($request->results_available_date)->format('d/m/Y') }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Resultados -->
                        @if($request->results)
                            <div style="margin-bottom: 15px;">
                                <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                    📊 {{ __('patient.medical_history.medical_requests.results') }}
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
                                    <span>📅 {{ __('patient.medical_history.medical_requests.consultation') }}: {{ Carbon\Carbon::parse($request->encounter->encounter_date)->format('d/m/Y') }}</span>
                                @endif
                            </div>
                            <div>
                                @if($request->reference_number)
                                    <span>🔖 {{ __('patient.medical_history.medical_requests.ref') }}: {{ $request->reference_number }}</span>
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
            <h3>{{ __('patient.medical_history.medical_requests.no_medical_orders') }}</h3>
            <p>{{ __('patient.medical_history.medical_requests.no_medical_orders_message') }}</p>
        </div>
    @endif
</div>
