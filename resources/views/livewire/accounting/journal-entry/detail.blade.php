<div>
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <h2 class="mb-1">{{ $journalEntry->entry_number }}</h2>
                    <p class="text-muted mb-0">
                        <span class="badge badge-{{ $journalEntry->status === App\Enums\JournalEntryStatus::POSTED ? 'success' : ($journalEntry->status === App\Enums\JournalEntryStatus::DRAFT ? 'warning' : 'secondary') }}">
                            {{ $journalEntry->status->label() }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('accounting.journal-entries') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            @if($journalEntry->isDraft())
                <button wire:click="post" class="btn btn-success" wire:loading.attr="disabled">
                    <i class="fas fa-check"></i> Contabilizar
                </button>
            @elseif($journalEntry->isPosted())
                <button wire:click="reverse" class="btn btn-danger" wire:loading.attr="disabled" onclick="confirm('¿Revertir este asiento?') || event.stopImmediatePropagation()">
                    <i class="fas fa-undo"></i> Revertir
                </button>
            @endif
        </div>
    </div>

    <!-- Información General -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Información General</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label text-muted small">Número de Asiento</label>
                    <p class="fw-bold">{{ $journalEntry->entry_number }}</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label text-muted small">Fecha</label>
                    <p class="fw-bold">{{ $journalEntry->entry_date->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label text-muted small">Período</label>
                    <p class="fw-bold">{{ $journalEntry->accountingPeriod->name }}</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label text-muted small">Estado</label>
                    <p class="fw-bold">
                        <span class="badge badge-{{ $journalEntry->status === App\Enums\JournalEntryStatus::POSTED ? 'success' : ($journalEntry->status === App\Enums\JournalEntryStatus::DRAFT ? 'warning' : 'secondary') }}">
                            {{ $journalEntry->status->label() }}
                        </span>
                    </p>
                </div>
            </div>

            @if($journalEntry->posted_at)
                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Fecha de Contabilización</label>
                        <p class="fw-bold">{{ $journalEntry->posted_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Contabilizado Por</label>
                        <p class="fw-bold">{{ $journalEntry->postedBy->name ?? 'N/A' }}</p>
                    </div>
                </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-12 mb-3">
                    <label class="form-label text-muted small">Descripción</label>
                    <p class="fw-bold">{{ $journalEntry->description }}</p>
                </div>
            </div>

            @if($journalEntry->source_type)
                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Documento de Origen</label>
                        <p class="fw-bold">{{ $journalEntry->document_type }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Número de Documento</label>
                        <p class="fw-bold">{{ $journalEntry->document_number }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Líneas del Asiento -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Líneas del Asiento</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 10%">Código</th>
                        <th>Cuenta Contable</th>
                        <th style="width: 15%">Centro de Costo</th>
                        <th style="width: 12%; text-align: right">Débito</th>
                        <th style="width: 12%; text-align: right">Crédito</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($journalEntry->journalEntryLines as $line)
                        <tr>
                            <td>
                                <span class="badge badge-light text-dark">{{ $line->accountingAccount->code }}</span>
                            </td>
                            <td>
                                <strong>{{ $line->accountingAccount->name }}</strong>
                            </td>
                            <td>
                                @if($line->costCenter)
                                    <small>{{ $line->costCenter->code }} - {{ $line->costCenter->name }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td style="text-align: right">
                                @if($line->debit > 0)
                                    <strong class="text-success">{{ number_format($line->debit, 2) }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="text-align: right">
                                @if($line->credit > 0)
                                    <strong class="text-danger">{{ number_format($line->credit, 2) }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $line->description ?? '-' }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No hay líneas registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <th colspan="3" style="text-align: right">TOTALES:</th>
                        <th style="text-align: right">
                            <strong class="text-success">{{ number_format($journalEntry->getTotalDebit(), 2) }}</strong>
                        </th>
                        <th style="text-align: right">
                            <strong class="text-danger">{{ number_format($journalEntry->getTotalCredit(), 2) }}</strong>
                        </th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align: right">BALANCEADO:</th>
                        <th colspan="3">
                            @if($journalEntry->isBalanced())
                                <span class="badge badge-success">✓ Sí</span>
                            @else
                                <span class="badge badge-danger">✗ No</span>
                            @endif
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Auditoría -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Información de Auditoría</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small">Creado Por</label>
                    <p class="fw-bold">{{ $journalEntry->createdBy->name ?? 'N/A' }} el {{ $journalEntry->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small">Última Actualización</label>
                    <p class="fw-bold">{{ $journalEntry->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
