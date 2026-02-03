<div>
    <!-- Button to open configuration modal -->
    <button wire:click="openModal" class="btn btn-outline-primary btn-sm" title="Configurar Widgets">
        <i class="feather-settings"></i> Configurar Dashboard
    </button>

    <!-- Configuration Modal -->
    @if($showModal)
    <div class="modal-overlay-v2" @click="closeModal">
        <div class="modal-content-v2" @click.stop>
            <div class="modal-header-v2">
                <h5 class="modal-title">
                    <i class="feather-grid me-2"></i>
                    Configurador Visual de Dashboard
                </h5>
                <button type="button" class="btn-close" wire:click="closeModal"></button>
            </div>

            <div class="modal-body-v2">
               @include('partials.message')

                <!-- Instructions -->
                <div class="instructions-banner mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="feather-info text-primary"></i>
                        <div>
                            <strong>Cómo usar:</strong>
                            <span class="text-muted">
                                Arrastra widgets desde "Disponibles" hacia la grilla.
                                Redimensiona con las esquinas.
                                Arrastra dentro de la grilla para reposicionar.
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Panel: Available Widgets -->
                    <div class="col-md-3">
                        <div class="available-widgets-panel">
                            <h6 class="panel-title">
                                <i class="feather-package me-2"></i>
                                Widgets Disponibles
                            </h6>
                            <small class="text-muted d-block mb-3">
                                Arrastra hacia la grilla →
                            </small>

                            <div id="available-widgets" class="available-widgets-list" wire:key="available-widgets-{{ now()->timestamp }}">
                                @foreach($widgets as $widget)
                                    @if(!$widget['is_visible'])
                                    <div class="available-widget-item"
                                         data-widget-key="{{ $widget['key'] }}"
                                         data-widget-name="{{ $widget['name'] }}"
                                         data-widget-description="{{ $widget['description'] }}"
                                         draggable="true"
                                         wire:key="avail-{{ $widget['key'] }}">
                                        <div class="widget-icon">
                                            <i class="feather-grid"></i>
                                        </div>
                                        <div class="widget-info">
                                            <div class="widget-name">{{ $widget['description'] }}</div>
                                            <small class="text-muted">{{ $widget['name'] }}</small>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel: Interactive Grid -->
                    <div class="col-md-9">
                        <div class="grid-panel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="panel-title mb-0">
                                    <i class="feather-layout me-2"></i>
                                    Dashboard Grid (12 columnas)
                                </h6>
                                <div class="grid-actions">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="addGridRow()">
                                        <i class="feather-plus"></i> Agregar Fila
                                    </button>
                                </div>
                            </div>

                            <!-- Column Headers -->
                            <div class="grid-headers">
                                <div class="grid-row-header"></div>
                                @foreach(['a','b','c','d','e','f','g','h','i','j','k','l'] as $col)
                                    <div class="grid-col-header">{{ $col }}</div>
                                @endforeach
                            </div>

                            <!-- Grid Container -->
                            <div class="grid-container-wrapper">
                                @php
                                    // Calculate maximum row needed based on widget positions
                                    $maxRow = 6; // Default minimum
                                    foreach($widgets as $widget) {
                                        if (isset($widget['position']) && $widget['position']) {
                                            try {
                                                [$start, $end] = explode(':', $widget['position']);
                                                $endRow = (int)substr($end, 1);
                                                if ($endRow > $maxRow) {
                                                    $maxRow = $endRow;
                                                }
                                            } catch (\Exception $e) {
                                                // Ignore malformed positions
                                            }
                                        }
                                    }
                                    // Add 2 extra rows for adding new widgets
                                    $totalRows = $maxRow + 2;
                                @endphp
                                <!-- Grid Body -->
                                <div id="dashboard-grid" class="dashboard-grid">
                                    <!-- Rows will be generated dynamically based on widgets -->
                                    @for($row = 1; $row <= $totalRows; $row++)
                                    <div class="grid-row" data-row="{{ $row }}">
                                        <div class="grid-row-header">{{ $row }}</div>
                                        @foreach(['a','b','c','d','e','f','g','h','i','j','k','l'] as $col)
                                            <div class="grid-cell-dropzone"
                                                 data-col="{{ $col }}"
                                                 data-row="{{ $row }}"
                                                 data-position="{{ $col }}{{ $row }}">
                                            </div>
                                        @endforeach
                                    </div>
                                    @endfor
                                </div>

                                <!-- Widgets Layer (absolute positioned over grid) -->
                                <div id="widgets-layer" class="widgets-layer" wire:key="widgets-layer-{{ now()->timestamp }}">
                                @foreach($widgets as $widget)
                                    @if($widget['is_visible'] && isset($widget['position']))
                                    @php
                                        // Parse position to calculate CSS Grid positioning
                                        try {
                                            [$start, $end] = explode(':', $widget['position']);
                                            $startCol = ord($start[0]) - 96; // a=1, b=2, etc.
                                            $startRow = (int)substr($start, 1);
                                            $endCol = ord($end[0]) - 96;
                                            $endRow = (int)substr($end, 1);

                                            // CSS Grid uses 1-based indexing
                                            // Add 1 to account for the row header column (40px)
                                            $gridColumnStart = $startCol + 1; // +1 for row header
                                            $gridColumnEnd = $endCol + 2; // +2 because end is exclusive + row header
                                            $gridRowStart = $startRow;
                                            $gridRowEnd = $endRow + 1; // +1 because end is exclusive

                                            \Log::info('Widget grid positioning', [
                                                'widget' => $widget['key'],
                                                'position' => $widget['position'],
                                                'grid' => [
                                                    'column' => "$gridColumnStart / $gridColumnEnd",
                                                    'row' => "$gridRowStart / $gridRowEnd",
                                                ]
                                            ]);
                                        } catch (\Exception $e) {
                                            \Log::error('Error calculating widget position', [
                                                'widget' => $widget['key'],
                                                'position' => $widget['position'],
                                                'error' => $e->getMessage()
                                            ]);
                                            // Fallback to default position
                                            $gridColumnStart = 2;
                                            $gridColumnEnd = 5;
                                            $gridRowStart = 1;
                                            $gridRowEnd = 2;
                                        }
                                    @endphp
                                    <div class="grid-widget"
                                         data-widget-key="{{ $widget['key'] }}"
                                         data-position="{{ $widget['position'] }}"
                                         style="grid-column: {{ $gridColumnStart }} / {{ $gridColumnEnd }}; grid-row: {{ $gridRowStart }} / {{ $gridRowEnd }};">
                                        <div class="widget-header">
                                            <span class="widget-title">{{ $widget['description'] }}</span>
                                            <button class="widget-remove" onclick="removeWidget('{{ $widget['key'] }}')">
                                                <i class="fa fa-close"></i>
                                            </button>
                                        </div>
                                        <div class="widget-body">
                                            <i class="feather-move"></i>
                                            <small class="text-muted d-block mt-2">{{ $widget['position'] }}</small>
                                        </div>
                                        <div class="resize-handle resize-se"></div>
                                    </div>
                                    @endif
                                @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer-v2">
                <button type="button" class="btn btn-outline-danger me-auto" onclick="confirmReset()">
                    <i class="feather-refresh-cw me-1"></i> Restablecer
                </button>
                <button type="button" class="btn btn-secondary" wire:click="closeModal">
                    <i class="feather-save me-1"></i> Guardar y Cerrar
                </button>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        // Global state
        let gridState = {
            widgets: {},
            draggedWidget: null,
            resizingWidget: null,
            startPos: { x: 0, y: 0 },
            startSize: { width: 0, height: 0 },
            initialized: false
        };

        // Wait for DOM to be ready, then initialize
        document.addEventListener('DOMContentLoaded', () => {
            console.log('🎯 DOM Ready, initializing drag & drop');
            initializeDragAndDrop();
        });

        // Also initialize when Livewire is ready
        document.addEventListener('livewire:initialized', () => {
            console.log('🎯 Livewire initialized');
            setTimeout(() => initializeDragAndDrop(), 100);

            // Listen for reload-page event
            Livewire.on('reload-page', () => {
                console.log('🔄 Reloading page...');
                window.location.reload();
            });
        });

        // Initialize drag and drop
        function initializeDragAndDrop() {
            console.log('🎯 Initializing Drag & Drop Grid');

            // Make available widgets draggable
            document.querySelectorAll('.available-widget-item').forEach(item => {
                // Remove old listeners to avoid duplicates
                item.removeEventListener('dragstart', handleDragStart);
                item.removeEventListener('dragend', handleDragEnd);
                // Add new listeners
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
            });

            // Make grid cells drop targets
            document.querySelectorAll('.grid-cell-dropzone').forEach(cell => {
                // Remove old listeners to avoid duplicates
                cell.removeEventListener('dragover', handleDragOver);
                cell.removeEventListener('drop', handleDrop);
                cell.removeEventListener('dragleave', handleDragLeave);
                // Add new listeners
                cell.addEventListener('dragover', handleDragOver);
                cell.addEventListener('drop', handleDrop);
                cell.addEventListener('dragleave', handleDragLeave);
            });

            // Make placed widgets draggable within grid
            document.querySelectorAll('.grid-widget').forEach(widget => {
                makeWidgetDraggable(widget);
                makeWidgetResizable(widget);
            });
        }

        // Drag handlers for available widgets
        function handleDragStart(e) {
            const widgetKey = e.target.closest('.available-widget-item').dataset.widgetKey;
            const widgetName = e.target.closest('.available-widget-item').dataset.widgetDescription;

            gridState.draggedWidget = {
                key: widgetKey,
                name: widgetName,
                isNew: true
            };

            e.target.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            console.log('🎨 Dragging widget:', widgetKey);
        }

        function handleDragEnd(e) {
            e.target.classList.remove('dragging');
            document.querySelectorAll('.grid-cell-dropzone').forEach(cell => {
                cell.classList.remove('drag-over');
            });
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            e.target.classList.add('drag-over');
        }

        function handleDragLeave(e) {
            e.target.classList.remove('drag-over');
        }

        function handleDrop(e) {
            e.preventDefault();
            e.target.classList.remove('drag-over');

            if (!gridState.draggedWidget) return;

            const col = e.target.dataset.col;
            const row = e.target.dataset.row;

            console.log('📍 Dropped at:', col, row);

            // Calculate default position (3 columns wide, 1 row tall)
            const startCol = col;
            const endColCode = col.charCodeAt(0) + 2; // +2 for 3 columns
            const endCol = String.fromCharCode(Math.min(endColCode, 'l'.charCodeAt(0)));
            const position = `${startCol}${row}:${endCol}${row}`;

            console.log('✅ Widget position:', position);

            // Add widget to grid via Livewire
            addWidgetToGrid(gridState.draggedWidget.key, position);

            gridState.draggedWidget = null;
        }

        // Add widget to grid
        function addWidgetToGrid(widgetKey, position) {
            console.log('➕ Adding widget to grid:', widgetKey, position);

            // Call Livewire method to update backend
            const livewireComponent = document.querySelector('[wire\\:id]');
            if (livewireComponent) {
                const wireId = livewireComponent.getAttribute('wire:id');
                const component = window.Livewire.find(wireId);

                if (component) {
                    component.call('placeWidgetOnGrid', widgetKey, position)
                        .then(() => {
                            console.log('✅ Widget placed successfully, reinitializing...');
                            setTimeout(() => initializeDragAndDrop(), 300);
                        })
                        .catch(error => {
                            console.error('❌ Error placing widget:', error);
                        });
                }
            }
        }

        // Remove widget from grid
        function removeWidget(widgetKey) {
            console.log('🗑️ Removing widget:', widgetKey);

            const livewireComponent = document.querySelector('[wire\\:id]');
            if (livewireComponent) {
                const wireId = livewireComponent.getAttribute('wire:id');
                const component = window.Livewire.find(wireId);

                if (component) {
                    component.call('removeWidgetFromGrid', widgetKey)
                        .then(() => {
                            console.log('✅ Widget removed successfully, reinitializing...');
                            setTimeout(() => initializeDragAndDrop(), 300);
                        });
                }
            }
        }

        // Make widget draggable within grid
        function makeWidgetDraggable(widget) {
            const header = widget.querySelector('.widget-header');

            header.addEventListener('mousedown', (e) => {
                if (e.target.closest('.widget-remove')) return;

                gridState.resizingWidget = null;
                widget.classList.add('dragging-grid');

                const widgetKey = widget.dataset.widgetKey;
                const currentPosition = widget.dataset.position;

                console.log('🎯 Started dragging widget:', widgetKey);

                // Parse current position
                const [start, end] = currentPosition.split(':');
                const startCol = start.charAt(0);
                const startRow = parseInt(start.slice(1));
                const endCol = end.charAt(0);
                const endRow = parseInt(end.slice(1));

                const widthCols = (endCol.charCodeAt(0) - startCol.charCodeAt(0)) + 1;
                const heightRows = endRow - startRow + 1;

                const grid = document.getElementById('dashboard-grid');
                const gridRect = grid.getBoundingClientRect();
                const cellWidth = gridRect.width / 12;
                const cellHeight = 82; // Approximate cell height

                let isDragging = false;
                let startMouseX = e.clientX;
                let startMouseY = e.clientY;

                function onMouseMove(e) {
                    const deltaX = e.clientX - startMouseX;
                    const deltaY = e.clientY - startMouseY;

                    // Only start dragging if moved more than 5px
                    if (!isDragging && (Math.abs(deltaX) > 5 || Math.abs(deltaY) > 5)) {
                        isDragging = true;
                    }

                    if (!isDragging) return;

                    // Calculate new grid position based on mouse movement
                    const colsToMove = Math.round(deltaX / cellWidth);
                    const rowsToMove = Math.round(deltaY / cellHeight);

                    if (colsToMove !== 0 || rowsToMove !== 0) {
                        // Calculate new start position
                        const newStartColNum = Math.max(1, Math.min(12 - widthCols + 1,
                            (startCol.charCodeAt(0) - 96) + colsToMove));
                        const newStartRow = Math.max(1, startRow + rowsToMove);

                        const newStartCol = String.fromCharCode(96 + newStartColNum);
                        const newEndColNum = newStartColNum + widthCols - 1;
                        const newEndCol = String.fromCharCode(96 + newEndColNum);
                        const newEndRow = newStartRow + heightRows - 1;

                        const newPosition = `${newStartCol}${newStartRow}:${newEndCol}${newEndRow}`;

                        // Visual feedback
                        highlightGridCells(newPosition);

                        console.log('📍 New position preview:', newPosition);
                    }
                }

                function onMouseUp() {
                    widget.classList.remove('dragging-grid');

                    if (isDragging) {
                        // Calculate final position
                        const mouseX = arguments[0].clientX;
                        const mouseY = arguments[0].clientY;

                        const deltaX = mouseX - startMouseX;
                        const deltaY = mouseY - startMouseY;

                        const colsToMove = Math.round(deltaX / cellWidth);
                        const rowsToMove = Math.round(deltaY / cellHeight);

                        if (colsToMove !== 0 || rowsToMove !== 0) {
                            const newStartColNum = Math.max(1, Math.min(12 - widthCols + 1,
                                (startCol.charCodeAt(0) - 96) + colsToMove));
                            const newStartRow = Math.max(1, startRow + rowsToMove);

                            const newStartCol = String.fromCharCode(96 + newStartColNum);
                            const newEndColNum = newStartColNum + widthCols - 1;
                            const newEndCol = String.fromCharCode(96 + newEndColNum);
                            const newEndRow = newStartRow + heightRows - 1;

                            const newPosition = `${newStartCol}${newStartRow}:${newEndCol}${newEndRow}`;

                            console.log('✅ Moving widget to:', newPosition);

                            // Update via Livewire
                            updateWidgetPosition(widgetKey, newPosition);
                        }
                    }

                    // Clear highlights
                    document.querySelectorAll('.grid-cell-dropzone').forEach(cell => {
                        cell.classList.remove('highlight-preview');
                    });

                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                }

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });
        }

        // Highlight grid cells for preview
        function highlightGridCells(position) {
            // Clear previous highlights
            document.querySelectorAll('.grid-cell-dropzone').forEach(cell => {
                cell.classList.remove('highlight-preview');
            });

            if (!position) return;

            try {
                const [start, end] = position.split(':');
                const startCol = start.charAt(0);
                const startRow = parseInt(start.slice(1));
                const endCol = end.charAt(0);
                const endRow = parseInt(end.slice(1));

                document.querySelectorAll('.grid-cell-dropzone').forEach(cell => {
                    const cellCol = cell.getAttribute('data-col');
                    const cellRow = parseInt(cell.getAttribute('data-row'));

                    const cellColNum = cellCol.charCodeAt(0) - 96;
                    const startColNum = startCol.charCodeAt(0) - 96;
                    const endColNum = endCol.charCodeAt(0) - 96;

                    if (cellRow >= startRow && cellRow <= endRow &&
                        cellColNum >= startColNum && cellColNum <= endColNum) {
                        cell.classList.add('highlight-preview');
                    }
                });
            } catch (error) {
                console.error('Error highlighting cells:', error);
            }
        }

        // Update widget position via Livewire
        function updateWidgetPosition(widgetKey, newPosition) {
            const livewireComponent = document.querySelector('[wire\\:id]');
            if (livewireComponent) {
                const wireId = livewireComponent.getAttribute('wire:id');
                const component = window.Livewire.find(wireId);

                if (component) {
                    component.call('updateWidgetPosition', widgetKey, newPosition)
                        .then(() => {
                            console.log('✅ Widget position updated, reinitializing...');
                            setTimeout(() => initializeDragAndDrop(), 300);
                        })
                        .catch(error => {
                            console.error('❌ Error updating position:', error);
                        });
                }
            }
        }

        // Make widget resizable
        function makeWidgetResizable(widget) {
            const resizeHandle = widget.querySelector('.resize-handle');

            if (!resizeHandle) return;

            resizeHandle.addEventListener('mousedown', (e) => {
                e.stopPropagation();
                e.preventDefault();

                const widgetKey = widget.dataset.widgetKey;
                const currentPosition = widget.dataset.position;

                console.log('🔧 Started resizing widget:', widgetKey);

                // Parse current position
                const [start, end] = currentPosition.split(':');
                const startCol = start.charAt(0);
                const startRow = parseInt(start.slice(1));
                const endCol = end.charAt(0);
                const endRow = parseInt(end.slice(1));

                const grid = document.getElementById('dashboard-grid');
                const gridRect = grid.getBoundingClientRect();
                const cellWidth = gridRect.width / 12;
                const cellHeight = 82;

                gridState.resizingWidget = widgetKey;
                gridState.startPos = { x: e.clientX, y: e.clientY };

                let currentWidthCols = (endCol.charCodeAt(0) - startCol.charCodeAt(0)) + 1;
                let currentHeightRows = endRow - startRow + 1;

                function onMouseMove(e) {
                    const deltaX = e.clientX - gridState.startPos.x;
                    const deltaY = e.clientY - gridState.startPos.y;

                    // Calculate new size in grid cells
                    const colsToAdd = Math.round(deltaX / cellWidth);
                    const rowsToAdd = Math.round(deltaY / cellHeight);

                    const newWidthCols = Math.max(1, Math.min(12, currentWidthCols + colsToAdd));
                    const newHeightRows = Math.max(1, currentHeightRows + rowsToAdd);

                    // Calculate new end position
                    const startColNum = startCol.charCodeAt(0) - 96;
                    const newEndColNum = Math.min(12, startColNum + newWidthCols - 1);
                    const newEndCol = String.fromCharCode(96 + newEndColNum);
                    const newEndRow = startRow + newHeightRows - 1;

                    const newPosition = `${startCol}${startRow}:${newEndCol}${newEndRow}`;

                    // Visual preview
                    highlightGridCells(newPosition);

                    console.log('📏 Resizing preview:', newPosition,
                        `(${newWidthCols} cols × ${newHeightRows} rows)`);
                }

                function onMouseUp(e) {
                    const deltaX = e.clientX - gridState.startPos.x;
                    const deltaY = e.clientY - gridState.startPos.y;

                    const colsToAdd = Math.round(deltaX / cellWidth);
                    const rowsToAdd = Math.round(deltaY / cellHeight);

                    const newWidthCols = Math.max(1, Math.min(12, currentWidthCols + colsToAdd));
                    const newHeightRows = Math.max(1, currentHeightRows + rowsToAdd);

                    // Only update if size changed
                    if (colsToAdd !== 0 || rowsToAdd !== 0) {
                        const startColNum = startCol.charCodeAt(0) - 96;
                        const newEndColNum = Math.min(12, startColNum + newWidthCols - 1);
                        const newEndCol = String.fromCharCode(96 + newEndColNum);
                        const newEndRow = startRow + newHeightRows - 1;

                        const newPosition = `${startCol}${startRow}:${newEndCol}${newEndRow}`;

                        console.log('✅ Resizing widget to:', newPosition);

                        // Update via Livewire
                        updateWidgetPosition(widgetKey, newPosition);
                    }

                    // Clear highlights
                    document.querySelectorAll('.grid-cell-dropzone').forEach(cell => {
                        cell.classList.remove('highlight-preview');
                    });

                    gridState.resizingWidget = null;
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                }

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });
        }

        // Add grid row
        function addGridRow() {
            const grid = document.getElementById('dashboard-grid');
            const currentRows = grid.querySelectorAll('.grid-row').length;
            const newRowNumber = currentRows + 1;

            const newRow = document.createElement('div');
            newRow.className = 'grid-row';
            newRow.dataset.row = newRowNumber;

            newRow.innerHTML = `
                <div class="grid-row-header">${newRowNumber}</div>
                ${['a','b','c','d','e','f','g','h','i','j','k','l'].map(col => `
                    <div class="grid-cell-dropzone" data-col="${col}" data-row="${newRowNumber}" data-position="${col}${newRowNumber}"></div>
                `).join('')}
            `;

            grid.appendChild(newRow);

            // Re-initialize drop handlers
            newRow.querySelectorAll('.grid-cell-dropzone').forEach(cell => {
                cell.addEventListener('dragover', handleDragOver);
                cell.addEventListener('drop', handleDrop);
                cell.addEventListener('dragleave', handleDragLeave);
            });
        }

        // Reset confirmation
        function confirmReset() {
            if (confirm('¿Estás seguro de restablecer la configuración por defecto? Se perderán todos los cambios personalizados.')) {
                const livewireComponent = document.querySelector('[wire\\:id]');
                if (livewireComponent) {
                    const wireId = livewireComponent.getAttribute('wire:id');
                    const component = window.Livewire.find(wireId);
                    if (component) {
                        component.call('resetToDefaults');
                    }
                }
            }
        }

        // Auto-initialize when modal opens
        document.addEventListener('click', function(e) {
            if (e.target.closest('[wire\\:click="openModal"]')) {
                setTimeout(() => initializeDragAndDrop(), 500);
            }
        });
    </script>

    <style>
        /* Modal Styles */
        .modal-overlay-v2 {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            backdrop-filter: blur(2px);
        }

        .modal-content-v2 {
            background: white;
            border-radius: 12px;
            width: 95%;
            max-width: 1400px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
        }

        .modal-header-v2 {
            padding: 1.25rem 1.5rem;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .modal-header-v2 .modal-title {
            margin: 0;
            color: white;
            font-weight: 600;
        }

        .modal-header-v2 .btn-close {
            filter: invert(1);
        }

        .modal-body-v2 {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer-v2 {
            padding: 1rem 1.5rem;
            border-top: 2px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            background: #f8f9fa;
        }

        /* Instructions Banner */
        .instructions-banner {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-left: 4px solid #2196f3;
            padding: 12px 16px;
            border-radius: 8px;
        }

        /* Panels */
        .available-widgets-panel,
        .grid-panel {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            height: 600px;
            overflow-y: auto;
        }

        .panel-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        /* Available Widgets List */
        .available-widgets-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .available-widget-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: 2px solid #5a67d8;
            border-radius: 8px;
            padding: 12px;
            cursor: grab;
            transition: all 0.2s ease;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .available-widget-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .available-widget-item.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }

        .widget-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .widget-info {
            flex: 1;
        }

        .widget-name {
            font-weight: 600;
            font-size: 14px;
        }

        /* Grid Styles */
        .grid-headers {
            display: grid;
            grid-template-columns: 40px repeat(12, 1fr);
            gap: 2px;
            margin-bottom: 2px;
            position: sticky;
            top: 0;
            background: white;
            z-index: 10;
        }

        .grid-row-header,
        .grid-col-header {
            background: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 12px;
            padding: 8px 4px;
            border-radius: 4px;
        }

        .grid-container-wrapper {
            position: relative;
            min-height: 500px;
        }

        .dashboard-grid {
            position: relative;
            z-index: 1;
        }

        .grid-row {
            display: grid;
            grid-template-columns: 40px repeat(12, 1fr);
            gap: 2px;
            margin-bottom: 2px;
        }

        .grid-cell-dropzone {
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            min-height: 80px;
            transition: all 0.2s ease;
            border-radius: 4px;
            position: relative;
            z-index: 1;
        }

        .grid-cell-dropzone.drag-over {
            background: #e7f3ff;
            border-color: #2196f3;
            border-style: solid;
            border-width: 2px;
        }

        .grid-cell-dropzone.highlight-preview {
            background: #fff3cd;
            border-color: #ffc107;
            border-style: solid;
            border-width: 2px;
            animation: pulse-preview 0.5s ease-in-out infinite;
        }

        @keyframes pulse-preview {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        /* Grid Widgets */
        .widgets-layer {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            /* Use CSS Grid to match the dashboard-grid layout */
            display: grid;
            grid-template-columns: 40px repeat(12, 1fr);
            gap: 2px;
            grid-auto-rows: 82px;
            z-index: 10; /* Above grid cells */
        }

        .grid-widget {
            pointer-events: all;
            background: white;
            border: 2px solid #2196f3;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
            display: flex;
            flex-direction: column;
            position: relative;
            min-height: 80px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10; /* Above grid cells */
            /* Grid positioning will be set via grid-column and grid-row */
        }

        .grid-widget:hover {
            box-shadow: 0 8px 20px rgba(33, 150, 243, 0.5);
            transform: translateY(-2px);
        }

        .grid-widget.dragging-grid {
            opacity: 0.7;
            z-index: 1000;
        }

        .widget-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 6px 6px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: move;
            font-weight: 600;
            font-size: 13px;
        }

        .widget-remove {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .widget-remove:hover {
            background: rgba(255, 0, 0, 0.8);
            transform: rotate(90deg);
        }

        .widget-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 24px;
        }

        .resize-handle {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #2196f3;
            border: 2px solid white;
            border-radius: 50%;
            cursor: nwse-resize;
            z-index: 10;
        }

        .resize-se {
            bottom: -6px;
            right: -6px;
        }

        .resize-handle:hover {
            background: #1976d2;
            transform: scale(1.3);
        }
    </style>
    @endpush
</div>
