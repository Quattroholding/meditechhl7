<div x-data="{
    activeOffcanvas: null,
    showMessage: false,
    message: '',
    messageType: 'success'
}"
     x-init="
        // Escuchar eventos de Livewire
        $wire.on('option-selected', (data) => {
           document.body.removeAttribute('style');
        });

     ">
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
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @php $id =\Illuminate\Support\Str::uuid();@endphp
    <div class="selector-field selector-field-on">
        <table style="width:100%">
            <tbody>
            <tr>
                <td>
                    @include('partials.input_saving',['function'=>'selectOption','saved'=>$saved])
                </td>
            </tr>
            <tr>
                <td style="width:80%;padding:0 20px;">
                    <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar..." >
                </td>
                <td style="padding-top: 6px;padding-left: 6px;padding-right: 6px; width:10%">
                    <div class="general-btn-small"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasRight{{$id}}"
                            aria-controls="offcanvasRight">
                        <div class="general-btn-small-text general-btn-small-text-a">Listado de Acceso Rápido</div>
                        <div class="general-btn-small-text general-btn-small-text-b">Ver listado</div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        {{--}}
     <x-offcanvas id="offcanvasRight{{$id}}" title="Listado de acceso rapido" position="right" size="xl" wire:ignore.self>

         @foreach($rapidAccess as $i)
             <div class="sel-list-item sel-code-{{$i->cpt->code}}" wire:click="selectOption({{ json_encode(['id'=>$i->cpt_id,'name'=>'']) }})">
                 <div class="sel-list-item-code">{{$i->cpt->code}}</div>
                 <div class="sel-list-item-content">{{$i->cpt->description_es}}</div>
                 <div class="preloader-space"></div><div class="preloader-space-2">
                 </div>
             </div>
         @endforeach
     </x-offcanvas>
     {{--}}
        <div class="offcanvas offcanvas-end quick-items quick-items-active" tabindex="-1" id="offcanvasRight{{$id}}" aria-labelledby="offcanvasRightLabel">


            <div class="offcanvas-body  quick-items-content">
                <div  class="quick-items-close" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar">
                    <img src="/images/close-floating.png" alt="">
                </div>
                <div class="sel-item-list-category">ACCESOS RAPIDOS</div>
                @if(count($rapidAccess) > 0)
                    @foreach($rapidAccess as $i)
                        <div class="sel-list-item sel-code-{{$i->cpt->code}} mb-2"
                             style="cursor: pointer; padding: 10px; border-radius: 5px; border: 1px solid #dee2e6;">

                            {{-- Contenido principal clickeable --}}
                            <div wire:click="selectOption({{ json_encode(['id'=>$i->cpt_id,'name'=>'']) }})">
                                <div class="sel-list-item-code fw-bold">{{$i->cpt->code}}</div>
                                <div class="sel-list-item-content">{{$i->cpt->description_es}}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <p>No hay accesos rápidos configurados</p>
                    </div>
                @endif
                {{-- Botones de control del panel --}}
                <div class="mt-4 d-flex gap-2 border-top pt-3">
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            wire:click="clearSearch">
                        <i class="fas fa-eraser"></i> Limpiar búsqueda
                    </button>

                    <button type="button"
                            class="btn btn-sm btn-secondary"
                            data-bs-dismiss="offcanvas">
                        <i class="fas fa-times"></i> Cerrar Panel
                    </button>
                </div>
            </div>
        </div> <!-- end offcanvas-body-->

        {{-- RESULTADOS DE BÚSQUEDA --}}
        @if(!empty($results))
            <div class="selector-items" style="z-index: 1000">
                @foreach($results as $result)
                    <div class="sel-list-item d-flex justify-content-between align-items-center"
                         style="cursor: pointer; padding: 8px; border-bottom: 1px solid #eee;">
                        <div class="flex-grow-1" wire:click="selectOption({{ json_encode($result) }})">
                            {{ $result['name'] }}
                        </div>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary"
                                wire:click="addToRapidAccess({{ $result['id'] }})"
                                title="Agregar a accesos rápidos">
                            <i class="fas fa-star"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div style="height:200px;">&nbsp;</div>
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('showToastr', (event) => {
                    toastr[event.type](event.message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000,
                    });
                });
            });
        </script>
</div>
