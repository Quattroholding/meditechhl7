<div class="encounters-content">
    @if($sectionData && (isset($sectionData['data']) ? count($sectionData['data']) > 0 : count($sectionData) > 0))
        <!-- Botón Descargar Historial Completo -->
        <div style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
            <button
                onclick="requestFullHistory({{ $patient->id }})"
                id="downloadHistoryBtn"
                class="btn"
                style="background: #10b981; color: white; padding: 10px 20px; font-size: 14px; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background 0.3s;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span id="downloadHistoryText">{{ __('patient.medical_history.download_full_history') }}</span>
            </button>
        </div>

        <div class="data-table-container">
            <table class="data-table">
                <thead>
                <tr>
                    <th>{{ __('patient.medical_history.date') }}</th>
                    {{--}}
                    <th>{{ __('patient.medical_history.type') }}</th>
                    {{--}}
                    <th>{{ __('patient.medical_history.professional') }}</th>
                    <th>{{ __('patient.medical_history.diagnosis') }}</th>
                    <th>{{ __('patient.medical_history.status') }}</th>
                    {{--}}
                    <th>{{ __('patient.medical_history.location') }}</th>
                    {{--}}
                    <th>{{ __('patient.medical_history.actions') }}</th>
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
                            @if($encounter->assistant->id)
                              <br>  👩‍💼 Asistente: {{ $encounter->assistant->full_name ?? '' }}
                            @endif
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
                            @if(auth()->user()->can('view',$encounter) && $encounter->appointment_id)
                            <a  href="{{route('consultation.download_resumen',$encounter->appointment_id)}}" target="_blank" class="btn" style="background: #3b82f6; color: white; padding: 6px 12px; font-size: 12px;">
                                👁️ {{ __('patient.medical_history.view_details') }}
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
                            ← {{ __('patient.medical_history.previous') }}
                        </button>
                    @else
                        <span style="background: #e2e8f0; color: #9ca3af; padding: 8px 16px; border-radius: 8px; font-size: 14px;">
                            ← {{ __('patient.medical_history.previous') }}
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
                            {{ __('patient.medical_history.next') }} →
                        </button>
                    @else
                        <span style="background: #e2e8f0; color: #9ca3af; padding: 8px 16px; border-radius: 8px; font-size: 14px;">
                            {{ __('patient.medical_history.next') }} →
                        </span>
                    @endif
                </nav>
            </div>

            <!-- Pagination Info -->
            <div style="margin-top: 15px; text-align: center; font-size: 13px; color: #64748b;">
                {{ __('patient.medical_history.showing_consultations') }} {{ $sectionData['from'] ?? 0 }} {{ __('patient.medical_history.to') }} {{ $sectionData['to'] ?? 0 }}
                {{ __('patient.medical_history.of') }} {{ $sectionData['total'] ?? 0 }} {{ __('patient.medical_history.total') }}
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">🏥</div>
            <h3>{{ __('patient.medical_history.no_consultations_registered') }}</h3>
            <p>{{ __('patient.medical_history.no_consultations_message') }}</p>
        </div>
    @endif
</div>

<script>
// Asegurar que las variables sean globales
if (typeof window.historyDownloadId === 'undefined') {
    window.historyDownloadId = null;
}
if (typeof window.pollingInterval === 'undefined') {
    window.pollingInterval = null;
}

window.requestFullHistory = async function(patientId) {
    const btn = document.getElementById('downloadHistoryBtn');
    const btnText = document.getElementById('downloadHistoryText');

    btn.disabled = true;
    btn.style.opacity = '0.6';
    btn.style.cursor = 'not-allowed';
    btnText.textContent = '{{ __('patient.medical_history.initiating_generation') }}';

    try {
        const response = await fetch('/patient-history/${patientId}/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            window.historyDownloadId = data.history_download_id;
            btnText.textContent = '{{ __('patient.medical_history.generating') }} 0%';
            window.startPolling();
            window.showNotification('{{ __('patient.medical_history.initiating_generation') }}', 'success');
        } else {
            throw new Error(data.message || 'Error al iniciar generación');
        }
    } catch (error) {
        console.error('Error:', error);
        window.showNotification(error.message, 'error');
        window.resetButton();
    }
}

window.startPolling = function() {
    window.pollingInterval = setInterval(window.checkStatus, 3000);
}

window.stopPolling = function() {
    if (window.pollingInterval) {
        clearInterval(window.pollingInterval);
        window.pollingInterval = null;
    }
}

window.checkStatus = async function() {
    if (!window.historyDownloadId) return;

    try {
        const response = await fetch(`/patient-history/${window.historyDownloadId}/status`);
        const data = await response.json();

        if (data.success) {
            const btnText = document.getElementById('downloadHistoryText');

            if (data.status === 'completed') {
                window.stopPolling();
                btnText.textContent = '{{ __('patient.medical_history.ready_click_download') }}';
                const btn = document.getElementById('downloadHistoryBtn');
                btn.style.background = '#10b981';
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
                btn.disabled = false;
                btn.onclick = () => window.location.href = data.download_url;
                window.showNotification(`{{ __('patient.medical_history.ready_click_download') }} (${data.total_encounters})`, 'success');
            } else if (data.status === 'failed') {
                window.stopPolling();
                btnText.textContent = '{{ __('patient.medical_history.generation_error') }}';
                window.showNotification(data.error_message || '{{ __('patient.medical_history.generation_error') }}', 'error');
                setTimeout(window.resetButton, 3000);
            } else if (data.status === 'processing') {
                btnText.textContent = `{{ __('patient.medical_history.generating') }} ${data.progress}% (${data.processed_encounters}/${data.total_encounters})`;
            }
        }
    } catch (error) {
        console.error('Error checking status:', error);
    }
}

window.resetButton = function() {
    const btn = document.getElementById('downloadHistoryBtn');
    const btnText = document.getElementById('downloadHistoryText');

    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor = 'pointer';
    btn.style.background = '#10b981';
    btnText.textContent = '{{ __('patient.medical_history.download_full_history') }}';
    btn.onclick = () => window.requestFullHistory({{ $patient->id }});

    window.historyDownloadId = null;
    window.stopPolling();
}

window.showNotification = function(message, type = 'info') {
    const colors = {success: '#10b981', error: '#ef4444', info: '#3b82f6'};
    const notification = document.createElement('div');
    notification.style.cssText = `position:fixed;top:20px;right:20px;background:${colors[type]};color:white;padding:16px 24px;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);z-index:9999;font-size:14px;max-width:400px;animation:slideIn 0.3s ease;`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Solo agregar el estilo una vez
if (!document.getElementById('notification-animations')) {
    const style = document.createElement('style');
    style.id = 'notification-animations';
    style.textContent = `@keyframes slideIn{from{transform:translateX(400px);opacity:0}to{transform:translateX(0);opacity:1}}@keyframes slideOut{from{transform:translateX(0);opacity:1}to{transform:translateX(400px);opacity:0}}`;
    document.head.appendChild(style);
}

window.addEventListener('beforeunload', window.stopPolling);
</script>
