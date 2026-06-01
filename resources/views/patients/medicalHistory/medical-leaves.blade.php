<div class="medical-leaves-content">
    @if($sectionData && (isset($sectionData['data']) ? count($sectionData['data']) > 0 : count($sectionData) > 0))
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                <tr>
                    <th>{{ __('patient.medical_history.record_number') }}</th>
                    <th>{{ __('patient.medical_history.issue_date') }}</th>
                    <th>{{ __('patient.medical_history.disability_period') }}</th>
                    <th>{{ __('patient.medical_history.days') }}</th>
                    <th>{{ __('patient.medical_history.diagnosis') }}</th>
                    <th>{{ __('patient.medical_history.doctor') }}</th>
                    <th>{{ __('patient.medical_history.status') }}</th>
                    <th>{{ __('patient.medical_history.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach((isset($sectionData['data']) ? $sectionData['data'] : $sectionData) as $medicalLeave)
                    <tr>
                        <td>
                            <strong>{{ $medicalLeave->identifier }}</strong>
                        </td>
                        <td>
                            <strong>{{ $medicalLeave->issue_date->format('d/m/Y') }}</strong>
                        </td>
                        <td>
                            <strong>{{ __('patient.medical_history.from') }}:</strong> {{ $medicalLeave->start_datetime->format('d/m/Y H:i') }}<br>
                            <strong>{{ __('patient.medical_history.until') }}:</strong> {{ $medicalLeave->end_datetime->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <span class="badge badge-info" style="background: #3b82f6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                {{ $medicalLeave->total_days }} {{ __('patient.medical_history.day_s') }}
                            </span>
                        </td>
                        <td>
                            @if($medicalLeave->diagnosis_code)
                                <strong>{{ $medicalLeave->diagnosis_code }}</strong><br>
                            @endif
                            <small>{{ Str::limit($medicalLeave->diagnosis, 50) }}</small>
                        </td>
                        <td>
                            <strong>{{ $medicalLeave->practitioner_name }}</strong>
                            @if($medicalLeave->practitioner_license_number)
                                <br><small>{{ __('patient.medical_history.code_label') }}: {{ $medicalLeave->practitioner_license_number }}</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'active' => '#10b981',
                                    'completed' => '#6b7280',
                                    'cancelled' => '#ef4444',
                                    'draft' => '#f59e0b',
                                ];
                                $statusLabels = [
                                    'active' => __('patient.medical_history.active_leave'),
                                    'completed' => __('patient.medical_history.completed_leave'),
                                    'cancelled' => __('patient.medical_history.cancelled_leave'),
                                    'draft' => __('patient.medical_history.draft_leave'),
                                ];
                                $statusColor = $statusColors[$medicalLeave->status] ?? '#6b7280';
                                $statusLabel = $statusLabels[$medicalLeave->status] ?? ucfirst($medicalLeave->status);
                            @endphp
                            <span class="badge" style="background: {{ $statusColor }}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('medical-leaves.download-pdf', $medicalLeave->id) }}"
                               target="_blank"
                               class="btn"
                               style="background: #3b82f6; color: white; padding: 6px 12px; font-size: 12px; border-radius: 6px; text-decoration: none; display: inline-block;">
                                📄 {{ __('patient.medical_history.download_pdf') }}
                            </a>
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
                        <button wire:click="previousMedicalLeavesPage"
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
                                <button wire:click="gotoMedicalLeavesPage({{ $page }})"
                                        style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 14px; min-width: 40px; text-align: center; transition: all 0.3s ease;">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach
                    </div>

                    <!-- Next Button -->
                    @if($sectionData['current_page'] < $sectionData['last_page'])
                        <button wire:click="nextMedicalLeavesPage"
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
                {{ __('patient.medical_history.showing_leaves') }} {{ $sectionData['from'] ?? 0 }} {{ __('patient.medical_history.to') }} {{ $sectionData['to'] ?? 0 }}
                {{ __('patient.medical_history.of') }} {{ $sectionData['total'] ?? 0 }} {{ __('patient.medical_history.total') }}
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">📄</div>
            <h3>{{ __('patient.medical_history.no_leaves_registered') }}</h3>
            <p>{{ __('patient.medical_history.no_leaves_message') }}</p>
        </div>
    @endif
</div>
