<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">Menú</li>
                <li>
                    <a class="{{ Request::is('dashboard/patient') ? 'active' : '' }}"  href="{{ route('patient.dashboard') }}">
                        <span class="menu-side"> <img  src="{{ URL::asset('/assets/img/icons/menu-icon-01.svg') }}" alt=""></span>&nbsp;
                      Dashboard
                    </a>
                </li>
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side"><img
                                src="{{ URL::asset('/assets/img/icons/menu-icon-04.svg') }}" alt=""></span>
                        <span>  {{ __('appointment.titles') }} </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('appointments') ? 'active' : '' }}" href="{{ url('appointments') }}">{{ __('generic.list') }} {{ __('appointment.titles') }}</a></li>
                        <li><a class="{{ Request::is('appointments/calendar') ? 'active' : '' }}" href="{{ route('appointment.calendar') }}">{{ __('Calendario') }} </a></li>
                    </ul>
                </li>
                <li>
                    <a class="{{ Request::is('patient/') ? 'active' : '' }}"  href="{{ route('patient.medical_history',auth()->user()->patient->id) }}">
                        <span class="menu-side"><img  src="{{ URL::asset('/assets/img/icons/menu-icon-13.svg') }}" alt=""></span>&nbsp;
                        {{ __('patient.medical_history') }}
                    </a>
                </li>
                <li>
                    <a class="{{ Request::is('practitioners/directory') ? 'active' : '' }}"  href="{{ route('practitioner.directory') }}">
                        <span class="menu-side"><img  src="{{ URL::asset('/assets/img/icons/menu-icon-03.svg') }}" alt=""></span>&nbsp;
                        {{ __('patient.medical_directory') }}
                    </a>
                </li>
                <li>
                    <a class="{{ Request::is('patient/') ? 'active' : '' }}"  href="{{ route('patient.profile',auth()->user()->patient->id) }}">
                        <span class="menu-side"><img  src="{{ URL::asset('/assets/img/icons/menu-icon-16.svg') }}" alt=""></span>&nbsp;
                        {{ __('patient.profile') }}
                    </a>
                </li>
            </ul>
            <div class="logout-btn">
                <a href="{{ url('login') }}">
                    <span class="menu-side">
                        <img src="{{ URL::asset('/assets/img/icons/logout.svg') }}" alt=""></span>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</div>
