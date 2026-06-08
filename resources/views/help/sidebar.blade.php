 <style>
    /* Sidebar Styles */
    .help-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 280px;
        height: 100vh;
        background: linear-gradient(180deg, #1a237e 0%, #283593 100%);
        padding: 20px 0;
        overflow-y: auto;
        z-index: 1000;
        transition: transform 0.3s ease;
    }

    .help-sidebar .logo {
        text-align: center;
        padding: 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 20px;
    }

    .help-sidebar .logo img {
        max-width: 150px;
    }

    .help-sidebar .logo h4 {
        color: #fff;
        margin-top: 10px;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .help-sidebar .nav-section {
        padding: 10px 20px;
    }

    .help-sidebar .nav-section-title {
        color: rgba(255,255,255,0.6);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
        padding-left: 10px;
        font-weight: bold;
    }

    .help-sidebar .nav-link {
        color: rgba(255,255,255,0.8);
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 5px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        font-size: 0.95rem;
    }

    .help-sidebar .nav-link:hover,
    .help-sidebar .nav-link.active {
        background: rgba(255,255,255,0.15);
        color: #fff;
        transform: translateX(5px);
    }

    .help-sidebar .nav-link i {
        width: 20px;
        text-align: center;
        font-size: 1.1rem;
    }

    .help-sidebar .nav-link .badge {
        margin-left: auto;
        font-size: 0.65rem;
        font-weight: 600;
        padding: 4px 8px;
    }

    /* Mobile handling for sidebar */
    @media (max-width: 992px) {
        .help-sidebar {
            width: 100%;
            height: auto;
            position: relative;
            transform: none !important;
        }
    }
</style>

<!-- Sidebar -->
    <aside class="help-sidebar">
        <div class="logo">
              <h4>Centro de Ayuda</h4>
            <img src="{{ url('images/logoSAMI.png') }}" alt="SAMI Logo" onerror="this.style.display='none'">
          
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

            <a href="{{ route('help.roles') }}" class="nav-link {{ $active === 'roles' ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span>Roles y Permisos</span>
                @if ($active === 'roles')
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

            <a href="{{ route('help.2fa') }}" class="nav-link {{ $active === '2fa' ? 'active' : '' }}">
                <i class="fas fa-shield-alt"></i>
                <span>Seguridad 2FA</span>
                @if ($active === '2fa')
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

            <a href="{{ route('help.doctors') }}" class="nav-link {{ $active === 'doctors' ? 'active' : '' }}">
                <i class="fas fa-user-md"></i>
                <span>Doctores</span>
                @if ($active === 'doctors')
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
            <a href="{{ route('help.doctor-dashboard') }}" class="nav-link {{ $active === 'doctor-dashboard' ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard Médico</span>
                @if ($active === 'doctor-dashboard')
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
            <a href="{{ route('help.profile') }}" class="nav-link {{ $active === 'profile' ? 'active' : '' }}">
                <i class="fas fa-user-circle"></i>
                <span>Mi Perfil</span>
                @if ($active === 'profile')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>


            <a href="{{ route('help.users') }}" class="nav-link {{ $active === 'users' ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
                @if ($active === 'users')
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

            <a href="{{ route('help.service-requests') }}" class="nav-link {{ $active === 'service-requests' ? 'active' : '' }}">
                <i class="fas fa-microscope"></i>
                <span>Repositorio Estudios</span>
                @if ($active === 'service-requests')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>

            <a href="{{ route('help.medicines') }}" class="nav-link {{ $active === 'medicines' ? 'active' : '' }}">
                <i class="fas fa-pills"></i>
                <span>Medicamentos</span>
                @if ($active === 'medicines')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>

            <a href="{{ route('help.inventory') }}" class="nav-link {{ $active === 'inventory' ? 'active' : '' }}">
                <i class="fas fa-boxes"></i>
                <span>Inventario</span>
                @if ($active === 'inventory')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>

            <a href="{{ route('help.medical-directory') }}" class="nav-link {{ $active === 'medical-directory' ? 'active' : '' }}">
                <i class="fas fa-address-book"></i>
                <span>Directorio Médico</span>
                @if ($active === 'medical-directory')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>

            <a href="{{ route('help.support') }}" class="nav-link {{ $active === 'support' ? 'active' : '' }}">
                <i class="fas fa-question-circle"></i>
                <span>Soporte</span>
                @if ($active === 'support')
                    <span class="badge bg-warning">Actual</span>
                @endif
            </a>
        </nav>
    </aside>