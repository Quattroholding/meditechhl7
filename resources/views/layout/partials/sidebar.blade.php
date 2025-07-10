<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">Menú</li>
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                            <i class="fa fa-chart-bar"></i></span>
                            <span> Dashboard </span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('/dashboard', 'index') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                        {{--}}
                        <li><a class="{{ Request::is('dashboard/doctor') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}">Doctor Dashboard</a></li>
                        <li><a class="{{ Request::is('dashboard/patient') ? 'active' : '' }}"  href="{{ route('patient.dashboard') }}">{{ __('patient.titles') }} Dashboard</a></li>
                        {{--}}
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side primary">
                            <i class="fa fa-hospital"></i></span>
                            <span> {{ __('client.titles') }} </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('clients') ? 'active' : '' }}" href="{{ route('client.index') }}">{{ __('generic.list') }} {{ __('client.titles') }}  </a></li>
                        <li><a class="{{ Request::is('clients/create') ? 'active' : '' }}"   href="{{ route('client.create') }}">{{ __('generic.create') }} {{ __('client.title') }}</a></li>
                        <li><a class="{{ Request::is('clients/branch') ? 'active' : '' }}"   href="{{ route('client.branch.index') }}">{{ __('generic.list') }} {{ __('client.branches') }}</a></li>
                        <li><a class="{{ Request::is('clients/consulting_rooms') ? 'active' : '' }}"   href="{{ route('client.room.index') }}">{{ __('generic.list') }} {{ __('client.rooms') }}</a></li>
                        <li><a class="{{ Request::is('user/create') ? 'active' : '' }}"   href="{{ route('user.create',array('role_id'=>3)) }}">{{ __('generic.create') }} {{ __('user.asistent') }}</a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                        <i class="fa fa-user-md"></i></span>
                        <span>{{ __('doctor.titles') }} </span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('practitioners') ? 'active' : '' }}"  href="{{ route('practitioner.index') }}">{{ __('generic.list') }} {{ __('doctor.titles') }}</a></li>
                        <li><a class="{{ Request::is('practitioners/create') ? 'active' : '' }}"   href="{{ route('practitioner.create') }}">{{ __('generic.create') }} {{ __('doctor.title') }}</a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                        <i class="fa fa-user-injured"></i></span>
                        <span>{{ __('patient.titles') }} </span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('patients') ? 'active' : '' }}"  href="{{ route('patient.index') }}">{{ __('generic.list') }} {{ __('patient.titles') }}</a></li>
                        <li><a class="{{ Request::is('patients/create') ? 'active' : '' }}"   href="{{ route('patient.create') }}">{{ __('generic.create') }} {{ __('patient.title') }}</a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:;">
                        <span class="menu-side"><i class="fa fa-calendar-alt"></i></span>
                        <span>  {{ __('appointment.titles') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('appointments') ? 'active' : '' }}" href="{{ route('appointment.index') }}">{{ __('generic.list') }} {{ __('appointment.titles') }}</a></li>
                        <li><a class="{{ Request::is('appointments/calendar') ? 'active' : '' }}" href="{{ route('appointment.calendar') }}">{{ __('Calendario') }} </a></li>
                    </ul>
                </li>
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
                <li class="submenu">
                    <a href="javascript:;">
                        <span class="menu-side">
                            <i class="fa fa-file-invoice-dollar"></i></span>
                        <span> {{__('Cuentas')}} </span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('accounts/invoices') ? 'active' : '' }}" href="{{route('invoice.index')}}">{{__('Facturas')}}</a></li>
                        <li><a class="{{ Request::is('accounts/payments') ? 'active' : '' }}" href="{{route('payment.index')}}">{{__('Pagos')}}</a></li>

                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                            <i class="fa fa-cogs"></i></span>
                        <span> Configuraciones </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('settings/create_user_procedures') ? 'active' : '' }}"  href="{{ route('setting.create_user_procedures') }}">{{ __('Servicios') }}</a></li>
                        <li><a class="{{ Request::is('settings/create_consultation_template') ? 'active' : '' }}"  href="{{ route('setting.create_template') }}">{{ __('Plantilla Consulta') }}</a></li>
                        <li><a class="{{ Request::is('settings/create_rapid_access') ? 'active' : '' }}"  href="{{ route('setting.create_rapid_access') }}">{{ __('Accesos Rapidos') }}</a></li>
                        <li><a class="{{ Request::is('settings/create_working_hour_user') ? 'active' : '' }}"  href="{{ route('setting.create_working_hour_user') }}">{{ __('Horario Laboral') }}</a></li>

                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                            <i class="fa fa-users"></i></span>
                        <span> Usuarios </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('users') ? 'active' : '' }}"  href="{{ route('user.index') }}">{{ __('generic.list') }} {{ __('user.titles') }}</a></li>
                        <li><a class="{{ Request::is('users/create') ? 'active' : '' }}"  href="{{ route('user.create') }}">{{ __('generic.create') }} {{ __('user.title') }}</a></li>
                    </ul>
                </li>
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
