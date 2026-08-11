<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Header y Search -->
                    <div class="page-table-header mb-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="doctor-table-blk">
                                    <h3>Correos de Salida - Exchange Office 365</h3>
                                    <div class="top-nav-search table-search-blk mt-2">
                                        <input type="text" wire:model.live.debounce.500ms="search" class="form-control" placeholder="Buscar en asunto, destinatario...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto text-end">
                                <button type="button" wire:click="$refresh" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sync-alt"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </div>

                    @include('partials.message')

                    @if(!$isConnected)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Error de conexión:</strong> {{ $errorMessage ?? 'No se pudo conectar con Microsoft Graph API. Verifica la configuración.' }}
                        </div>
                    @endif

                    <!-- Información de conexión -->
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Buzón:</strong> {{ config('services.microsoft.mailbox_email', 'notificaciones@meditecpty.com') }}
                        @if($isConnected)
                            <span class="badge bg-success ms-2">Conectado</span>
                        @else
                            <span class="badge bg-danger ms-2">Desconectado</span>
                        @endif
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;"></th>
                                    <th wire:click="sortBy('subject')" style="cursor: pointer;">
                                        Asunto
                                        @if($sortField === 'subject')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </th>
                                    <th>Destinatarios</th>
                                    <th wire:click="sortBy('sentDateTime')" style="width: 180px; cursor: pointer;">
                                        Fecha de Envío
                                        @if($sortField === 'sentDateTime')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </th>
                                    <th style="width: 80px;" class="text-center">Adjuntos</th>
                                    <th style="width: 100px;" class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($emails as $email)
                                    <tr>
                                        <td>
                                            <span class="avatar avatar-sm bg-primary">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $email['subject'] ?? '(Sin asunto)' }}</strong>
                                            @if(isset($email['bodyPreview']))
                                                <br>
                                                <small class="text-muted">
                                                    {{ \Str::limit($email['bodyPreview'], 100) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($email['toRecipients']) && count($email['toRecipients']) > 0)
                                                <div>
                                                    @foreach($email['toRecipients'] as $index => $recipient)
                                                        @if($index < 2)
                                                            <span class="badge bg-info me-1">
                                                                {{ $recipient['emailAddress']['name'] ?? $recipient['emailAddress']['address'] }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                    @if(count($email['toRecipients']) > 2)
                                                        <span class="badge bg-secondary">
                                                            +{{ count($email['toRecipients']) - 2 }} más
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                            @if(isset($email['ccRecipients']) && count($email['ccRecipients']) > 0)
                                                <small class="text-muted">
                                                    CC: {{ count($email['ccRecipients']) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($email['sentDateTime']))
                                                <small>
                                                    {{ \Carbon\Carbon::parse($email['sentDateTime'])->format('d/m/Y H:i') }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($email['sentDateTime'])->diffForHumans() }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(isset($email['hasAttachments']) && $email['hasAttachments'])
                                                <i class="fas fa-paperclip text-primary"></i>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button wire:click="viewDetails('{{ $email['id'] }}')" class="btn btn-primary btn-sm" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            @if($isConnected)
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No hay correos enviados</p>
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
                    <div class="mt-3">
                        {{ $emails->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalles -->
    @if($showDetailModal && $selectedEmail)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-envelope-open-text"></i>
                            Detalles del Correo
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Asunto:</label>
                            <p>{{ $selectedEmail['subject'] ?? '(Sin asunto)' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">De:</label>
                            <p>
                                {{ $selectedEmail['from']['emailAddress']['name'] ?? '' }}
                                &lt;{{ $selectedEmail['from']['emailAddress']['address'] ?? '' }}&gt;
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Para:</label>
                            @if(isset($selectedEmail['toRecipients']))
                                <ul class="list-unstyled">
                                    @foreach($selectedEmail['toRecipients'] as $recipient)
                                        <li>
                                            <span class="badge bg-info">
                                                {{ $recipient['emailAddress']['name'] ?? $recipient['emailAddress']['address'] }}
                                                &lt;{{ $recipient['emailAddress']['address'] }}&gt;
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        @if(isset($selectedEmail['ccRecipients']) && count($selectedEmail['ccRecipients']) > 0)
                            <div class="mb-3">
                                <label class="form-label fw-bold">CC:</label>
                                <ul class="list-unstyled">
                                    @foreach($selectedEmail['ccRecipients'] as $recipient)
                                        <li>
                                            <span class="badge bg-secondary">
                                                {{ $recipient['emailAddress']['name'] ?? $recipient['emailAddress']['address'] }}
                                                &lt;{{ $recipient['emailAddress']['address'] }}&gt;
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de Envío:</label>
                            <p>
                                {{ \Carbon\Carbon::parse($selectedEmail['sentDateTime'])->format('d/m/Y H:i:s') }}
                                ({{ \Carbon\Carbon::parse($selectedEmail['sentDateTime'])->diffForHumans() }})
                            </p>
                        </div>

                        @if(isset($selectedEmail['internetMessageId']))
                            <div class="mb-3">
                                <label class="form-label fw-bold">Message ID:</label>
                                <p><code>{{ $selectedEmail['internetMessageId'] }}</code></p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold">Vista previa del contenido:</label>
                            <div class="border p-3 bg-light">
                                @if(isset($selectedEmail['body']['content']))
                                    {!! \Str::limit(strip_tags($selectedEmail['body']['content']), 500) !!}
                                @else
                                    {{ $selectedEmail['bodyPreview'] ?? 'Sin contenido' }}
                                @endif
                            </div>
                        </div>

                        @if(isset($selectedEmail['hasAttachments']) && $selectedEmail['hasAttachments'])
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-paperclip"></i> Tiene adjuntos
                                </label>
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
