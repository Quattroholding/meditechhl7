 <!-- Sidebar -->
    <aside class="help-sidebar">
        <div class="logo">
              <h4>Centro de Ayuda</h4>
            <img src="{{ url('images/logoSAMI.jpg') }}" alt="SAMI Logo" onerror="this.style.display='none'">
          
        </div>
        
        <nav class="nav-section">
            <div class="nav-section-title">Navegación</div>
            <a href="{{ route('help.index') }}" class="nav-link {{ $active === 'index' ? 'active' : '' }}" >
                <i class="fas fa-home"></i>
                <span>Inicio</span>
                @if ($active === 'index')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
            <a href="{{ route('help.registration') }}" class="nav-link {{ $active === 'registration' ? 'active' : '' }}">
                <i class="fas fa-user-plus"></i>
                <span>Registro</span>
                @if ($active === 'registration')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
            <a href="{{ route('help.branches') }}" class="nav-link {{ $active === 'branches' ? 'active' : '' }}">
                <i class="fas fa-building"></i>
                <span>Sucursales</span>
                @if ($active === 'branches')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
            <a href="{{ route('help.consulting-rooms') }}" class="nav-link {{ $active === 'consulting-rooms' ? 'active' : '' }}">
                <i class="fas fa-door-open"></i>
                <span>Consultorios</span>
                @if ($active === 'consulting-rooms')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
            <a href="{{ route('help.subscriptions') }}" class="nav-link {{ $active === 'subscriptions' ? 'active' : '' }}">
                <i class="fas fa-id-card"></i>
                <span>Suscripciones</span>
                @if ($active === 'subscriptions')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
            <a href="{{ route('help.patients') }}" class="nav-link {{ $active === 'patients' ? 'active' : '' }}">
                <i class="fas fa-hospital-user"></i>
                <span>Pacientes</span>
                @if ($active === 'patients')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
            <a href="{{ route('help.medical-history') }}" class="nav-link {{ $active === 'medical-history' ? 'active' : '' }}">
                <i class="fas fa-notes-medical"></i>
                <span>Historia Médica</span>
                @if ($active === 'medical-history')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
            <a href="{{ route('help.appointments') }}" class="nav-link {{ $active === 'appointments' ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i>
                <span>Citas</span>
                @if ($active === 'appointments')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
            <a href="{{ route('help.consultation') }}" class="nav-link {{ $active === 'consultation' ? 'active' : '' }}">
                <i class="fas fa-stethoscope"></i>
                <span>Consultas</span>
                @if ($active === 'consultation')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
            <a href="{{ route('help.billing') }}" class="nav-link {{ $active === 'billing' ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Facturación</span>
                @if ($active === 'billing')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
            <a href="{{ route('help.payments') }}" class="nav-link {{ $active === 'payments' ? 'active' : '' }}">
                <i class="fas fa-credit-card"></i>
                <span>Pagos</span>
                    @if ($active === 'payments')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
            <a href="{{ route('help.settings') }}" class="nav-link {{ $active === 'settings' ? 'active' : '' }}">
                <i class="fas fa-cogs"></i>
                <span>Configuraciones</span>
                @if ($active === 'settings')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
        </nav>
    </aside>