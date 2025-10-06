<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">Menú</li>
                @can('dashboard.admin')
                    <li class="menu-side">
                        <a class="{{ Request::is('dashboard') ? 'active' : '' }}"  href="{{ route('admin.dashboard') }}"><span class="menu-side" >
                            <i class="fa fa-chart-bar"></i></span>
                            <span> Dashboard </span>
                        </a>
                    </li>
                @endcan
                @can('dashboard.doctor')
                    <li class="menu-side">
                        <a class="{{ Request::is('dashboard/doctor') ? 'active' : '' }}"  href="{{ route('doctor.dashboard') }}"><span class="menu-side" >
                                <i class="fa fa-chart-bar"></i></span>
                            <span> Dashboard </span>
                        </a>
                    </li>
                @endcan
                @can('dashboard.patient')
                <li>
                    <a class="{{ Request::is('dashboard/patient') ? 'active' : '' }}"  href="{{ route('patient.dashboard') }}">
                        <span class="menu-side"> <i class="fa fa-chart-bar"></i></span>&nbsp;
                        <span>Dashboard</span>
                    </a>
                </li>
                @endcan
                @can('dashboard.client')
                <li class="menu-side">
                    <a class="{{ Request::is('dashboard/client') ? 'active' : '' }}"  href="{{ route('client.dashboard') }}"><span class="menu-side" >
                            <i class="fa fa-chart-bar"></i></span>
                        <span> Dashboard </span>
                    </a>
                </li>
                @endcan
                @can('dashboard.assistence')
                    <li class="menu-side">
                        <a class="{{ Request::is('dashboard/assistence') ? 'active' : '' }}"  href="{{ route('assistence.dashboard') }}"><span class="menu-side" >
                            <i class="fa fa-chart-bar"></i></span>
                            <span> Dashboard </span>
                        </a>
                    </li>
                @endcan
                @canany(['clients.view', 'clients.create', 'branches.view', 'branches.create'])
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side primary">
                            <i class="fa fa-location"></i></span>
                            <span> {{ __('client.locations') }} </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        @can('clients.view')
                        <li><a class="{{ Request::is('clients') ? 'active' : '' }}" href="{{ route('client.index') }}">{{ __('generic.list') }} {{ __('client.titles') }}  </a></li>
                        @endcan
                        @can('clients.create')
                        <li><a class="{{ Request::is('clients/create') ? 'active' : '' }}"   href="{{ route('client.create') }}">{{ __('generic.create') }} {{ __('client.title') }}</a></li>
                        @endcan
                        @can('branches.view')
                        <li><a class="{{ Request::is('clients/branch') ? 'active' : '' }}"   href="{{ route('client.branch.index') }}">{{ __('generic.list') }} {{ __('client.branches') }}</a></li>
                        @endcan
                        @can('rooms.view')
                        <li><a class="{{ Request::is('clients/consulting_rooms') ? 'active' : '' }}"   href="{{ route('client.room.index') }}">{{ __('generic.list') }} {{ __('client.rooms') }}</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany
                @canany(['practitioners.view','practitioners.create'])
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                        <i class="fa fa-user-md"></i></span>
                        <span>{{ __('doctor.titles') }} </span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        @can('practitioners.view')
                        <li><a class="{{ Request::is('practitioners') ? 'active' : '' }}"  href="{{ route('practitioner.index') }}">{{ __('generic.list') }} {{ __('doctor.titles') }}</a></li>
                        @endcan
                        @can('practitioners.create')
                        <li><a class="{{ Request::is('practitioners/create') ? 'active' : '' }}"   href="{{ route('practitioner.create') }}">{{ __('generic.create') }} {{ __('doctor.title') }}</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany
                @canany(['patients.view','patients.create'])
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                        <i class="fa fa-user-injured"></i></span>
                        <span>{{ __('patient.titles') }} </span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        @can('patients.view')
                        <li><a class="{{ Request::is('patients') ? 'active' : '' }}"  href="{{ route('patient.index') }}">{{ __('generic.list') }} {{ __('patient.titles') }}</a></li>
                        @endcan
                        @can('patients.create')
                        <li><a class="{{ Request::is('patients/create') ? 'active' : '' }}"   href="{{ route('patient.create') }}">{{ __('generic.create') }} {{ __('patient.title') }}</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany
                @canany(['appointments.view','appointments.calendar'])
                <li class="submenu">
                    <a href="javascript:;">
                        <span class="menu-side"><i class="fa fa-calendar-alt"></i></span>
                        <span>  {{ __('appointment.titles') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        @can('appointments.view')
                        <li><a class="{{ Request::is('appointments') ? 'active' : '' }}" href="{{ route('appointment.index') }}">{{ __('generic.list') }} {{ __('appointment.titles') }}</a></li>
                        @endif
                        @can('appointments.calendar')
                        <li><a class="{{ Request::is('appointments/calendar') ? 'active' : '' }}" href="{{ route('appointment.calendar') }}">{{ __('Calendario') }} </a></li>
                        @endif
                    </ul>
                </li>
                @endcanany
                @can('consultations.view')
                <li class="submenu">
                    <a href="javascript:;">
                        <span class="menu-side"> <i class="fa fa-stethoscope"></i></span>
                        <span>  {{ __('encounter.titles') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('consultation') ? 'active' : '' }}" href="{{ route('consultation.index') }}">{{ __('generic.list') }} {{ __('encounter.titles') }}</a></li>
                    </ul>
                </li>
                @endcan
                @can('service_request.view')
                <li class="submenu">
                    <a href="javascript:;">
                        <span class="menu-side"> <i class="fa fa-tasks"></i></span>
                        <span>  Solicitudes de <br/>Servicio </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('service_requests') ? 'active' : '' }}" href="{{ route('service_request.index') }}">Lista de Solicitudes</a></li>
                    </ul>
                </li>
                @endcan
                @canany(['medicines.view','medicines.create'])
                <li class="submenu">
                    <a href="javascript:;">
                        <span class="menu-side">
                            <i class="fa fa-pills"></i></span>
                        <span> {{__('Medicamentos')}} </span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        @can('medicines.view')
                        <li><a class="{{ Request::is('medicines') ? 'active' : '' }}" href="{{route('medicine.index')}}">{{ __('generic.list') }} {{__('Medicamentos')}}</a></li>
                        @endcan
                        {{--}}
                        @can('medicines.create')
                        <li><a class="{{ Request::is('medicines/create') ? 'active' : '' }}" href="{{route('medicine.create')}}">{{ __('generic.create') }} {{ __('Medicamento') }}</a></li>
                        @endcan
                        {{--}}
                    </ul>
                </li>
                @endcanany
                @canany(['surveys.view','surveys.create'])
                <li class="submenu">
                    <a href="javascript:;">
                        <span class="menu-side">
                            <i class="fa fa-poll"></i></span>
                        <span> Encuestas </span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        @can('surveys.view')
                        <li><a class="{{ Request::is('surveys') ? 'active' : '' }}" href="{{ route('surveys.index') }}">Lista de Encuestas</a></li>
                        @endcan
                        @can('surveys.create')
                        <li><a class="{{ Request::is('surveys/create') ? 'active' : '' }}" href="{{ route('surveys.create') }}">Crear Encuesta</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany
                @canany(['invoices.view','payments.view'])
                <li class="submenu">
                    <a href="javascript:;">
                        <span class="menu-side">
                            <i class="fa fa-file-invoice-dollar"></i></span>
                        <span> {{__('Cuentas')}} </span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        @can('invoices.view')
                        <li><a class="{{ Request::is('accounts/invoices') ? 'active' : '' }}" href="{{route('invoice.index')}}">{{__('Facturas')}}</a></li>
                        @endcan
                        @can('payments.view')
                        <li><a class="{{ Request::is('accounts/payments') ? 'active' : '' }}" href="{{route('payment.index')}}">{{__('Pagos')}}</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany
                @canany(['settings.create_user_procedures','settings.create_consultation_template','settings.create_rapid_access','settings.create_working_hour_user'])
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                            <i class="fa fa-cogs"></i></span>
                        <span> {{__('Configuraciones')}} </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        @can('settings.create_user_procedures')
                        <li><a class="{{ Request::is('settings/create_user_procedures') ? 'active' : '' }}"  href="{{ route('setting.create_user_procedures') }}">{{ __('Servicios') }}</a></li>
                        @endcan
                        @can('settings.create_consultation_template')
                        <li><a class="{{ Request::is('settings/create_consultation_template') ? 'active' : '' }}"  href="{{ route('setting.create_template') }}">{{ __('Plantilla Consulta') }}</a></li>
                        @endcan
                        @can('settings.create_rapid_access')
                        <li><a class="{{ Request::is('settings/create_rapid_access') ? 'active' : '' }}"  href="{{ route('setting.create_rapid_access') }}">{{ __('Accesos Rapidos') }}</a></li>
                        @endcan
                        @can('settings.create_working_hour_user')
                        <li><a class="{{ Request::is('settings/create_working_hour_user') ? 'active' : '' }}"  href="{{ route('setting.create_working_hour_user') }}">{{ __('Horario Laboral') }}</a></li>
                        @endcan
                        @can('settings.signature_and_seal' && auth()->user()->practitioner)
                            <li><a class="{{ Request::is('settings/'.auth()->user()->practitioner->id.'/signature_and_seal') ? 'active' : '' }}"   href="{{ route('setting.signature_and_seal',auth()->user()->practitioner->id) }}">{{ __('doctor.signature-manager') }}</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany
                @can('practitioners.directory')
                <li>
                    <a class="{{ Request::is('practitioners/directory') ? 'active' : '' }}"  href="{{ route('practitioner.directory') }}">
                        <span class="menu-side"><i class="fa fa-user-md"></i></span>&nbsp;
                        <span>{{ __('patient.medical_directory') }}</span>
                    </a>
                </li>
                @endcan
                @if(auth()->user()->can('patients.medical_history') && auth()->user()->patient)
                <li>
                    <a class="{{ Request::is('patients/'.auth()->user()->patient->id.'/medical_history') ? 'active' : '' }}"  href="{{ route('patient.medical_history',auth()->user()->patient->id) }}">
                        <span class="menu-side"><i class="fa fa-file-medical"></i></span>&nbsp;
                        <span>{{ __('patient.medical_history') }}</span>
                    </a>
                </li>
                @endcan
                @canany(['users.view', 'users.create'])
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                            <i class="fa fa-users"></i></span>
                        <span> Usuarios </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        @can('users.view')
                        <li><a class="{{ Request::is('users') ? 'active' : '' }}"  href="{{ route('user.index') }}">{{ __('generic.list') }} {{ __('user.titles') }}</a></li>
                        @endcan
                        @can('users.create')
                        <li><a class="{{ Request::is('users/create') ? 'active' : '' }}"  href="{{ route('user.create') }}">{{ __('generic.create') }} {{ __('user.title') }}</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany
                @canany(['manage-packages'])
                    <li class="submenu">
                        <a href="javascript:;"><span class="menu-side">
                            <i class="fa fa-boxes-packing"></i></span>
                            <span> Paquetes </span> <span class="menu-arrow"></span></a>
                        <ul style="display: none;">
                                <li><a class="{{ Request::is('packages') ? 'active' : '' }}"  href="{{ route('package.index') }}">{{ __('generic.list') }} {{ __('package.titles') }}</a></li>
                                <li><a class="{{ Request::is('packages/create') ? 'active' : '' }}"  href="{{ route('package.create') }}">{{ __('generic.create') }} {{ __('package.title') }}</a></li>
                        </ul>
                    </li>
                @endcanany
                @if(auth()->user()->can('practitioners.profile') && auth()->user()->practitioner)
                <li>
                    <a class="{{ Request::is('practitioners/'.auth()->user()->practitioner->id.'/profile') ? 'active' : '' }}"  href="{{ route('practitioner.profile',auth()->user()->practitioner->id) }}">
                        <span class="menu-side"><i class="fa fa-cog"></i></span>&nbsp;
                        <span>{{ __('doctor.profile') }}</span>
                    </a>
                </li>
                @endcan
                @if(auth()->user()->can('patients.profile') && auth()->user()->patient)
                    <li>
                        <a class="{{ Request::is('patients/'.auth()->user()->patient->id.'/profile') ? 'active' : '' }}"  href="{{ route('patient.profile',auth()->user()->patient->id) }}">
                            <span class="menu-side"><i class="fa fa-cog"></i></span>&nbsp;
                            <span>{{ __('patient.profile') }}</span>
                        </a>
                    </li>
                @endcan
                @can('users.profile')
                    <li>
                        <a class="{{ Request::is('profile') ? 'active' : '' }}"  href="{{ route('profile.edit') }}">
                            <span class="menu-side"><i class="fa fa-cog"></i></span>&nbsp;
                            <span>{{ __('doctor.profile') }}</span>
                        </a>
                    </li>
                @endcan
                @role('admin')
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                            <i class="fa fa-key"></i></span>
                        <span> Tokens API </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('api-tokens') ? 'active' : '' }}"  href="{{ route('api-tokens.index') }}">Gestionar Tokens</a></li>
                        <li><a class="{{ Request::is('api-tokens/create') ? 'active' : '' }}"  href="{{ route('api-tokens.create') }}">Crear Token</a></li>
                    </ul>
                </li>
                @endrole

                @canany(['manage-roles', 'manage-permissions'])
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                            <i class="fa fa-shield-alt"></i></span>
                        <span> Roles y Permisos </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        @can('manage-roles')
                        <li><a class="{{ Request::is('roles') ? 'active' : '' }}"  href="{{ route('role.index') }}">Gestionar Roles</a></li>
                        @endcan
                        @can('manage-permissions')
                        <li><a class="{{ Request::is('permissions') ? 'active' : '' }}"  href="{{ route('permission.index') }}">Gestionar Permisos</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                @can('manage insurances')
                <li class="menu-side">
                    <a class="{{ Request::is('insurances*') ? 'active' : '' }}" href="{{ route('insurances.index') }}">
                        <span class="menu-side">
                            <i class="fa fa-shield"></i>
                        </span>
                        <span>Aseguradoras</span>
                    </a>
                </li>
                @endcan
            </ul>
            <div class="logout-btn">
                <a href="{{ url('logout') }}">
                    <span class="menu-side">
                        <i class="fa fa-sign-out-alt"></i></span>
                    <span>{{__('Cerrar sesión')}}</span>
                </a>
            </div>
        </div>
    </div>
</div>
