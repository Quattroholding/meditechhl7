<div class="medical-history-container">
    <style>
        .medical-history-container {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            /*background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);*/
            min-height: 100vh;
            /*padding: 20px;*/
        }

        .patient-header {
            /*background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);*/
            background: #003b62;
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .patient-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50%, -50%);
        }

        .patient-info {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 30px;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .patient-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            border: 4px solid rgba(255, 255, 255, 0.3);
        }

        .patient-details h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .patient-meta {
            font-size: 16px;
            opacity: 0.9;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .patient-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-light {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-light:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .main-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
            max-width: 1800px;
            margin: 0 auto;
        }

        .sidebar_medical {
            background: white;
            border-radius: 20px;
            padding: 30px;
            height: fit-content;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 20px;
        }

        .section-nav {
            list-style: none;
            padding: 0;
        }

        .nav-item {
            margin-bottom: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            border-radius: 12px;
            text-decoration: none;
            color: #64748b;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .nav-link:hover {
            background: #f1f5f9;
            color: #334155;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, #003b62, #764ba2);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .nav-icon {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        .nav-text {
            flex: 1;
        }

        .nav-count {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .nav-link.active .nav-count {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .content-area {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .content-header {
            padding: 30px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        }

        .header-top {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filters-section {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .content-body {
            padding: 30px;
        }

        .loading-state {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            color: #64748b;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e2e8f0;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Overview Grid */
        .overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: #667eea;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .stat-title {
            font-weight: 600;
            color: #334155;
            font-size: 16px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .stat-subtitle {
            font-size: 14px;
            color: #64748b;
        }

        /* Timeline */
        .timeline-section {
            margin-top: 30px;
        }

        .timeline-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .timeline {
            position: relative;
            padding-left: 40px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #667eea, #764ba2);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 25px;
            background: white;
            border: 2px solid #f1f5f9;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .timeline-item:hover {
            border-color: #667eea;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.1);
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -47px;
            top: 25px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #667eea;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #667eea;
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .timeline-title-item {
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .timeline-date {
            font-size: 12px;
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .timeline-content {
            color: #64748b;
            line-height: 1.5;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table th {
            background: #f8fafc;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #334155;
            border-bottom: 2px solid #e2e8f0;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            color: #64748b;
        }

        .data-table tr:hover {
            background: #f8fafc;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-active { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #f3f4f6; color: #6b7280; }
        .badge-resolved { background: #dbeafe; color: #1e40af; }
        .badge-pending { background: #fef3c7; color: #92400e; }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .sidebar_medical {
                position: static;
            }

            .patient-info {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 20px;
            }

            .overview-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .medical-history-container {
                padding: 10px;
            }

            .patient-header {
                padding: 20px;
            }

            .content-header {
                padding: 20px;
            }

            .content-body {
                padding: 20px;
            }

            .filters-section {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>

    <livewire:patient.patient-head patient_id="{{$patient->id}}"/>


    <!-- Contenido Principal -->
    <div class="main-content">
        <!-- sidebar_medical de Navegación -->
        <div class="sidebar_medical">
            <ul class="section-nav">
                <li class="nav-item">
                    <div wire:click="changeSection('overview')"
                         class="nav-link {{ $activeSection === 'overview' ? 'active' : '' }}">
                        <span class="nav-icon">📚</span>
                        <span class="nav-text">General</span>
                    </div>
                </li>
                <li class="nav-item">
                    <div wire:click="changeSection('encounters')"
                         class="nav-link {{ $activeSection === 'encounters' ? 'active' : '' }}">
                        <span class="nav-icon">🏥</span>
                        <span class="nav-text">Consultas</span>
                        <span class="nav-count">{{ $overviewData['total_encounters'] ?? 0 }}</span>
                    </div>
                </li>
                <li class="nav-item">
                    <div wire:click="changeSection('conditions')"
                         class="nav-link {{ $activeSection === 'conditions' ? 'active' : '' }}">
                        <span class="nav-icon">🩺</span>
                        <span class="nav-text">Diagnosticos</span>
                        <span class="nav-count">{{ $overviewData['active_conditions'] ?? 0 }}</span>
                    </div>
                </li>
                <li class="nav-item">
                    <div wire:click="changeSection('physical-exams')"
                         class="nav-link {{ $activeSection === 'physical-exams' ? 'active' : '' }}">
                        <span class="nav-icon">🔍</span>
                        <span class="nav-text">Exámenes Físicos</span>
                    </div>
                </li>
                <li class="nav-item">
                    <div wire:click="changeSection('vital-signs')"
                         class="nav-link {{ $activeSection === 'vital-signs' ? 'active' : '' }}">
                        <span class="nav-icon">❤️</span>
                        <span class="nav-text">Signos Vitales</span>
                    </div>
                </li>
                <li class="nav-item">
                    <div wire:click="changeSection('present-illnesses')"
                         class="nav-link {{ $activeSection === 'present-illnesses' ? 'active' : '' }}">
                        <span class="nav-icon">🤒</span>
                        <span class="nav-text">Enfermedad Actual</span>
                    </div>
                </li>
                <li class="nav-item">
                    <div wire:click="changeSection('medical-requests')"
                         class="nav-link {{ $activeSection === 'medical-requests' ? 'active' : '' }}">
                        <span class="nav-icon">📋</span>
                        <span class="nav-text">Órdenes Médicas</span>
                        <span class="nav-count">{{ $overviewData['total_requests'] ?? 0 }}</span>
                    </div>
                </li>
                {{--}}
                <li class="nav-item">
                    <div wire:click="changeSection('service-requests')"
                         class="nav-link {{ $activeSection === 'service-requests' ? 'active' : '' }}">
                        <span class="nav-icon">🧪</span>
                        <span class="nav-text">Solicitudes de Servicios</span>
                    </div>
                </li>
                {{--}}
                <li class="nav-item">
                    <div wire:click="changeSection('medical-histories')"
                         class="nav-link {{ $activeSection === 'medical-histories' ? 'active' : '' }}">
                        <span class="nav-icon">📚</span>
                        <span class="nav-text">Historial Previo</span>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Área de Contenido -->
        <div class="content-area">
            <div class="content-header">
                <div class="header-top">
                    <h2 class="section-title">
                        @switch($activeSection)
                            @case('overview')
                                📊 Resumen General
                                @break
                            @case('encounters')
                                🏥 Consultas Médicas
                                @break
                            @case('vital-signs')
                                ❤️ Signos Vitales
                                @break
                            @case('conditions')
                                🩺 Condiciones Médicas
                                @break
                            @case('physical-exams')
                                🔍 Exámenes Físicos
                                @break
                            @case('present-illnesses')
                                🤒 Enfermedad Actual
                                @break
                            @case('medical-requests')
                                📋 Órdenes Médicas
                                @break
                            @case('service-requests')
                                🧪 Solicitudes de Servicios
                                @break
                            @case('medical-histories')
                                📚 Historial Médico Previo
                                @break
                        @endswitch
                    </h2>

                    @if($activeSection !== 'overview')
                        <div class="float-right">
                            <button wire:click="toggleFilters" class="btn btn-light" style="color: #667eea; background: white; border: 2px solid #667eea;">
                                🔍 Filtros
                            </button>
                        </div>

                    @endif
                </div>

                @if($showFilters && $activeSection !== 'overview')
                    <div class="filters-section">
                        <div class="filter-group">
                            <label class="filter-label">Período</label>
                            <select wire:model.live="selectedTimeRange" class="form-control">
                                <option value="all">Todos</option>
                                <option value="today">Hoy</option>
                                <option value="week">Esta Semana</option>
                                <option value="month">Este Mes</option>
                                <option value="year">Este Año</option>
                                <option value="custom">Personalizado</option>
                            </select>
                        </div>

                        @if($selectedTimeRange === 'custom')
                            <div class="filter-group">
                                <label class="filter-label">Desde</label>
                                <input wire:model.live="dateFrom" type="date" class="form-control">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">Hasta</label>
                                <input wire:model.live="dateTo" type="date" class="form-control">
                            </div>
                        @endif

                        <div class="filter-group">
                            <label class="filter-label">Buscar</label>
                            <input wire:model.live.debounce.300ms="searchTerm"
                                   type="text"
                                   placeholder="Buscar en registros..."
                                   class="form-control">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">&nbsp;</label>
                            <button wire:click="clearFilters" class="btn" style="background: #f59e0b; color: white;">
                                🗑️ Limpiar
                            </button>
                        </div>
                    </div>
                @endif
            </div>
            <div class="content-body">
                @if($isLoading)
                    <div class="loading-state">
                        <div class="loading-spinner"></div>
                        <span>Cargando información médica...</span>
                    </div>
                @else
                    @switch($activeSection)
                        @case('overview')
                            @include('patients.medicalHistory.overview')
                            @break
                        @case('encounters')
                            @include('patients.medicalHistory.encounters')
                            @break
                        @case('vital-signs')
                            @include('patients.medicalHistory.vital-signs')
                            @break
                        @case('conditions')
                            @include('patients.medicalHistory.conditions')
                            @break
                        @case('physical-exams')
                            @include('patients.medicalHistory.physical-exams')
                            @break
                        @case('present-illnesses')
                            @include('patients.medicalHistory.present-illnesses')
                            @break
                        @case('medical-requests')
                            @include('patients.medicalHistory.medical-requests')
                            @break
                        @case('service-requests')
                            @include('patients.medicalHistory.service-requests')
                            @break
                        @case('medical-histories')
                            @include('patients.medicalHistory.medical-histories')
                            @break
                    @endswitch
                @endif
            </div>
        </div>
    </div>

</div>
