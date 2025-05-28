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
                                        👨‍👩‍👧‍👦 Historia Familiar
                                        @break
                                    @case('personal')
                                        👤 Historia Personal
                                        @break
                                    @case('surgery')
                                        🏥 Historia Quirúrgica
                                        @break
                                    @case('obstetric')
                                        🤱 Historia Obstétrica
                                        @break
                                    @case('gynecologic')
                                        👩 Historia Ginecológica
                                        @break
                                    @case('social-history')
                                        🌍 Historia Social
                                        @break
                                    @case('occupational')
                                        💼 Historia Ocupacional
                                        @break
                                    @case('psychiatric')
                                        🧠 Historia Psiquiátrica
                                        @break
                                    @case('allergy')
                                        ⚠️ Alergias
                                        @break
                                    @case('medications')
                                        💊 Historia Farmacológica
                                        @break
                                    @case('immunizations')
                                        💉 Historia de Vacunación
                                        @break
                                    @default
                                        📚 Historia Médica
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
                                                🔴 Alta Relevancia
                                                @break
                                            @case('medium')
                                                🟡 Relevancia Media
                                                @break
                                            @case('low')
                                                🟢 Baja Relevancia
                                                @break
                                            @default
                                                {{ ucfirst($history->relevance) }}
                                        @endswitch
                                    </span>
                                @endif
                                @if($history->verification_status=='confirmed')
                                    <span class="badge badge-resolved">✅ Verificado</span>
                                @else
                                    <span class="badge" style="background: #f3f4f6; color: #6b7280;">❓ No Verificado</span>
                                @endif
                                @if($history->clinical_status=='active')
                                    <span class="badge badge-active">🟢 Activo</span>
                                @else
                                    <span class="badge badge-inactive">⚫ Inactivo</span>
                                @endif
                            </div>
                        </div>
                        <div style="text-align: right; font-size: 12px; color: #64748b;">
                            <div><strong>Registrado:</strong> {{ Carbon\Carbon::parse($history->recorded_date)->format('d/m/Y') }}</div>
                            @if($history->source)
                                <div><strong>Fuente:</strong> {{ $history->source }}</div>
                            @endif
                            @if($history->recorded_by)
                                <div><strong>Por:</strong> {{ $history->recorded_by }}</div>
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
                                👨‍👩‍👧‍👦 Información Familiar
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                @if($history->relationship)
                                    <div>
                                        <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">👥 Parentesco</div>
                                        <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->relationship }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->condition)
                                    <div>
                                        <div style="font-size: 12px; color: #0891b2; font-weight: 600; margin-bottom: 5px;">🩺 Condición</div>
                                        <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->condition }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->age_at_onset)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">🎂 Edad de Inicio</div>
                                        <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->age_at_onset }} años
                                        </div>
                                    </div>
                                @endif
                                @if($history->age_at_death)
                                    <div>
                                        <div style="font-size: 12px; color: #6b7280; font-weight: 600; margin-bottom: 5px;">⚰️ Edad de Fallecimiento</div>
                                        <div style="background: #f9fafb; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->age_at_death }} años
                                        </div>
                                    </div>
                                @endif
                                @if($history->cause_of_death)
                                    <div style="grid-column: 1 / -1;">
                                        <div style="font-size: 12px; color: #6b7280; font-weight: 600; margin-bottom: 5px;">💀 Causa de Fallecimiento</div>
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
                                🏥 Información Quirúrgica
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                @if($history->procedure_name)
                                    <div>
                                        <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px;">⚕️ Procedimiento</div>
                                        <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->procedure_name }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->date_performed)
                                    <div>
                                        <div style="font-size: 12px; color: #ea580c; font-weight: 600; margin-bottom: 5px;">📅 Fecha</div>
                                        <div style="background: #fff7ed; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ Carbon\Carbon::parse($history->date_performed)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->surgeon)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">👨‍⚕️ Cirujano</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->surgeon }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->hospital)
                                    <div>
                                        <div style="font-size: 12px; color: #0891b2; font-weight: 600; margin-bottom: 5px;">🏥 Hospital</div>
                                        <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->hospital }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->anesthesia_type)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">💨 Anestesia</div>
                                        <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->anesthesia_type }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->complications)
                                    <div style="grid-column: 1 / -1;">
                                        <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">⚠️ Complicaciones</div>
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
                                🌍 Historia Social
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                @if($history->smoking_status)
                                    <div>
                                        <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">🚬 Tabaquismo</div>
                                        <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->smoking_status }}
                                            @if($history->smoking_pack_years)
                                                <br><small>({{ $history->smoking_pack_years }} paquetes/año)</small>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if($history->alcohol_use)
                                    <div>
                                        <div style="font-size: 12px; color: #f59e0b; font-weight: 600; margin-bottom: 5px;">🍺 Alcohol</div>
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
                                        <div style="font-size: 12px; color: #8b5cf6; font-weight: 600; margin-bottom: 5px;">💊 Sustancias</div>
                                        <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->drug_use }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->exercise_habits)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">🏃‍♂️ Ejercicio</div>
                                        <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->exercise_habits }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->diet_habits)
                                    <div>
                                        <div style="font-size: 12px; color: #0891b2; font-weight: 600; margin-bottom: 5px;">🥗 Dieta</div>
                                        <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->diet_habits }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->sleep_patterns)
                                    <div>
                                        <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px;">😴 Sueño</div>
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
                                🤱 Historia Obstétrica
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                                @if($history->gravida)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">🤰 Gravida</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px; text-align: center; font-weight: 600;">
                                            G{{ $history->gravida }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->para)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">👶 Para</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px; text-align: center; font-weight: 600;">
                                            P{{ $history->para }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->abortions)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">💔 Abortos</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px; text-align: center; font-weight: 600;">
                                            A{{ $history->abortions }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->living_children)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">👨‍👩‍👧‍👦 Vivos</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px; text-align: center; font-weight: 600;">
                                            {{ $history->living_children }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->last_menstrual_period)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">📅 FUM</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ Carbon\Carbon::parse($history->last_menstrual_period)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->contraceptive_method)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">💊 Anticoncepción</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->contraceptive_method }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($history->pregnancy_complications)
                                <div style="margin-top: 15px;">
                                    <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">⚠️ Complicaciones del Embarazo</div>
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
                                ⚠️ Información de Alergias
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                @if($history->allergen)
                                    <div>
                                        <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">🌿 Alérgeno</div>
                                        <div style="background: #fef2f2; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                                            {{ $history->allergen }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->reaction_type)
                                    <div>
                                        <div style="font-size: 12px; color: #f59e0b; font-weight: 600; margin-bottom: 5px;">⚡ Tipo de Reacción</div>
                                        <div style="background: #fffbeb; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->reaction_type }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->severity)
                                    <div>
                                        <div style="font-size: 12px; color: #8b5cf6; font-weight: 600; margin-bottom: 5px;">📊 Severidad</div>
                                        <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ ucfirst($history->severity) }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->first_occurrence)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">📅 Primera Vez</div>
                                        <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ Carbon\Carbon::parse($history->first_occurrence)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($history->reaction_description)
                                <div style="margin-top: 15px;">
                                    <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">📝 Descripción de la Reacción</div>
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
                                💉 Historia de Vacunación
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                @if($history->vaccine_name)
                                    <div>
                                        <div style="font-size: 12px; color: #059669; font-weight: 600; margin-bottom: 5px;">💉 Vacuna</div>
                                        <div style="background: #f0fdf4; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 600;">
                                            {{ $history->vaccine_name }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->administration_date)
                                    <div>
                                        <div style="font-size: 12px; color: #0891b2; font-weight: 600; margin-bottom: 5px;">📅 Fecha</div>
                                        <div style="background: #f0f9ff; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ Carbon\Carbon::parse($history->administration_date)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->dose_number)
                                    <div>
                                        <div style="font-size: 12px; color: #7c3aed; font-weight: 600; margin-bottom: 5px;">🔢 Dosis</div>
                                        <div style="background: #faf5ff; padding: 10px; border-radius: 8px; font-size: 13px; text-align: center;">
                                            {{ $history->dose_number }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->lot_number)
                                    <div>
                                        <div style="font-size: 12px; color: #ea580c; font-weight: 600; margin-bottom: 5px;">🏷️ Lote</div>
                                        <div style="background: #fff7ed; padding: 10px; border-radius: 8px; font-size: 13px; font-family: monospace;">
                                            {{ $history->lot_number }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->administered_by)
                                    <div>
                                        <div style="font-size: 12px; color: #be185d; font-weight: 600; margin-bottom: 5px;">👨‍⚕️ Administrado por</div>
                                        <div style="background: #fdf2f8; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ $history->administered_by }}
                                        </div>
                                    </div>
                                @endif
                                @if($history->next_due_date)
                                    <div>
                                        <div style="font-size: 12px; color: #f59e0b; font-weight: 600; margin-bottom: 5px;">📅 Próxima Dosis</div>
                                        <div style="background: #fffbeb; padding: 10px; border-radius: 8px; font-size: 13px;">
                                            {{ Carbon\Carbon::parse($history->next_due_date)->format('d/m/Y') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($history->adverse_reactions)
                                <div style="margin-top: 15px;">
                                    <div style="font-size: 12px; color: #dc2626; font-weight: 600; margin-bottom: 5px;">⚠️ Reacciones Adversas</div>
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
                                📝 Notas Adicionales
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
