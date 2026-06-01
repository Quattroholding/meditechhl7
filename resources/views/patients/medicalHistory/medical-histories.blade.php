<div class="medical-histories-content">
    @if($sectionData && count($sectionData) > 0)
        <div class="histories-grid" style="display: grid; gap: 25px;">
            @foreach($sectionData as $history)
                <div class="history-card" style="background: white; border: 2px solid #f1f5f9; border-radius: 16px; padding: 25px; transition: all 0.3s ease;">
                    <div class="history-header" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                                @switch($history->category)
                                    @case('family-history')
                                        👨‍👩‍👧‍👦 {{ __('patient.medical_history.family_history_title') }}
                                        @break
                                    @case('personal')
                                        👤 {{ __('patient.medical_history.personal_history') }}
                                        @break
                                    @case('surgery')
                                        🏥 {{ __('patient.medical_history.surgical_history') }}
                                        @break
                                    @case('obstetric')
                                        🤱 {{ __('patient.medical_history.obstetric_history') }}
                                        @break
                                    @case('gynecologic')
                                        👩 {{ __('patient.medical_history.gynecologic_history') }}
                                        @break
                                    @case('social-history')
                                        🌍 {{ __('patient.medical_history.social_history_title') }}
                                        @break
                                    @case('occupational')
                                        💼 {{ __('patient.medical_history.occupational_history') }}
                                        @break
                                    @case('psychiatric')
                                        🧠 {{ __('patient.medical_history.psychiatric_history') }}
                                        @break
                                    @case('allergy')
                                        ⚠️ {{ __('patient.medical_history.allergies_title') }}
                                        @break
                                    @case('medications')
                                        💊 {{ __('patient.medical_history.pharmacological_history') }}
                                        @break
                                    @case('immunizations')
                                        💉 {{ __('patient.medical_history.vaccination_history') }}
                                        @break
                                    @default
                                        📚 {{ __('patient.medical_history.medical_history_title') }}
                                @endswitch
                            </h3>
                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                @if($history->relevance)
                                    <span class="badge" style="background:
                                        @if($history->relevance === 'high') #fee2e2; color: #dc2626;
                                        @elseif($history->relevance === 'medium') #fef3c7; color: #92400e;
                                        @else #f0fdf4; color: #166534; @endif">
                                        @switch($history->relevance)
                                            @case('high')
                                                🔴 {{ __('patient.medical_history.high_relevance') }}
                                                @break
                                            @case('medium')
                                                🟡 {{ __('patient.medical_history.medium_relevance') }}
                                                @break
                                            @case('low')
                                                🟢 {{ __('patient.medical_history.low_relevance') }}
                                                @break
                                            @default
                                                {{ ucfirst($history->relevance) }}
                                        @endswitch
                                    </span>
                                @endif
                                @if($history->verification_status=='confirmed')
                                    <span class="badge badge-resolved">✅ {{ __('patient.medical_history.verified') }}</span>
                                @else
                                    <span class="badge" style="background: #f3f4f6; color: #6b7280;">❓ {{ __('patient.medical_history.not_verified') }}</span>
                                @endif
                                @if($history->clinical_status=='active')
                                    <span class="badge badge-active">🟢 {{ __('patient.medical_history.active') }}</span>
                                @else
                                    <span class="badge badge-inactive">⚫ {{ __('patient.medical_history.inactive') }}</span>
                                @endif
                            </div>
                        </div>
                        <div style="text-align: right; font-size: 12px; color: #64748b;">
                            <div><strong>{{ __('patient.medical_history.recorded') }}:</strong> {{ Carbon\Carbon::parse($history->recorded_date)->format('d/m/Y') }}</div>
                            @if($history->source)
                                <div><strong>{{ __('patient.medical_history.source') }}:</strong> {{ $history->source }}</div>
                            @endif
                            @if($history->recorded_by)
                                <div><strong>{{ __('patient.medical_history.by') }}:</strong> {{ $history->recorded_by }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="history-title">
                        <h4>{{$history->title}}</h4>
                    </div>
                    <!-- Contenido Principal -->
                    <div class="history-content" style="background: #f8fafc; padding: 15px; border-radius: 10px; margin-bottom: 15px;">

                        <div style="color: #1e293b; line-height: 1.6;">
                            {{ $history->description ?? $history->details }}
                        </div>
                    </div>

                    <!-- Detalles Específicos por Tipo de Historia -->
                    @if($history->history_type === 'family')
                        <div class="family-details" style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #dc2626; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                👨‍👩‍👧‍👦 {{ __('patient.medical_history.family_information') }}
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                @if($history->relationship)
                                    <div>
                                        <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">👥 {{ __('patient.medical_history.relationship') }}</div>
                                        <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->relationship }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->condition)
                                    <div>
                                        <div style="font-size: 12px; color: #0891b2; font-weight: 600; margin-bottom: 5px;">🩺 {{ __('patient.medical_history.condition') }}</div>
                                        <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->condition }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->age_at_onset)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">🎂 {{ __('patient.medical_history.age_at_onset') }}</div>
                                        <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->age_at_onset }} {{ __('patient.medical_history.years') }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->age_at_death)
                                    <div>
                                        <div style="font-size: 12px; color: #6b7280; font-weight: 600; margin-bottom: 5px;">⚰️ {{ __('patient.medical_history.age_at_death') }}</div>
                                        <div style="background: #f9fafb; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->age_at_death }} {{ __('patient.medical_history.years') }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->cause_of_death)
                                    <div style="grid-column: 1 / -1;">
                                        <div style="font-size: 12px; color: #6b7280; font-weight: 600; margin-bottom: 5px;">💀 {{ __('patient.medical_history.cause_of_death') }}</div>
                                        <div style="background: #f9fafb; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->cause_of_death }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($history->history_type === 'surgical')
                        <div class="surgical-details" style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #7c3aed; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                🏥 {{ __('patient.medical_history.surgical_information') }}
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                @if($history->procedure_name)
                                    <div>
                                        <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px;">⚕️ {{ __('patient.medical_history.procedure') }}</div>
                                        <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->procedure_name }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->date_performed)
                                    <div>
                                        <div style="font-size: 12px; color: #ea580c; font-weight: 600; margin-bottom: 5px;">📅 {{ __('patient.medical_history.date_performed') }}</div>
                                        <div style="background: #fff7ed; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ Carbon\Carbon::parse($history->date_performed)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->surgeon)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">👨‍⚕️ {{ __('patient.medical_history.surgeon') }}</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->surgeon }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->hospital)
                                    <div>
                                        <div style="font-size: 12px; color: #0891b2; font-weight: 600; margin-bottom: 5px;">🏥 {{ __('patient.medical_history.hospital') }}</div>
                                        <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->hospital }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->anesthesia_type)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">💨 {{ __('patient.medical_history.anesthesia') }}</div>
                                        <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->anesthesia_type }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->complications)
                                    <div style="grid-column: 1 / -1;">
                                        <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">⚠️ {{ __('patient.medical_history.complications') }}</div>
                                        <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->complications }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($history->history_type === 'social')
                        <div class="social-details" style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #059669; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                🌍 {{ __('patient.medical_history.social_history_section') }}
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                @if($history->smoking_status)
                                    <div>
                                        <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">🚬 {{ __('patient.medical_history.smoking') }}</div>
                                        <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->smoking_status }}
                                            @if($history->smoking_pack_years)
                                                <br><small>({{ $history->smoking_pack_years }} {{ __('patient.medical_history.pack_years') }})</small>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if($history->alcohol_use)
                                    <div>
                                        <div style="font-size: 12px; color: #f59e0b; font-weight: 600; margin-bottom: 5px;">🍺 {{ __('patient.medical_history.alcohol') }}</div>
                                        <div style="background: #fffbeb; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->alcohol_use }}
                                            @if($history->alcohol_frequency)
                                                <br><small>({{ $history->alcohol_frequency }})</small>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if($history->drug_use)
                                    <div>
                                        <div style="font-size: 12px; color: #8b5cf6; font-weight: 600; margin-bottom: 5px;">💊 {{ __('patient.medical_history.substances') }}</div>
                                        <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->drug_use }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->exercise_habits)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">🏃‍♂️ {{ __('patient.medical_history.exercise') }}</div>
                                        <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->exercise_habits }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->diet_habits)
                                    <div>
                                        <div style="font-size: 12px; color: #0891b2; font-weight: 600; margin-bottom: 5px;">🥗 {{ __('patient.medical_history.diet') }}</div>
                                        <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->diet_habits }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->sleep_patterns)
                                    <div>
                                        <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px;">😴 {{ __('patient.medical_history.sleep') }}</div>
                                        <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->sleep_patterns }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($history->history_type === 'obstetric')
                        <div class="obstetric-details" style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #be185d; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                🤱 {{ __('patient.medical_history.obstetric_history_section') }}
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                                @if($history->gravida)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">🤰 {{ __('patient.medical_history.gravida') }}</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px; text-align: center; font-weight: 600;">
                                            G{{ $history->gravida }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->para)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">👶 {{ __('patient.medical_history.para') }}</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px; text-align: center; font-weight: 600;">
                                            P{{ $history->para }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->abortions)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">💔 {{ __('patient.medical_history.abortions') }}</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px; text-align: center; font-weight: 600;">
                                            A{{ $history->abortions }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->living_children)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">👨‍👩‍👧‍👦 {{ __('patient.medical_history.living_children') }}</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px; text-align: center; font-weight: 600;">
                                            {{ $history->living_children }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->last_menstrual_period)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">📅 {{ __('patient.medical_history.lmp') }}</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ Carbon\Carbon::parse($history->last_menstrual_period)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->contraceptive_method)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">💊 {{ __('patient.medical_history.contraception') }}</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->contraceptive_method }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($history->pregnancy_complications)
                                <div style="margin-top: 15px;">
                                    <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">⚠️ {{ __('patient.medical_history.pregnancy_complications') }}</div>
                                    <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px;">
                                        {{ $history->pregnancy_complications }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($history->history_type === 'allergies')
                        <div class="allergy-details" style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #dc2626; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                ⚠️ {{ __('patient.medical_history.allergy_information') }}
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                @if($history->allergen)
                                    <div>
                                        <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">🌿 {{ __('patient.medical_history.allergen') }}</div>
                                        <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                                            {{ $history->allergen }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->reaction_type)
                                    <div>
                                        <div style="font-size: 12px; color: #f59e0b; font-weight: 600; margin-bottom: 5px;">⚡ {{ __('patient.medical_history.reaction_type') }}</div>
                                        <div style="background: #fffbeb; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->reaction_type }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->severity)
                                    <div>
                                        <div style="font-size: 12px; color: #8b5cf6; font-weight: 600; margin-bottom: 5px;">📊 {{ __('patient.medical_history.severity_label') }}</div>
                                        <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ ucfirst($history->severity) }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->first_occurrence)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">📅 {{ __('patient.medical_history.first_occurrence') }}</div>
                                        <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ Carbon\Carbon::parse($history->first_occurrence)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($history->reaction_description)
                                <div style="margin-top: 15px;">
                                    <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">📝 {{ __('patient.medical_history.reaction_description') }}</div>
                                    <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px; border-left: 4px solid #dc2626;">
                                        {{ $history->reaction_description }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($history->history_type === 'immunizations')
                        <div class="immunization-details" style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #059669; margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                💉 {{ __('patient.medical_history.vaccination_history_section') }}
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                @if($history->vaccine_name)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">💉 {{ __('patient.medical_history.vaccine') }}</div>
                                        <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                                            {{ $history->vaccine_name }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->administration_date)
                                    <div>
                                        <div style="font-size: 12px; color: #0891b2; font-weight: 600; margin-bottom: 5px;">📅 {{ __('patient.medical_history.administration_date') }}</div>
                                        <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ Carbon\Carbon::parse($history->administration_date)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->dose_number)
                                    <div>
                                        <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px;">🔢 {{ __('patient.medical_history.dose') }}</div>
                                        <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px; text-align: center;">
                                            {{ $history->dose_number }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->lot_number)
                                    <div>
                                        <div style="font-size: 12px; color: #ea580c; font-weight: 600; margin-bottom: 5px;">🏷️ {{ __('patient.medical_history.lot') }}</div>
                                        <div style="background: #fff7ed; padding: 10px; border-radius: 8px; font-size: 13px; font-family: monospace;">
                                            {{ $history->lot_number }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->administered_by)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">👨‍⚕️ {{ __('patient.medical_history.administered_by') }}</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->administered_by }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->next_due_date)
                                    <div>
                                        <div style="font-size: 12px; color: #f59e0b; font-weight: 600; margin-bottom: 5px;">📅 {{ __('patient.medical_history.next_dose') }}</div>
                                        <div style="background: #fffbeb; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ Carbon\Carbon::parse($history->next_due_date)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($history->adverse_reactions)
                                <div style="margin-top: 15px;">
                                    <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">⚠️ {{ __('patient.medical_history.adverse_reactions') }}</div>
                                    <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px; border-left: 4px solid #dc2626;">
                                        {{ $history->adverse_reactions }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Notas Adicionales -->
                    @if($history->notes)
                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                                📝 {{ __('patient.medical_history.additional_notes') }}
                            </div>
                            <div style="background: linear-gradient(135deg, #fef3c7, #fcd34d); padding: 15px; border-radius: 10px; color: #92400e; font-style: italic; border-left: 4px solid #f59e0b;">
                                {{ $history->notes }}
                            </div>
                        </div>
                        @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
