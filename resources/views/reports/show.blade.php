<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            @component('components.page-header')
                @slot('title') {{ $report->getTitle() }} @endslot
                @slot('li_1')
                    <a href="{{ route('reports.index') }}">Reportes</a>
                @endslot
            @endcomponent

            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fa fa-filter me-2"></i>Filtros del Reporte
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-4">{{ $report->getDescription() }}</p>

                            <form id="reportForm">
                                @csrf
                                @foreach($filters as $key => $filter)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">{{ $filter['label'] }}</label>

                                        @if($filter['type'] === 'date')
                                            <input type="date"
                                                   name="{{ $key }}"
                                                   class="form-control"
                                                   value="{{ $filter['default'] ?? '' }}"
                                                {{ $filter['required'] ?? false ? 'required' : '' }}>

                                        @elseif($filter['type'] === 'select')
                                            <select name="{{ $key }}{{ ($filter['multiple'] ?? false) ? '[]' : '' }}"
                                                    class="form-select"
                                                {{ ($filter['multiple'] ?? false) ? 'multiple' : '' }}
                                                {{ $filter['required'] ?? false ? 'required' : '' }}>
                                                <option value="">{{ ($filter['multiple'] ?? false) ? 'Seleccionar...' : 'Todos' }}</option>
                                                @foreach($filter['options'] as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        @elseif($filter['type'] === 'datalist')
                                            <input name="{{$key}}" type="text" list="myOptions{{$key}}" placeholder="Buscar..."    class="form-control">
                                            <datalist id="myOptions{{$key}}">
                                                @foreach($filter['options'] as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </datalist>
                                        @endif
                                    </div>
                                @endforeach

                                <hr class="my-4">

                                <div class="row g-2">
                                    @if($report->canExportExcel())
                                        <div class="col-md-4">
                                            <button type="button"
                                                    class="btn btn-primary w-100"
                                                    onclick="exportReport('excel')">
                                                <i class="fa fa-file-excel me-2"></i> Exportar a Excel
                                            </button>
                                        </div>
                                    @endif

                                    @if($report->canExportPdf())
                                        <div class="col-md-4">
                                            <button type="button"
                                                    class="btn btn-secondary w-100"
                                                    onclick="exportReport('pdf')">
                                                <i class="fa fa-file-pdf me-2"></i> Exportar a PDF
                                            </button>
                                        </div>
                                    @endif
                                    <div class="col-md-4">
                                        <a href="{{route('reports.index')}}"
                                                class="btn btn-danger w-100"
                                                onclick="exportReport('pdf')">
                                            <i class="fa fa-close"></i>Cancelar
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function exportReport(format) {
                const form = document.getElementById('reportForm');
                const formData = new FormData(form);

                // Mostrar loading
                const btn = event.target;
                const originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Generando...';

                fetch(`{{ route('reports.show', $report->getName()) }}/${format}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Error al generar reporte');
                        return response.blob();
                    })
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `{{ $report->getName() }}-${new Date().toISOString().slice(0,10)}.${format === 'excel' ? 'xlsx' : 'pdf'}`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        a.remove();

                        // Restaurar botón
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    })
                    .catch(error => {
                        alert('Error al generar el reporte: ' + error.message);
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    });
            }
        </script>
    @endpush
</x-app-layout>
