<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Dashboard
                @endslot
                @slot('li_1')
                    Admin Dashboard
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="good-morning-blk">
                <div class="row">
                    <div class="col-md-6">
                        <div class="morning-user">
                            <h2>{{__('generic.hi')}}, <span>{{auth()->user()->full_name}}</span></h2>
                            <p>{{__('generic.welcome')}}</p>
                            <button class="btn btn-primary" onclick="refreshAllKpis()">
                                <i class="fa fa-refresh"></i> Actualizar Todos
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 position-blk">
                        <div class="morning-img">
                            <img src="{{ URL::asset('/assets/img/morning-img-01.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI Grid -->
            <div class="row">
                <!-- Today Appointments KPI -->
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                    <div class="card kpi-card">
                        <div class="card-header">
                            <h5><i class="fa fa-calendar text-primary"></i> Citas de Hoy</h5>
                        </div>
                        <div class="card-body">
                            @livewire('dashboard.admin.today-appointments')
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue KPI -->
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                    <div class="card kpi-card">
                        <div class="card-header">
                            <h5><i class="fa fa-dollar-sign text-primary"></i> Ingresos Mensuales</h5>
                        </div>
                        <div class="card-body">
                            @livewire('dashboard.admin.monthly-revenue')
                        </div>
                    </div>
                </div>

                <!-- Active Patients KPI -->
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                    <div class="card kpi-card">
                        <div class="card-header">
                            <h5><i class="fa fa-users text-secondary"></i> Pacientes Activos</h5>
                        </div>
                        <div class="card-body">
                            @livewire('dashboard.admin.active-patients')
                        </div>
                    </div>
                </div>

                <!-- Top Specialties KPI -->
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                    <div class="card kpi-card">
                        <div class="card-header">
                            <h5><i class="fa fa-star text-tertiary"></i> Especialidades Más Demandadas</h5>
                        </div>
                        <div class="card-body">
                            @livewire('dashboard.admin.top-specialties')
                        </div>
                    </div>
                </div>

                <!-- Practitioner Performance KPI -->
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                    <div class="card kpi-card">
                        <div class="card-header">
                            <h5><i class="fa fa-user-md text-secondary"></i> Rendimiento Médicos</h5>
                        </div>
                        <div class="card-body">
                            @livewire('dashboard.admin.practitioner-performance')
                        </div>
                    </div>
                </div>

                <!-- Common Diagnoses KPI -->
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                    <div class="card kpi-card">
                        <div class="card-header">
                            <h5><i class="fa fa-stethoscope text-tertiary"></i> Diagnósticos Frecuentes</h5>
                        </div>
                        <div class="card-body">
                            @livewire('dashboard.admin.common-diagnoses')
                        </div>
                    </div>
                </div>

                <!-- Resource Utilization KPI -->
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12">
                    <div class="card kpi-card">
                        <div class="card-header">
                            <h5><i class="fa fa-building text-primary"></i> Utilización de Recursos</h5>
                        </div>
                        <div class="card-body">
                            @livewire('dashboard.admin.resource-utilization')
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<style>
.kpi-card {
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: none;
    border-radius: 8px;
}

.kpi-card .card-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-bottom: 1px solid var(--primary-light);
    border-radius: 8px 8px 0 0;
}

.kpi-card .card-header h5 {
    margin: 0;
    font-weight: 600;
    color: #ffffff;
}

.dash-header {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.text-purple {
    color: var(--tertiary) !important;
}

.loading-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100px;
}

.kpi-metric {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.kpi-label {
    color: #6c757d;
    font-size: 0.9rem;
}
</style>

<script>
function refreshAllKpis() {
    // Trigger refresh for all Livewire components
    Livewire.dispatch('refreshData');

    // Show loading indicator
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Actualizando...';
    btn.disabled = true;

    // Re-enable button after 3 seconds
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 3000);
}
</script>
</x-app-layout>
