<div>
    <!-- Button to open configuration modal -->
    <button wire:click="openModal" class="btn btn-outline-primary btn-sm" title="Configurar Widgets">
        <i class="feather-settings"></i> Configurar Dashboard
    </button>

    <!-- Configuration Modal -->
    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h5 class="modal-title">Configurar Widgets del Dashboard</h5>
                <button type="button" class="btn-close" wire:click="closeModal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p class="text-muted">Selecciona los widgets que deseas ver en tu dashboard y arrastra para reordenar.</p>
                </div>
                <div id="widget-list" class="list-group">
                    @foreach($widgets as $index => $widget)
                    <div class="list-group-item"
                         data-widget-key="{{ $widget['key'] }}"
                         data-widget-description="{{ $widget['description'] }}"
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
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">Guardar</button>
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        console.log('🟢 Widget Configuration script loading...');
        console.log('🟢 Sortable available:', typeof Sortable !== 'undefined');

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
                window.location.reload();
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
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
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
