<div>
    @if(count($selectedLists)>0)
        <div id="" class="multiple-field-values mb-3">
            <div class="multivalue-item-container">
                @foreach($selectedLists as $s)
                    <div class="multivalue-item" code="{{$s->id}}">
                        <table wire:click="delete({{$s->id}})">
                            <tbody>
                            <tr>
                                <td>
                                <span>
                                <div class="delete-multivalue">
                                    <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z"></path></svg>
                                    </span>
                                    <span>Borrar</span>
                                </div>
                                </span>
                                </td>
                                <td>
                                    {{$s->cpt->full_name}}
                                </td>
                            </tr>

                            </tbody>
                        </table>
                        <div class="my-3">
                            {{__('consultation.instruction')}}
                            <x-textarea-input  wire:keyup.debounce.300ms="updateNote({{$s->id}})" rows="1"
                                               wire:model="notes.{{$s->id}}"
                                               class="block mt-1 w-full" type="text" name="notes"
                                               placeholder="Escribir instrucciones (opcional)">{{$s->note}}
                            </x-textarea-input>
                        </div>

                    </div>
                    @include('partials.input_saving',['function'=>'updateNote','saved'=>$savedNote[$s->id],'function_param'=>$s->id])
                @endforeach
            </div>
        </div>
    @endif
    <div class="selector-field selector-field-on">
        <table style="width:100%">
            <tbody>
            <tr>
                <td>
                    @include('partials.input_saving',['function'=>'selectOption','saved'=>$saved])
                </td>
            </tr>
            <tr>
                <td style="width:80%;padding:20px;">
                    <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar por descripcion o codigo cpt" >
                </td>
                <td style="padding-top: 6px;padding-left: 6px;padding-right: 6px; width:10%">
                    <div class="general-btn-small"
                            type="button"
                            style="cursor: pointer;"
                            data-offcanvas-target="offcanvasRight-{{$encounter_id}}-{{$section_id}}"
                            onclick="openRapidAccessOffcanvas(this.getAttribute('data-offcanvas-target'))">
                        <div class="general-btn-small-text general-btn-small-text-a">Listado de Acceso Rápido</div>
                        <div class="general-btn-small-text general-btn-small-text-b">Ver listado</div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        {{-- Componente independiente de accesos rápidos --}}
        @livewire('consultation.rapid-access-offcanvas', [
            'sectionId' => $section_id,
            'offcanvasId' => 'offcanvasRight-'.$encounter_id.'-'.$section_id,
            'encounterId' => $encounter_id
        ], key('rapid-access-'.$encounter_id.'-'.$section_id))

        {{-- RESULTADOS DE BÚSQUEDA --}}
        @if(!empty($results))
            <div class="selector-items" style="z-index: 1000">
                @foreach($results as $result)
                    <div class="sel-list-item row" wire:click="selectOption({{ json_encode($result) }})">
                        <div class="col-md-1 "><strong>{{ $result['code'] }}</strong></div>
                        <div class="col-md-8">  {{ $result['description_es'] }}</div>
                        <div class="col-md-3 text-end">
                            <button type="button"  class="btn btn-sm btn-outline-primary"  wire:click="addToRapidAccess({{ $result['id'] }})"  title="Agregar a accesos rápidos">
                                <i class="fas fa-star"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div style="height:200px;">&nbsp;</div>

    <script>
        // Función global para abrir offcanvas de acceso rápido
        window.openRapidAccessOffcanvas = function(offcanvasId) {
            // Intentar múltiples veces con retrasos incrementales
            let attempts = 0;
            const maxAttempts = 10;
            const baseDelay = 100;

            function tryOpen() {
                attempts++;
                const el = document.getElementById(offcanvasId);

                if (el) {
                    console.log('Elemento encontrado en intento', attempts);
                    let instance = bootstrap.Offcanvas.getInstance(el);

                    if (!instance) {
                        console.log('Creando instancia de offcanvas...');
                        try {
                            instance = bootstrap.Offcanvas.getOrCreateInstance(el, {
                                backdrop: true,
                                keyboard: true
                            });
                        } catch (error) {
                            console.error('Error al crear instancia:', error);
                            return;
                        }
                    }

                    if (instance) {
                        instance.show();
                        console.log('Offcanvas mostrado exitosamente');
                    }
                } else if (attempts < maxAttempts) {
                    console.log('Elemento no encontrado, reintentando... (intento', attempts, ')');
                    setTimeout(tryOpen, baseDelay * attempts);
                } else {
                    console.error('No se pudo encontrar el elemento después de', maxAttempts, 'intentos');
                }
            }

            tryOpen();
        };
    </script>
</div>
