<div class="daily-timeline-container" x-data="timelineManager()" x-init="init()">

    <style>
        .daily-timeline-container {
            max-height: 700px;
            overflow-y: auto;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
            border-radius: 15px;
            position: relative;
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: rgb(45, 59, 165);
            color: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .timeline-date {
            font-size: 24px;
            font-weight: 700;
        }

        .timeline-stats {
            text-align: right;
            font-size: 14px;
        }

        .timeline-main {
            position: relative;
            padding-left: 80px;
            min-height: 600px; /* Asegurar altura mínima para el cálculo */
            width: 98%;
        }

        .timeline-axis {
            position: absolute;
            left: 40px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, #667eea, #764ba2);
            border-radius: 2px;
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.3);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 25px;
            padding: 20px 25px;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 5px solid transparent;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -52px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 4px solid white;
            z-index: 10;
        }

        .timeline-time {
            position: absolute;
            left: -125px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            font-weight: 700;
            color: #667eea;
            text-align: right;
            min-width: 60px;
        }

        /* Estados de las citas */
        .timeline-item.fulfilled {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-left-color: #28a745;
        }
        .timeline-item.fulfilled::before {
            background: #28a745;
        }

        .timeline-item.current {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-left-color: #ffc107;
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
            transform: scale(1.02);
        }
        .timeline-item.current::before {
            background: #ffc107;
            animation: pulse 1.5s infinite;
        }

        .timeline-item.checked-in {
            background: linear-gradient(135deg, #2E37A4, #0d6efd);
            border-left-color: #1621a1;
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
            transform: scale(1.02);
        }
        .timeline-item.checked-in::before {
            background: #0d6efd;
            animation: pulse 1.5s infinite;
        }


        .timeline-item.checked-in .profile-image a ,.timeline-item.checked-in .appointment-doctor , .timeline-item.checked-in .appointment-specialty,
        .timeline-item.checked-in .appointment-phone,.timeline-item.checked-in appointment-duration{
            color:#fff;
        }

        .timeline-item.next {
            background: linear-gradient(135deg, #cce5ff, #b3d9ff);
            border-left-color: #007bff;
            box-shadow: 0 12px 35px rgba(0, 123, 255, 0.4);
            transform: scale(1.05);
            border: 2px solid #007bff;
        }
        .timeline-item.next::before {
            position: absolute;
            left:-15px;
            background: #007bff;
            width: 24px;
            height: 24px;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.6);
        }

        .timeline-item.upcoming {
            background: linear-gradient(135deg, #e2e3f0, #d1d9ff);
            border-left-color: #6f42c1;
        }
        .timeline-item.upcoming::before {
            background: #6f42c1;
        }

        .timeline-item.overdue {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border-left-color: #dc3545;
        }
        .timeline-item.overdue::before {
            background: #dc3545;
        }

        .timeline-item.cancelled {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-left-color: #6c757d;
            opacity: 0.7;
        }
        .timeline-item.cancelled::before {
            background: #6c757d;
        }

        .timeline-item.scheduled {
            background: linear-gradient(135deg, #f0f8ff, #e6f3ff);
            border-left-color: #17a2b8;
        }
        .timeline-item.scheduled::before {
            background: #17a2b8;
        }

        .timeline-item.pending {
            background: linear-gradient(135deg, #FFA500, #d7a323);
            border-left-color: #d7a323;
        }
        .timeline-item.pending::before {
            background: #fff;
        }

        .timeline-item:hover {
            transform: translateX(10px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .timeline-item.next:hover {
            transform: translateX(10px) scale(1.07);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .appointment-patient {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }

        .appointment-status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .appointment-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .appointment-doctor {
            font-size: 16px;
            font-weight: 600;
            color: #34495e;
            margin-bottom: 5px;
        }

        .appointment-specialty {
            font-size: 14px;
            color: #7f8c8d;
            font-style: italic;
        }

        .appointment-contact {
            text-align: right;
        }

        .appointment-phone {
            font-size: 14px;
            color: #27ae60;
            font-weight: 500;
        }

        .appointment-duration {
            font-size: 12px;
            color: #95a5a6;
            margin-top: 5px;
        }

        .appointment-reason {
            background: rgba(255, 255, 255, 0.7);
            padding: 12px;
            border-radius: 8px;
            font-style: italic;
            color: #2c3e50;
            margin-bottom: 15px;
            border-left: 3px solid #3498db;
        }

        .appointment-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .btn-confirm { background: #3498db; color: white; }
        .btn-start { background: #f39c12; color: white; }
        .btn-complete { background: #27ae60; color: white; }
        .btn-cancel { background: #e8536e; color: white; }
        .btn-edit { background: #9b59b6; color: white; }

        .empty-timeline {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .empty-timeline-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .next-appointment-highlight {
            position: relative;
            overflow: hidden;
        }

        .next-appointment-highlight::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .timeline-legend {
            position: sticky;
            top: 0;
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            z-index: 50;
        }

        .legend-items {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        .daily-timeline-container {
            max-height: 700px;
            overflow-y: auto;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
            border-radius: 15px;
            position: relative;
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .timeline-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .auto-update-toggle {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .auto-update-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .auto-update-toggle.active {
            background: rgba(34, 197, 94, 0.3);
            border-color: rgba(34, 197, 94, 0.5);
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.4);
        }

        .live-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse-live 2s infinite;
        }

        @keyframes pulse-live {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        .time-display {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: 700;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .current-time-indicator {
            position: absolute;
            left: 20px;
            width: 44px;
            height: 44px;
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: 700;
            border: 4px solid white;
            box-shadow: 0 0 20px rgba(231, 76, 60, 0.6);
            z-index: 100;
            transition: top 0.5s ease-in-out;
        }

        .current-time-indicator.updating {
            animation: pulse-update 0.8s ease-in-out;
        }

        @keyframes pulse-update {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); box-shadow: 0 0 30px rgba(231, 76, 60, 0.9); }
            100% { transform: scale(1); }
        }



        /* Resto de estilos del timeline igual que antes... */

        .update-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(45deg, #22c55e, #16a34a);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .update-notification.show {
            transform: translateX(0);
        }

        .next-appointment-pulse {
            animation: next-appointment-glow 3s infinite;
        }

        @keyframes next-appointment-glow {
            0%, 100% { box-shadow: 0 12px 35px rgba(0, 123, 255, 0.4); }
            50% { box-shadow: 0 15px 40px rgba(0, 123, 255, 0.6); }
        }

        .debug-info {
            position: fixed;
            top: 10px;
            left: 10px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            z-index: 9999;
            display: none;
        }

        .debug-info.show {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .timeline-main {
                padding-left: 0px;
            }

            .timeline-time {
                left: -110px;
                font-size: 12px;
                display: none;
            }

            .timeline-legend{
                position: relative;
            }

            .appointment-details {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .appointment-contact {
                text-align: left;
            }

            .timeline-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .timeline-axis ,  .current-time-indicator {
                display: none;
            }

        }
    </style>

    <!-- Header del Timeline -->
    <div class="timeline-header">
        <div>
            <div class="timeline-date">
                {{ $currentPeriod }}
            </div>
            @if($calendarData['isToday'])
                <div style="font-size: 14px; opacity: 0.9; margin-top: 5px; display: flex; align-items: center; gap: 10px;">
                    📅 Hoy
                    <div class="time-display" x-text="currentTime">{{ now()->format('H:i:s') }}</div>
                </div>
            @endif
        </div>
        <div class="timeline-stats">
            <div>{{ count($calendarData['appointments']) }} citas programadas</div>
            @if($calendarData['nextAppointment'])
                <div style="margin-top: 5px; font-weight: 600;">
                    🔔 Próxima: {{ date('H:i', strtotime($calendarData['nextAppointment']['start'])) }}
                </div>
            @endif
        </div>
        @if($calendarData['isToday'])
            <div class="timeline-controls">
                <button
                    wire:click="toggleAutoUpdate"
                    class="auto-update-toggle {{ $autoUpdateEnabled ? 'active' : '' }}"
                    title="{{ $autoUpdateEnabled ? 'Desactivar actualización automática' : 'Activar actualización automática' }}">
                    @if($autoUpdateEnabled)
                        <div class="live-indicator"></div>
                        <span>EN VIVO</span>
                    @else
                        <span>⏸️ PAUSADO</span>
                    @endif
                </button>
                <button
                    wire:click="refreshTimePosition"
                    class="auto-update-toggle"
                    title="Actualizar ahora">
                    🔄 Actualizar
                </button>
            </div>
        @endif
    </div>

    <!-- Leyenda -->
    <div class="timeline-legend">
        <div class="legend-items">
            <div class="legend-item">
                <div class="legend-dot" style="background: #007bff; box-shadow: 0 0 10px rgba(0, 123, 255, 0.6);"></div>
                <span>Próxima Cita</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: #FFA500;"></div>
                <span>Pendiente</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: #ffc107;"></div>
                <span>En Progreso</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: #2E37A4;"></div>
                <span>En Curso</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: #28a745;"></div>
                <span>Completada</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: #6f42c1;"></div>
                <span>Próximos 15 min</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: #17a2b8;"></div>
                <span>Programada</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background: #dc3545;"></div>
                <span>Retrasada</span>
            </div>
        </div>
    </div>

    @if(count($calendarData['appointments']) > 0)
        <!-- Timeline Principal -->
        <div class="timeline-main">
            <!-- Eje del timeline -->
            <div class="timeline-axis"></div>

            <!-- Indicador de tiempo actual -->

            <!-- Indicador de tiempo actual -->
            @if($calendarData['isToday'] && $calendarData['currentTimePosition'] !== null)
                <div
                    class="current-time-indicator"
                    x-bind:class="{ 'updating': isUpdating }"
                    x-bind:style="`top: ${calculateIndicatorPosition()}px`"
                    x-text="currentTime.substring(0, 5)">
                </div>
            @endif


            <!-- Citas del día -->
            @foreach($calendarData['appointments'] as $index => $appointment)
                @php
                    $isNext = $index === $calendarData['nextAppointmentIndex'];
                    $status = $this->getAppointmentStatus($appointment, $isNext);
                    $appModel = \App\Models\Appointment::find($appointment['id']);
                @endphp

                <div class="timeline-item {{ $status }} {{ $isNext ? 'next-appointment-pulse' : '' }}" wire:click="editAppointment({{ $appointment['id'] }})">

                    <div class="timeline-time">

                        {{ $appointment['formatted_start_time']  }}
                    </div>

                    <div class="appointment-header">
                        <div class="appointment-patient">
                            {!!  \App\Models\Patient::find($appointment['patient']['id'])->profile_name!!}

                        </div>
                        <div class="appointment-status-badge" style="background:
                            @if($status === 'next') #007bff
                            @elseif($status === 'current') #ffc107
                            @elseif($status === 'pending') #408f2d
                            @elseif($status === 'fulfilled') #28a745
                            @elseif($status === 'overdue') #dc3545
                            @elseif($status === 'upcoming') #6f42c1
                            @elseif($status === 'cancelled') #6c757d
                            @else #17a2b8 @endif; color: white;">
                            @if($status === 'next') 🔔 PRÓXIMA
                            @elseif($status === 'pending') 🕐 PENDIENTE DE CONFIRMACION
                            @elseif($status === 'current') ⏱️ EN PROGRESO
                            @elseif($status === 'checked-in') 🏥 CONSULTA EN CURSO
                            @elseif($status === 'fulfilled') ✅ COMPLETADA
                            @elseif($status === 'overdue') ⚠️ RETRASADA
                            @elseif($status === 'upcoming') 🕐 PRÓXIMA (15min)
                            @elseif($status === 'cancelled') ❌ CANCELADA
                            @else 📅 PROGRAMADA @endif
                        </div>
                    </div>

                    <div class="appointment-details">
                        <div>
                            <div class="appointment-doctor">
                                👨‍⚕️ {{ $appointment['practitioner']['name'] }}
                            </div>
                            <div class="appointment-specialty">
                                {{ $appointment['medical_speciality']['name'] }}
                            </div>
                        </div>
                        <div class="appointment-contact">
                            @if($appointment['patient']['phone'])
                                <div class="appointment-phone">
                                    📞 {{ $appointment['patient']['phone'] }}
                                </div>
                            @endif
                            <div class="appointment-duration">
                                ⏱️ {{ $appointment['minutes_duration'] }} minutos
                            </div>
                        </div>
                    </div>

                    @if($appointment['description'])
                        <div class="appointment-reason">
                            <strong>Motivo:</strong> {{ $appointment['description'] }}
                        </div>
                    @endif

                    <div class="appointment-actions">
                        @if(auth()->user()->can('arrived',$appModel))
                            <button wire:click.stop="updateStatus({{ $appointment['id'] }}, 'arrived')" class="action-btn btn-start">
                                🚪 Registrar Llegada
                            </button>
                        @endif

                        @if(auth()->user()->can('checked_in',$appModel))
                            <button wire:click.stop="updateStatus({{ $appointment['id'] }}, 'checked-in')" class="action-btn btn-start">
                                ▶️ Iniciar
                            </button>
                        @endif

                        @if(auth()->user()->can('fulfilled',$appModel))
                            <button wire:click.stop="updateStatus({{ $appointment['id'] }}, 'fulfilled')" class="action-btn btn-complete">
                                ✅ Finalizar
                            </button>
                        @endif

                        @if(auth()->user()->can('viewConsultation',$appModel))
                            <a href="{{route('consultation.show',$appointment['id'])}}" class="action-btn btn-start">
                                ▶️ Ver Consulta
                            </a>
                        @endif

                        @if(auth()->user()->can('cancelled',$appModel))
                            <button wire:click.stop="updateStatus({{ $appointment['id'] }}, 'canceled')"  class="action-btn btn-cancel">
                                ❌ Cancelar
                            </button>
                        @endif

                        @if(auth()->user()->can('noshow',$appModel))
                            <button wire:click.stop="updateStatus({{ $appointment['id'] }}, 'noshow')"  class="action-btn btn-cancel">
                                👻 No aparecio
                            </button>
                        @endif
                        @if(auth()->user()->can('edit',$appModel))
                            <div class="float-right">
                                <button wire:click.stop="editAppointment({{ $appointment['id'] }})"  class="action-btn btn-edit">
                                    ✏️ Editar
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <script>
                    document.addEventListener('livewire:initialized', () => {
                        Livewire.on('showToastr{{$appointment['id']}}', (event) => {
                            toastr[event.type](event.message, '', {
                                closeButton: true,
                                progressBar: true,
                                positionClass: 'toast-top-right',
                                timeOut: 5000,
                                onHidden: function() {
                                    window.location.href = '{{route('consultation.show',$appointment['id'])}}'; // Replace with your desired URL
                                }
                            });
                        });
                    });
                </script>
            @endforeach
        </div>
    @else
        <!-- Estado vacío -->
        <div class="empty-timeline">
            <div class="empty-timeline-icon">📅</div>
            <h3 style="color: #7f8c8d; margin-bottom: 10px;">No hay citas programadas</h3>
            <p style="color: #95a5a6;">
                @if($calendarData['isToday'])
                    ¡Perfecto! No tienes citas para hoy.
                @else
                    No hay citas programadas para este día.
                @endif
            </p>
            <button wire:click="openModal('{{ $calendarData['date']->format('Y-m-d') }}')"
                    class="btn btn-primary" style="margin-top: 20px;">
                + Programar Nueva Cita
            </button>
        </div>
    @endif
    <!-- Script Alpine.js para manejo del tiempo -->
    <script>
        function timelineManager() {
            return {
                currentTime: '{{ now()->format("H:i:s") }}',
                timePosition: {{ $calendarData['currentTimePosition'] ?? 0 }},
                timelineHeight: 600,
                isUpdating: false,
                updateInterval: null,
                showNotification: false,
                notificationMessage: '',
                showDebug: false,
                autoUpdateEnabled: @entangle('autoUpdateEnabled'),

                init() {
                    console.log('Timeline Manager initialized');

                    // Esperar a que el DOM esté listo
                    this.$nextTick(() => {
                        this.calculateTimelineHeight();
                        this.updateTime();
                    });

                    // Escuchar eventos de Livewire
                    this.$wire.on('timePositionUpdated', (data) => {
                        console.log('Time position update received:', data);
                        this.handleTimeUpdate(data);
                    });

                    this.$wire.on('startAutoUpdate', () => {
                        this.startAutoUpdate();
                    });

                    this.$wire.on('stopAutoUpdate', () => {
                        this.stopAutoUpdate();
                    });

                    // Iniciar auto-update si está habilitado
                    if (this.autoUpdateEnabled) {
                        this.startAutoUpdate();
                    }

                    // Recalcular altura cuando cambie el tamaño
                    window.addEventListener('resize', () => {
                        this.calculateTimelineHeight();
                    });
                },

                calculateTimelineHeight() {
                    const timelineMain = document.querySelector('.timeline-main');
                    if (timelineMain) {
                        const items = timelineMain.querySelectorAll('.timeline-item');
                        if (items.length > 0) {
                            const firstItem = items[0];
                            const lastItem = items[items.length - 1];
                            this.timelineHeight = (lastItem.offsetTop + lastItem.offsetHeight) - firstItem.offsetTop;
                        }
                    }
                },

                calculateIndicatorPosition() {
                    const timelineItems = document.querySelectorAll('.timeline-item');

                    if (!timelineItems.length) {
                        return 0;
                    }

                    // Obtener posición del primer y último elemento
                    const firstItem = timelineItems[0];
                    const lastItem = timelineItems[timelineItems.length - 1];

                    const startY = firstItem.offsetTop;
                    const endY = lastItem.offsetTop + lastItem.offsetHeight;
                    const totalHeight = endY - startY;

                    // Calcular posición basada en el porcentaje
                    const indicatorY = startY + (totalHeight * (this.timePosition / 100));

                    // Ajustar para centrar el indicador en la cita actual
                    const adjustedY = indicatorY - 22; // 22px es la mitad del tamaño del indicador

                    return Math.max(0, Math.round(adjustedY));
                },

                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('es-ES', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                },

                startAutoUpdate() {
                    if (this.updateInterval) {
                        clearInterval(this.updateInterval);
                    }

                    // Actualizar cada segundo
                    this.updateInterval = setInterval(() => {
                        this.updateTime();

                        // Actualizar posición cada 30 segundos
                        if (new Date().getSeconds() % 30 === 0) {
                            console.log('Triggering server time position update');
                            this.$wire.call('refreshTimePosition');
                        }
                    }, 1000);

                    this.showNotificationMessage('⏰ Actualización automática activada');
                },

                stopAutoUpdate() {
                    if (this.updateInterval) {
                        clearInterval(this.updateInterval);
                        this.updateInterval = null;
                    }
                    this.showNotificationMessage('⏸️ Actualización automática pausada');
                },

                handleTimeUpdate(data) {
                    console.log('Handling time update:', data);

                    this.isUpdating = true;

                    // Actualizar posición con los datos del servidor
                    if (data.position !== undefined && data.position !== null) {
                        this.timePosition = parseFloat(data.position);
                        console.log('Position updated from server:', this.timePosition);
                    }

                    // Actualizar tiempo
                    if (data.currentTime) {
                        const seconds = new Date().getSeconds().toString().padStart(2, '0');
                        this.currentTime = data.currentTime + ':' + seconds;
                    }

                    // Debug info
                    if (data.debug) {
                        console.log('Debug info:', data.debug);
                    }

                    // Remover indicador de actualización
                    setTimeout(() => {
                        this.isUpdating = false;
                    }, 800);

                    this.showNotificationMessage(`🔄 Posición actualizada: ${this.timePosition.toFixed(1)}%`);
                },

                showNotificationMessage(message) {
                    this.notificationMessage = message;
                    this.showNotification = true;

                    setTimeout(() => {
                        this.showNotification = false;
                    }, 3000);
                },

                destroy() {
                    if (this.updateInterval) {
                        clearInterval(this.updateInterval);
                    }
                }
            }
        }
    </script>
</div>
