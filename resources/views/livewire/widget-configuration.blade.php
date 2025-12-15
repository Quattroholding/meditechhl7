<div>
    <!-- Button to open configuration modal -->
    <button wire:click="openModal" class="btn btn-outline-primary btn-sm" title="Configurar Widgets">
        <i class="feather-settings"></i> Configurar Dashboard
    </button>

    <!-- Configuration Modal -->
    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
        <div class="modal-content" @click.stop>
            <div class="modal-header">
                <h5 class="modal-title">Configurar Widgets del Dashboard</h5>
                <button type="button" class="btn-close" wire:click="closeModal"></button>
            </div>
            <div class="modal-body">
                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="feather-check-circle me-2"></i>
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Grid Visual Guide -->
                <div class="mb-4">
                    <h6 class="mb-2">
                        <i class="feather-grid me-2"></i>Guía de Grid Dashboard
                    </h6>
                    <p class="text-muted small mb-3">
                        El dashboard está dividido en <strong>12 columnas (a-l)</strong> por filas.
                        Las posiciones se definen como: <code>columnaInicio+fila:columnaFin+fila</code>
                    </p>

                    <!-- Visual Grid Reference -->
                    <div class="grid-reference mb-3">
                        <div class="grid-header">
                            <div class="grid-label"></div>
                            <div class="grid-col-label">a</div>
                            <div class="grid-col-label">b</div>
                            <div class="grid-col-label">c</div>
                            <div class="grid-col-label">d</div>
                            <div class="grid-col-label">e</div>
                            <div class="grid-col-label">f</div>
                            <div class="grid-col-label">g</div>
                            <div class="grid-col-label">h</div>
                            <div class="grid-col-label">i</div>
                            <div class="grid-col-label">j</div>
                            <div class="grid-col-label">k</div>
                            <div class="grid-col-label">l</div>
                        </div>
                        <div class="grid-body">
                            @for($row = 1; $row <= 4; $row++)
                                <div class="grid-row">
                                    <div class="grid-row-label">{{ $row }}</div>
                                    @foreach(['a','b','c','d','e','f','g','h','i','j','k','l'] as $col)
                                        <div class="grid-cell" data-col="{{ $col }}" data-row="{{ $row }}"></div>
                                    @endforeach
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Quick Examples -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <div class="example-box">
                                <strong>25% ancho (3 cols):</strong>
                                <code>a1:c1</code>, <code>d1:f1</code>, <code>g1:i1</code>, <code>j1:l1</code>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="example-box">
                                <strong>50% ancho (6 cols):</strong>
                                <code>a1:f1</code>, <code>g1:l1</code>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="example-box">
                                <strong>100% ancho (12 cols):</strong>
                                <code>a1:l1</code>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="example-box">
                                <strong>2 filas altura:</strong>
                                <code>a1:f2</code> (fila 1 a 2)
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <h6>
                        <i class="feather-settings me-2"></i>Configuración de Widgets
                    </h6>
                    <p class="text-muted small">Selecciona los widgets que deseas ver, arrastra para reordenar y configura su tamaño.</p>
                </div>
                <div id="widget-list" class="list-group">
                    @foreach($widgets as $index => $widget)
                    <div class="list-group-item"
                         data-widget-key="{{ $widget['key'] }}"
                         data-widget-description="{{ $widget['description'] }}"
                         data-widget-position="{{ $widget['position'] ?? 'auto' }}"
                         onmouseover="highlightWidgetPosition('{{ $widget['position'] ?? 'auto' }}')"
                         onmouseout="highlightWidgetPosition(null)"
                         style="cursor: move; {{ !$widget['is_visible'] ? 'opacity: 0.6;' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-center">
                                <i class="feather-menu me-2 text-muted" style="cursor: grab;"></i>
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           {{ $widget['is_visible'] ? 'checked' : '' }}
                                           wire:change="toggleWidget('{{ $widget['key'] }}')"
                                           id="widget-{{ $widget['key'] }}">
                                    <label class="form-check-label" for="widget-{{ $widget['key'] }}">
                                        {{ $widget['description'] }}
                                    </label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary">{{ $widget['order_position'] }}</span>
                            </div>
                        </div>

                        <!-- Width Configuration -->
                        <div class="mt-2 ps-4">
                            <label class="form-label small text-muted">Ancho del widget:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button"
                                        class="btn btn-sm {{ $widget['width'] === 'col-lg-3' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                        wire:click="changeWidgetWidth('{{ $widget['key'] }}', 'col-lg-3')"
                                        title="25% de ancho">
                                    25%
                                </button>
                                <button type="button"
                                        class="btn btn-sm {{ $widget['width'] === 'col-lg-4' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                        wire:click="changeWidgetWidth('{{ $widget['key'] }}', 'col-lg-4')"
                                        title="33% de ancho">
                                    33%
                                </button>
                                <button type="button"
                                        class="btn btn-sm {{ $widget['width'] === 'col-lg-6' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                        wire:click="changeWidgetWidth('{{ $widget['key'] }}', 'col-lg-6')"
                                        title="50% de ancho">
                                    50%
                                </button>
                                <button type="button"
                                        class="btn btn-sm {{ $widget['width'] === 'col-lg-9' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                        wire:click="changeWidgetWidth('{{ $widget['key'] }}', 'col-lg-9')"
                                        title="75% de ancho">
                                    75%
                                </button>
                                <button type="button"
                                        class="btn btn-sm {{ $widget['width'] === 'col-lg-12' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                        wire:click="changeWidgetWidth('{{ $widget['key'] }}', 'col-lg-12')"
                                        title="100% de ancho">
                                    100%
                                </button>
                            </div>
                        </div>

                        <!-- Height Configuration -->
                        <div class="mt-2 ps-4">
                            <label class="form-label small text-muted">Alto del widget (filas):</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button"
                                        class="btn btn-sm {{ ($widget['height'] ?? 1) === 1 ? 'btn-primary' : 'btn-outline-secondary' }}"
                                        wire:click="changeWidgetHeight('{{ $widget['key'] }}', 1)"
                                        title="1 fila de alto">
                                    1x
                                </button>
                                <button type="button"
                                        class="btn btn-sm {{ ($widget['height'] ?? 1) === 2 ? 'btn-primary' : 'btn-outline-secondary' }}"
                                        wire:click="changeWidgetHeight('{{ $widget['key'] }}', 2)"
                                        title="2 filas de alto">
                                    2x
                                </button>
                                <button type="button"
                                        class="btn btn-sm {{ ($widget['height'] ?? 1) === 3 ? 'btn-primary' : 'btn-outline-secondary' }}"
                                        wire:click="changeWidgetHeight('{{ $widget['key'] }}', 3)"
                                        title="3 filas de alto">
                                    3x
                                </button>
                                <button type="button"
                                        class="btn btn-sm {{ ($widget['height'] ?? 1) === 4 ? 'btn-primary' : 'btn-outline-secondary' }}"
                                        wire:click="changeWidgetHeight('{{ $widget['key'] }}', 4)"
                                        title="4 filas de alto">
                                    4x
                                </button>
                            </div>
                        </div>

                        <!-- Spatie Position Display -->
                        <div class="mt-2 ps-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-muted">
                                    <i class="feather-grid me-1"></i>
                                    Posición: <code class="position-badge">{{ $widget['position'] ?? 'auto' }}</code>
                                </small>
                                <small class="text-muted">
                                    <i class="feather-info me-1"></i>
                                    Pasa el mouse sobre el widget para ver su posición en el grid
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-outline-danger me-auto"
                        onclick="confirmReset()"
                        title="Restablecer configuración por defecto">
                    <i class="feather-refresh-cw me-1"></i> Restablecer por defecto
                </button>
                <button type="button" class="btn btn-secondary" wire:click="closeModal">Guardar y Cerrar</button>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        console.log('🟢 Widget Configuration script loading...');
        console.log('🟢 Sortable available:', typeof Sortable !== 'undefined');

        // Función para visualizar posición de widget en el grid
        window.highlightWidgetPosition = function(position) {
            console.log('🎨 Highlighting position:', position);

            // Limpiar highlights anteriores
            document.querySelectorAll('.grid-cell').forEach(cell => {
                cell.classList.remove('selected');
            });

            if (!position || position === 'auto') {
                console.log('⚠️ No position to highlight');
                return;
            }

            try {
                // Parsear posición: "a1:f2" -> start: a1, end: f2
                const [start, end] = position.split(':');
                const startCol = start.charAt(0);
                const startRow = parseInt(start.slice(1));
                const endCol = end.charAt(0);
                const endRow = parseInt(end.slice(1));

                console.log('📍 Position parsed:', { startCol, startRow, endCol, endRow });

                // Highlight todas las celdas en el rango
                document.querySelectorAll('.grid-cell').forEach(cell => {
                    const cellCol = cell.getAttribute('data-col');
                    const cellRow = parseInt(cell.getAttribute('data-row'));

                    // Comparar columnas (a=1, b=2, etc.)
                    const cellColNum = cellCol.charCodeAt(0) - 96;
                    const startColNum = startCol.charCodeAt(0) - 96;
                    const endColNum = endCol.charCodeAt(0) - 96;

                    // Si la celda está dentro del rango
                    if (cellRow >= startRow && cellRow <= endRow &&
                        cellColNum >= startColNum && cellColNum <= endColNum) {
                        cell.classList.add('selected');
                    }
                });

                console.log('✅ Position highlighted');
            } catch (error) {
                console.error('❌ Error highlighting position:', error);
            }
        };

        // Función para confirmar reset
        window.confirmReset = function() {
            console.log('🔄 confirmReset called');
            if (confirm('¿Estás seguro de restablecer la configuración por defecto? Se perderán todos los cambios personalizados.')) {
                console.log('✅ User confirmed reset');

                // Buscar el componente Livewire
                const modalContent = document.querySelector('.modal-content');
                if (modalContent) {
                    const livewireElement = modalContent.closest('[wire\\:id]');
                    if (livewireElement) {
                        const wireId = livewireElement.getAttribute('wire:id');
                        console.log('📞 Found Livewire component:', wireId);

                        const component = window.Livewire.find(wireId);
                        if (component) {
                            console.log('🎯 Calling resetToDefaults...');
                            component.call('resetToDefaults')
                                .then(() => {
                                    console.log('✅ Reset completed successfully');
                                })
                                .catch(error => {
                                    console.error('❌ Error resetting:', error);
                                });
                        } else {
                            console.error('❌ Component not found');
                        }
                    } else {
                        console.error('❌ Livewire element not found');
                    }
                } else {
                    console.error('❌ Modal content not found');
                }
            } else {
                console.log('❌ User cancelled reset');
            }
        };

        // Global function to initialize sortable (can be called from anywhere)
        window.initWidgetSortable = function() {
            console.log('🔧 initWidgetSortable called');

            const widgetList = document.getElementById('widget-list');
            console.log('🔍 Widget list element:', widgetList);

            if (!widgetList) {
                console.log('❌ widget-list not found');
                return false;
            }

            if (widgetList.sortableInstance) {
                console.log('⚠️ Sortable already initialized, destroying first');
                widgetList.sortableInstance.destroy();
                delete widgetList.sortableInstance;
            }

            if (typeof Sortable === 'undefined') {
                console.error('❌ Sortable library not available');
                return false;
            }

            try {
                console.log('🚀 Creating new Sortable instance...');

                const sortable = new Sortable(widgetList, {
                    animation: 200,
                    delay: 0,
                    delayOnTouchStart: true,
                    delayOnTouchOnly: false,
                    touchStartThreshold: 5,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    forceFallback: false,
                    fallbackClass: 'sortable-fallback',
                    fallbackOnBody: true,
                    swapThreshold: 1,
                    invertSwap: false,
                    invertedSwapThreshold: 1,
                    direction: 'vertical',

                    onChoose: function(evt) {
                        console.log('👆 Item chosen:', evt.item);
                    },

                    onStart: function(evt) {
                        console.log('🎯 Drag started - item:', evt.item.textContent.trim());
                        console.log('🎯 Old index:', evt.oldIndex);
                        evt.item.style.opacity = '0.7';
                        document.body.style.cursor = 'grabbing';
                    },

                    onEnd: function(evt) {
                        console.log('🎯 Drag ended - old index:', evt.oldIndex, 'new index:', evt.newIndex);
                        evt.item.style.opacity = '1';
                        document.body.style.cursor = '';

                        if (evt.oldIndex !== evt.newIndex) {
                            console.log('🔄 Position changed, updating order...');

                            const orderedWidgets = [];
                            const items = widgetList.querySelectorAll('.list-group-item');
                            console.log('📋 Found', items.length, 'items to reorder');

                            items.forEach((item, index) => {
                                const widgetKey = item.getAttribute('data-widget-key');
                                const widgetDescription = item.getAttribute('data-widget-description');
                                const checkbox = item.querySelector('input[type="checkbox"]');
                                const widget = {
                                    key: widgetKey,
                                    description:widgetDescription,
                                    is_visible: checkbox ? checkbox.checked : true,
                                    order_position: index + 1
                                };
                                orderedWidgets.push(widget);
                                console.log(`📝 Item ${index + 1}: ${widgetKey} (visible: ${widget.is_visible})`);
                            });

                            console.log('📤 Sending order update to server...');

                            // Try multiple ways to find and call the Livewire component
                            const livewireComponent = widgetList.closest('[wire\\:id]');
                            if (livewireComponent) {
                                const wireId = livewireComponent.getAttribute('wire:id');
                                console.log('🔗 Found Livewire component:', wireId);

                                const component = window.Livewire.find(wireId);
                                if (component && typeof component.call === 'function') {
                                    console.log('📞 Calling updateOrder method...');
                                    component.call('updateOrder', orderedWidgets)
                                        .then(() => {
                                            console.log('✅ Order updated successfully');
                                        })
                                        .catch((error) => {
                                            console.error('❌ Error updating order:', error);
                                        });
                                } else {
                                    console.error('❌ Component.call not available');
                                }
                            } else {
                                console.error('❌ Livewire component not found');
                            }
                        } else {
                            console.log('➡️ No position change detected');
                        }
                    },

                    onMove: function(evt) {
                        console.log('🔄 Item moving...');
                        return true; // Allow the move
                    }
                });

                widgetList.sortableInstance = sortable;
                console.log('✅ Sortable initialized successfully!');

                // Test drag capability
                const items = widgetList.querySelectorAll('.list-group-item');
                console.log(`🎯 Found ${items.length} draggable items`);

                return true;

            } catch (error) {
                console.error('❌ Error creating Sortable:', error);
                return false;
            }
        };

        // Try multiple initialization strategies
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🟢 DOM loaded, setting up event listeners...');

            // Strategy 1: Direct click listener on the button
            document.addEventListener('click', function(e) {
                if (e.target.closest('[wire\\:click="openModal"]')) {
                    console.log('🔘 Modal button clicked, waiting for modal...');

                    // Wait for modal to appear and initialize
                    let attempts = 0;
                    const checkModal = setInterval(() => {
                        attempts++;
                        console.log(`🔍 Attempt ${attempts}: Looking for modal...`);

                        if (window.initWidgetSortable()) {
                            console.log('✅ Modal found and sortable initialized!');
                            clearInterval(checkModal);
                        } else if (attempts >= 20) {
                            console.log('⏰ Timeout waiting for modal');
                            clearInterval(checkModal);
                        }
                    }, 250);
                }
            });

            // Strategy 2: Livewire events
            if (typeof Livewire !== 'undefined') {
                console.log('🟢 Livewire available, setting up listeners...');

                document.addEventListener('livewire:init', () => {
                    console.log('🟢 Livewire initialized');
                });

                document.addEventListener('livewire:navigated', () => {
                    console.log('🟢 Livewire navigated');
                });

                Livewire.on('refresh', () => {
                    console.log('🟢 Livewire refresh event');
                    setTimeout(() => window.initWidgetSortable(), 100);
                });
            }

            // Strategy 3: Mutation observer
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                if (node.id === 'widget-list' || node.querySelector('#widget-list')) {
                                    console.log('🔍 Modal detected via mutation observer');
                                    setTimeout(() => window.initWidgetSortable(), 100);
                                }
                            }
                        });
                    }
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            console.log('🟢 All initialization strategies set up');
        });

        // Listen for dashboard reload event
        document.addEventListener('livewire:init', () => {
            Livewire.on('reload-dashboard', () => {
                console.log('🔄 Reloading dashboard...');
                // Remove show_salute parameter from URL if present
                const url = new URL(window.location.href);
                url.searchParams.delete('show_salute');
                window.location.href = url.toString();
            });
        });
    </script>

    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        /* Grid Reference Styles */
        .grid-reference {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            overflow-x: auto;
        }

        .grid-header {
            display: grid;
            grid-template-columns: 30px repeat(12, 1fr);
            gap: 2px;
            margin-bottom: 2px;
        }

        .grid-body {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .grid-row {
            display: grid;
            grid-template-columns: 30px repeat(12, 1fr);
            gap: 2px;
        }

        .grid-label,
        .grid-row-label {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 11px;
            color: #6c757d;
            background: #e9ecef;
            border-radius: 3px;
        }

        .grid-col-label {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 11px;
            color: #495057;
            background: #e9ecef;
            border-radius: 3px;
            padding: 4px;
            min-width: 30px;
        }

        .grid-cell {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            min-height: 35px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .grid-cell:hover {
            background: #e7f3ff;
            border-color: #2196f3;
            transform: scale(1.05);
        }

        .grid-cell.selected {
            background: #2196f3;
            border-color: #1976d2;
        }

        .example-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 10px;
            font-size: 13px;
        }

        .example-box code {
            background: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            color: #d63384;
        }

        .position-badge {
            background: #e7f3ff;
            color: #0066cc;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid #2196f3;
        }

        .list-group-item:hover {
            box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
        }

        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end;
        }

        .sortable-ghost {
            opacity: 0.4;
            background: #e3f2fd !important;
            border: 2px dashed #2196f3 !important;
        }

        .sortable-chosen {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .sortable-drag {
            transform: rotate(2deg);
        }

        .list-group-item {
            transition: all 0.2s ease;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            background: white;
            margin-bottom: 0.25rem;
            border-radius: 4px;
            user-select: none;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .feather-menu {
            cursor: grab;
        }

        .feather-menu:active {
            cursor: grabbing;
        }
    </style>
    @endpush
</div>
