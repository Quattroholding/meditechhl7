<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title') Reportes @endslot
                @slot('li_1') Sistema de Reportes @endslot
            @endcomponent

            <div class="row">
                @forelse($reports as $report)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <i class="fa fa-file-alt fa-3x text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="card-title mb-0">{{ $report->getTitle() }}</h5>
                                    </div>
                                </div>
                                <p class="card-text text-muted">{{ $report->getDescription() }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('reports.show', $report->getName()) }}"
                                       class="btn btn-primary w-100">
                                        <i class="fa fa-chart-bar me-2"></i> Generar Reporte
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            No hay reportes disponibles para tu rol.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
