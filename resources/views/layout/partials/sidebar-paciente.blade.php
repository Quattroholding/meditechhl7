<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">

            <ul>
                <li class="menu-title">Menú</li>
                <li>
                    <a class="{{ Request::is('dashboard/patient') ? 'active' : '' }}"  href="{{ route('patient.dashboard') }}">
                        <span class="menu-side"> <i class="fa fa-chart-bar"></i></span>&nbsp;
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="submenu">
                    <a href="javascript:;"><span class="menu-side"><i class="fa fa-calendar-alt"></i></span>
                        <span>  {{ __('appointment.titles') }} </span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ Request::is('appointments') ? 'active' : '' }}" href="{{ url('appointments') }}">{{ __('generic.list') }} {{ __('appointment.titles') }}</a></li>
                        <li><a class="{{ Request::is('appointments/calendar') ? 'active' : '' }}" href="{{ route('appointment.calendar') }}">{{ __('appointment.booking') }} </a></li>
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
                    </ul>
                </li>
                <li>
                    <a class="{{ Request::is('practitioners/directory') ? 'active' : '' }}"  href="{{ route('practitioner.directory') }}">
                        <span class="menu-side"><i class="fa fa-user-injured"></i></span>&nbsp;
                        <span>{{ __('patient.medical_directory') }}</span>
                    </a>
                </li>
                <li>
                    <a class="{{ Request::is('patients/'.auth()->user()->patient->id.'/medical_history') ? 'active' : '' }}"  href="{{ route('patient.medical_history',auth()->user()->patient->id) }}">
                        <span class="menu-side"><i class="fa fa-file-medical"></i></span>&nbsp;
                        <span>{{ __('patient.medical_history') }}</span>
                    </a>
                </li>
                <li>
                    <a class="{{ Request::is('patients/'.auth()->user()->patient->id.'/profile') ? 'active' : '' }}"  href="{{ route('patient.profile',auth()->user()->patient->id) }}">
                        <span class="menu-side"><i class="fa fa-cog"></i></span>&nbsp;
                        <span>{{ __('patient.profile') }}</span>
                    </a>
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
