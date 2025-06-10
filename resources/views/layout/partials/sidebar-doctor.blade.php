<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">Menú</li>
                <li class="menu-side">
                    <a class="{{ Request::is('dashboard/doctor') ? 'active' : '' }}"  href="{{ route('doctor.dashboard') }}"><span class="menu-side" >
                            <img  src="{{ URL::asset('/assets/img/icons/menu-icon-01.svg') }}" alt=""></span>
                            <span> Dashboard </span>
                    </a>
                </li>
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side"><img
                                src="{{ URL::asset('/assets/img/icons/menu-icon-04.svg') }}" alt=""></span>
                        <span>  {{ __('appointment.titles') }} </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('appointments') ? 'active' : '' }}" href="{{ url('appointments') }}">{{ __('generic.list') }} {{ __('appointment.titles') }}</a></li>
                        <li><a class="{{ Request::is('appointments/calendar') ? 'active' : '' }}" href="{{ route('appointment.calendar') }}">{{ __('appointment.scheduler') }} </a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                        <img  src="{{ URL::asset('/assets/img/icons/menu-icon-03.svg') }}" alt=""></span>
                        <span>{{ __('patient.titles') }} </span> <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('patients') ? 'active' : '' }}"  href="{{ route('patient.index') }}">{{ __('generic.list') }} {{ __('patient.titles') }}</a></li>
                        <li><a class="{{ Request::is('patients/create') ? 'active' : '' }}"   href="{{ route('patient.create') }}">{{ __('generic.create') }} {{ __('patient.title') }}</a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:;">
                        <span class="menu-side"> <img  src="{{ URL::asset('/assets/img/icons/icono-consulta.svg') }}" alt=""></span>
                        <span>  {{ __('encounter.titles') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('consultation') ? 'active' : '' }}" href="{{ route('consultation.index') }}">{{ __('generic.list') }} {{ __('encounter.titles') }}</a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side">
                            <img src="{{ URL::asset('/assets/img/icons/menu-icon-16.svg') }}" alt=""></span>
                        <span> Configuraciones </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('settings/create_user_procedures') ? 'active' : '' }}"  href="{{ route('setting.create_user_procedures') }}">{{ __('Procedimientos') }}</a></li>
                        <li><a class="{{ Request::is('settings/create_consultation_template') ? 'active' : '' }}"  href="{{ route('setting.create_template') }}">{{ __('Plantilla Consulta') }}</a></li>
                        <li><a class="{{ Request::is('settings/create_rapid_access') ? 'active' : '' }}"  href="{{ route('setting.create_rapid_access') }}">{{ __('Accesos Rapidos') }}</a></li>
                        <li><a class="{{ Request::is('settings/create_working_hour_user') ? 'active' : '' }}"  href="{{ route('setting.create_working_hour_user') }}">{{ __('Horario Laboral') }}</a></li>
                        <li><a class="{{ Request::is('client/branch/create') ? 'active' : '' }}"   href="{{ route('client.branch.create') }}">{{ __('generic.create') }} {{ __('client.branch') }}</a></li>
                        <li><a class="{{ Request::is('client/room/create') ? 'active' : '' }}"   href="{{ route('client.room.create') }}">{{ __('generic.create') }} {{ __('client.room') }}</a></li>
                    </ul>
                </li>
                <li>
                    <a class="{{ Request::is('practitioners/directory') ? 'active' : '' }}"  href="{{ route('practitioner.directory') }}">
                        <span class="menu-side"><img  src="{{ URL::asset('/assets/img/icons/menu-icon-03.svg') }}" alt=""></span>&nbsp;
                        <span>{{ __('patient.medical_directory') }}</span>
                    </a>
                </li>
                <li>
                    <a class="{{ Request::is('practitioners/') ? 'active' : '' }}"  href="{{ route('practitioner.profile',auth()->user()->practitioner->id) }}">
                        <span class="menu-side"><img  src="{{ URL::asset('/assets/img/icons/menu-icon-16.svg') }}" alt=""></span>&nbsp;
                        <span>{{ __('doctor.profile') }}</span>
                    </a>
                </li>
            </ul>
            <div class="logout-btn">
                <a href="{{ url('logout') }}">
                    <span class="menu-side">
                        <img src="{{ URL::asset('/assets/img/icons/logout.svg') }}" alt=""></span>
                    <span>{{__('Cerrar sesión')}}</span>
                </a>
            </div>
        </div>
    </div>
</div>
 ó
