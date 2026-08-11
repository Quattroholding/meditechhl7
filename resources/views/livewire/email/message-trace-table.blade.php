<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Header -->
                    <div class="page-table-header mb-3">
                        <h3><i class="fas fa-search"></i> Seguimiento de Mensajes (Message Trace)</h3>
                        <p class="text-muted">Busca correos específicos y verifica si fueron entregados</p>
                    </div>

                    @include('partials.message')

                    <!-- Filtros de Búsqueda -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Remitente -->
                                <div class="col-md-6">
                                    <label class="form-label">Remitente (Sender Address)</label>
                                    <input type="email" wire:model="senderEmail" class="form-control" placeholder="notificaciones@meditecpty.com">
                                    <small class="text-muted">Correo del remitente</small>
                                </div>

                                <!-- Destinatario -->
                                <div class="col-md-6">
                                    <label class="form-label">Destinatario (Recipient Address)</label>
                                    <input type="email" wire:model="recipientEmail" class="form-control" placeholder="ejemplo@gmail.com">
                                    <small class="text-muted">Correo del destinatario para verificar entrega</small>
                                </div>

                                <!-- Fecha Inicio -->
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Inicio</label>
                                    <input type="date" wire:model="startDate" class="form-control">
                                </div>

                                <!-- Fecha Fin -->
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Fin</label>
                                    <input type="date" wire:model="endDate" class="form-control">
                                </div>

                                <!-- Asunto -->
                                <div class="col-md-6">
                                    <label class="form-label">Asunto (Subject)</label>
                                    <input type="text" wire:model.live.debounce.500ms="searchTerm" class="form-control" placeholder="Buscar en asunto...">
                                </div>

                                <!-- Botones -->
                                <div class="col-12">
                                    <button type="button" wire:click="search" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                    <button type="button" wire:click="clearFilters" class="btn btn-secondary">
                                        <i class="fas fa-eraser"></i> Limpiar Filtros
                                    </button>
                                    <button type="button" wire:click="$refresh" class="btn btn-info">
                                        <i class="fas fa-sync-alt"></i> Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!$isConnected)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Error de conexión:</strong> {{ $errorMessage ?? 'No se pudo conectar con Exchange.' }}
                        </div>
                    @endif

                    <!-- Información de la Búsqueda -->
                    <div class="alert alert-info mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <strong><i class="fas fa-info-circle"></i> Búsqueda actual:</strong>
                                @if($senderEmail)
                                    Remitente: <code>{{ $senderEmail }}</code>
                                @endif
                                @if($recipientEmail)
                                    | Destinatario: <code>{{ $recipientEmail }}</code>
                                @endif
                                | Período: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                            </div>
                            <div class="col-md-4 text-end">
                                @if($isConnected)
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Conectado</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Desconectado</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Resultados -->
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;"></th>
                                    <th>Fecha/Hora</th>
                                    <th>Remitente</th>
                                    <th>Destinatario</th>
                                    <th>Asunto</th>
                                    <th style="width: 120px;" class="text-center">Estado</th>
                                    <th style="width: 100px;" class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($messages as $index => $message)
                                    <tr>
                                        <td>
                                            <span class="avatar avatar-sm bg-{{ $message['Status'] === 'Entregado' ? 'success' : ($message['Status'] === 'Fallido' ? 'danger' : 'warning') }}">
                                                <i class="fas fa-{{ $message['Status'] === 'Entregado' ? 'check' : ($message['Status'] === 'Fallido' ? 'times' : 'clock') }}"></i>
                                            </span>
                                        </td>
                                        <td>
                                            @if(isset($message['Received']))
                                                <small>
                                                    {{ \Carbon\Carbon::parse($message['Received'])->format('d/m/Y H:i:s') }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($message['Received'])->diffForHumans() }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $message['SenderAddress'] ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $message['RecipientAddress'] ?? 'N/A' }}
                                            </span>
                                            @if(isset($message['RecipientCount']) && $message['RecipientCount'] > 1)
                                                <span class="badge bg-secondary">
                                                    +{{ $message['RecipientCount'] - 1 }} más
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $message['Subject'] ?? '(Sin asunto)' }}</strong>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass = match($message['Status'] ?? 'Unknown') {
                                                    'Entregado' => 'success',
                                                    'Fallido' => 'danger',
                                                    'Pendiente' => 'warning',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ $message['Status'] ?? 'Desconocido' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <button wire:click="viewDetails({{ $index }})" class="btn btn-primary btn-sm" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            @if($isConnected)
                                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No se encontraron correos con los criterios especificados</p>
                                                <p class="text-muted"><small>Intenta ampliar el rango de fechas o cambiar los filtros</small></p>
                                            @else
                                                <i class="fas fa-plug fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No se pudo conectar con Exchange</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($messages->total() > 0)
                        <div class="mt-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <p class="text-muted mb-0">
                                        Mostrando {{ $messages->firstItem() }} - {{ $messages->lastItem() }} de {{ $messages->total() }} correos
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    {{ $messages->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalles -->
    @if($showDetailModal && $selectedMessage)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-envelope-open-text"></i>
                            Detalles del Mensaje
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Estado -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Estado de Entrega:</label>
                            @php
                                $status = $selectedMessage['Status'] ?? 'Desconocido';
                                $statusIcon = match($status) {
                                    'Entregado' => 'check-circle',
                                    'Fallido' => 'times-circle',
                                    'Pendiente' => 'clock',
                                    default => 'question-circle'
                                };
                                $statusColor = match($status) {
                                    'Entregado' => 'success',
                                    'Fallido' => 'danger',
                                    'Pendiente' => 'warning',
                                    default => 'secondary'
                                };
                            @endphp
                            <h4>
                                <span class="badge bg-{{ $statusColor }}">
                                    <i class="fas fa-{{ $statusIcon }}"></i> {{ $status }}
                                </span>
                            </h4>
                        </div>

                        <!-- Asunto -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Asunto:</label>
                            <p>{{ $selectedMessage['Subject'] ?? '(Sin asunto)' }}</p>
                        </div>

                        <!-- Remitente -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Remitente:</label>
                            <p><code>{{ $selectedMessage['SenderAddress'] ?? 'N/A' }}</code></p>
                        </div>

                        <!-- Destinatarios -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Destinatario(s):</label>
                            @if(isset($selectedMessage['AllRecipients']) && count($selectedMessage['AllRecipients']) > 0)
                                <ul class="list-unstyled">
                                    @foreach($selectedMessage['AllRecipients'] as $recipient)
                                        <li>
                                            <span class="badge bg-info">
                                                {{ $recipient['emailAddress']['name'] ?? $recipient['emailAddress']['address'] }}
                                                &lt;{{ $recipient['emailAddress']['address'] }}&gt;
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p><code>{{ $selectedMessage['RecipientAddress'] ?? 'N/A' }}</code></p>
                            @endif
                        </div>

                        <!-- CC Recipients -->
                        @if(isset($selectedMessage['CcRecipients']) && count($selectedMessage['CcRecipients']) > 0)
                            <div class="mb-3">
                                <label class="form-label fw-bold">CC:</label>
                                <ul class="list-unstyled">
                                    @foreach($selectedMessage['CcRecipients'] as $recipient)
                                        <li>
                                            <span class="badge bg-secondary">
                                                {{ $recipient['emailAddress']['address'] }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Fecha -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha/Hora:</label>
                            <p>
                                {{ \Carbon\Carbon::parse($selectedMessage['Received'])->format('d/m/Y H:i:s') }}
                                ({{ \Carbon\Carbon::parse($selectedMessage['Received'])->diffForHumans() }})
                            </p>
                        </div>

                        <!-- Message ID -->
                        @if(isset($selectedMessage['MessageId']))
                            <div class="mb-3">
                                <label class="form-label fw-bold">Message ID:</label>
                                <p><code style="font-size: 0.85em;">{{ $selectedMessage['MessageId'] }}</code></p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
