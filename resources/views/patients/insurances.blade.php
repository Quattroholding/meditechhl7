<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('patient.titles') }}

                @endslot
                @slot('li_1')
                    {{ __('generic.list') }} {{ __('patient.insurances') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card card-table show-entire">
                                        <div class="card-body">
                                            <!-- Table Header -->

                                            <!-- /Table Header -->
                                            @include('partials.message')
                                            @livewire('patient.add-insurance', ['patient_id' => $patient->id])

                                            <div class="table-responsive">
                                                <table class="table border-0 custom-table comman-table mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th>Prioridad</th>
                                                        <th>Compañía</th>
                                                        <th>Número de Póliza</th>
                                                        <th>Titular</th>
                                                        <th>Cobertura</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($patient->insurancePolicies as $policy)
                                                        <tr>
                                                            <td>
                                <span class="badge badge-{{ $policy->priority == 'primary' ? 'primary' : ($policy->priority == 'secondary' ? 'secondary' : 'info') }}">
                                    {{ ucfirst($policy->priority) }}
                                </span>
                                                            </td>
                                                            <td>{{ $policy->insuranceCompany->name ?? 'N/A' }}</td>
                                                            <td>{{ $policy->policy_number }}</td>
                                                            <td>{{ $policy->subscriber_name }}</td>
                                                            <td>{{ $policy->coverage_percentage }}%</td>
                                                            <td>
                                                                @if($policy->is_active && !$policy->isExpired())
                                                                    <span class="badge badge-success">Activo</span>
                                                                @elseif($policy->isExpired())
                                                                    <span class="badge badge-warning">Expirado</span>
                                                                @else
                                                                    <span class="badge badge-secondary">Inactivo</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="actions">
                                                                    <a class="btn btn-sm bg-success-light" href="#" title="Ver detalles">
                                                                        <i class="fe fe-eye"></i>
                                                                    </a>
                                                                    <a class="btn btn-sm bg-info-light" href="#" title="Editar">
                                                                        <i class="fe fe-edit"></i>
                                                                    </a>
                                                                    <a class="btn btn-sm bg-danger-light" href="#" title="Eliminar">
                                                                        <i class="fe fe-trash"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
