<x-guest-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item active">Debug Login</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title mb-0">Debug Login - Seleccionar Usuario</h5>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-warning text-dark">Debug Mode</span>
                                    <span class="badge bg-info ms-2">IP: {{ request()->ip() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Filtros -->
                            <form method="GET" class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Buscar por nombre o email</label>
                                    <input type="text"
                                           name="search"
                                           class="form-control"
                                           placeholder="Buscar..."
                                           value="{{ request('search') }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Filtrar por rol</label>
                                    <select name="role" class="form-select">
                                        <option value="">Todos los roles</option>
                                        @foreach($roles as $roleOption)
                                            <option value="{{ $roleOption->name }}"
                                                    {{ request('role') == $roleOption->name ? 'selected' : '' }}>
                                                {{ ucfirst($roleOption->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Filtrar por plan</label>
                                    <select name="package" class="form-select">
                                        <option value="">Todos los Planes</option>
                                        @foreach($packages as $packageOption)
                                            <option value="{{ $packageOption->id }}"
                                                {{ request('package') == $packageOption->id ? 'selected' : '' }}>
                                                {{ ucfirst($packageOption->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-search me-1"></i> Buscar
                                    </button>
                                    <a href="{{ route('debug.login') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Limpiar
                                    </a>
                                </div>
                            </form>

                            <!-- Tabla de usuarios -->
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Usuario</th>
                                            <th>Email</th>
                                            <th>Roles</th>
                                            <th>Cliente</th>
                                            <th>Plan</th>
                                            <th>Estado Suscripción</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($users as $user)
                                            <tr>
                                                <td class="text-sm">{{ $user->id }}</td>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        @if(!empty($user->profile_picture))
                                                            <a href="#" class="avatar avatar-sm me-2">
                                                                <img class="avatar-img rounded-circle"
                                                                     src="{{ url('storage/'.$user->profile_picture) }}"
                                                                     alt="{{ $user->full_name }}">
                                                            </a>
                                                        @else
                                                            <a href="#" class="avatar avatar-sm me-2">
                                                                <img class="avatar-img rounded-circle"
                                                                     src="{{ URL::asset('/assets/img/user-06.jpg') }}"
                                                                     alt="{{ $user->full_name }}">
                                                            </a>
                                                        @endif
                                                        <a href="#">{{ $user->full_name }}</a>
                                                    </h2>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @foreach($user->roles as $userRole)
                                                        <span class="badge bg-warning me-1">
                                                            {{ $userRole->name }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @php
                                                        $defaultClient = $user->default_client_id ? \App\Models\Client::find($user->default_client_id) : null;
                                                    @endphp
                                                    {{ $defaultClient?->name ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{$defaultClient?->package->name}}
                                                </td>
                                                <td>
                                                    @php
                                                        $subscription = $defaultClient?->subscription;
                                                    @endphp
                                                    @if($subscription && $subscription->status)
                                                        <span class="badge bg-{{ $subscription->status->color() }}">
                                                            {{ $subscription->status->label() }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Sin suscripción</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <form method="POST"
                                                          action="{{ route('debug.login.as', $user) }}"
                                                          style="display: inline;"
                                                          onsubmit="return confirm('¿Hacer login como {{ $user->full_name }}?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-sign-in-alt me-1"></i> Login
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <p class="text-muted mb-0">No se encontraron usuarios</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            @if($users->hasPages())
                                <div class="mt-4">
                                    {{ $users->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
