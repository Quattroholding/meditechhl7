<div wire:ignore>
    <div class="offcanvas offcanvas-end quick-items quick-items-active"
         tabindex="-1" id="{{$offcanvasId}}"
         aria-labelledby="offcanvasRightLabel"
         data-bs-backdrop="true"
         data-bs-keyboard="true">
        <div class="offcanvas-body  quick-items-content">
            <div  class="quick-items-close" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar">
                <img src="/images/close-floating.png" alt="">
            </div>
            <div class="sel-item-list-category">ACCESOS RAPIDOS</div>

            <div id="rapid-access-items-{{$encounterId}}-{{$section_id}}">
                @if(count($rapidAccess) > 0)
                    @foreach($rapidAccess as $item)
                        <div class="sel-list-item mb-2 @if($item->is_selected) bg-primary text-white @endif"
                             style="cursor: pointer; padding: 10px; border-radius: 5px; border: 1px solid #dee2e6;"
                             onclick="window.selectRapidAccessItem{{$encounterId}}{{$section_id}}({{$item->cpt_id}})"
                             data-cpt-id="{{$item->cpt_id}}"
                             data-cpt-code="{{$item->cpt->code}}">
                            <div class="sel-list-item-code fw-bold">{{$item->cpt->code}}</div>
                            <div class="sel-list-item-content">{{$item->cpt->description_es}}</div>
                            @if($item->is_selected)
                                <div class="mt-1 selected-indicator">
                                    <small><i class="fas fa-check-circle" title=" Ya seleccionado"></i></small>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <p>No hay accesos rápidos configurados</p>
                    </div>
                @endif
            </div>

            {{-- Botones de control del panel --}}
            <div class="mt-4 d-flex gap-2 border-top pt-3">
                <button type="button"
                        class="btn btn-sm btn-secondary"
                        onclick="
                            const el = document.getElementById('{{$offcanvasId}}');
                            const instance = bootstrap.Offcanvas.getInstance(el);
                            if (instance) {
                                instance.hide();
                            }
                            document.body.classList.remove('modal-open', 'offcanvas-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                            setTimeout(() => {
                                document.querySelectorAll('.offcanvas-backdrop').forEach(b => b.remove());
                            }, 300);
                        ">
                    <i class="fas fa-times"></i> Cerrar Panel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Función global para seleccionar item
    window.selectRapidAccessItem{{$encounterId}}{{$section_id}} = function(cptId) {
        // Llamar a Livewire
        @this.selectItem(cptId);

        // Actualizar visualmente el item seleccionado de forma inmediata
        setTimeout(() => {
            const itemsContainer = document.getElementById('rapid-access-items-{{$encounterId}}-{{$section_id}}');
            if (itemsContainer) {
                const items = itemsContainer.querySelectorAll('[data-cpt-id="' + cptId + '"]');
                items.forEach(item => {
                    item.classList.add('bg-primary', 'text-white');
                    // Agregar el indicador si no existe
                    if (!item.querySelector('.selected-indicator')) {
                        const indicator = document.createElement('div');
                        indicator.className = 'mt-1 selected-indicator';
                        indicator.innerHTML = '<small><i class="fas fa-check-circle" title=" Ya seleccionado"></i></small>';
                        item.appendChild(indicator);
                    }
                });
            }
        }, 100);
    };

    document.addEventListener('livewire:init', () => {
        Livewire.on('rapid-access-item-selected-{{$encounterId}}-{{$section_id}}', (event) => {
            // Mantener el offcanvas abierto después de seleccionar
            setTimeout(() => {
                const el = document.getElementById('{{$offcanvasId}}');
                if (el) {
                    // Limpiar estilos del body
                    document.body.classList.remove('modal-open', 'offcanvas-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    // Eliminar todos los backdrops
                    document.querySelectorAll('.offcanvas-backdrop').forEach(b => b.remove());

                    // Reabrir el offcanvas
                    const instance = bootstrap.Offcanvas.getInstance(el);
                    if (instance) {
                        instance.show();
                    } else {
                        const newInstance = new bootstrap.Offcanvas(el);
                        newInstance.show();
                    }
                }
            }, 150);
        });

        // Escuchar cuando se elimina un item para actualizar visualmente
        Livewire.on('rapid-access-list-updated-{{$encounterId}}-{{$section_id}}', (event) => {
            console.log('Lista de accesos rápidos actualizada, recargando visualmente...');
            // Llamar al método de Livewire para obtener los datos actualizados
            @this.call('loadRapidAccess').then(() => {
                // Recargar el contenido del contenedor
                location.reload(); // Temporal, idealmente usaríamos Alpine o fetch
            });
        });
    });
</script>
