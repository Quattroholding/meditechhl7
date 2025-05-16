<div>
    @if(count($selectedLists)>0)
        <x-input-label  :value="__($type)" />
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

    <table style="width:100%">
        <tbody><tr>
            <td style="width:80%">
                <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar..." >
            </td>
            <td style="padding-top: 6px;padding-left: 6px;padding-right: 6px; width:10%">
                <div class="general-btn-small" type="button" data-bs-toggle="offcanvas" data-bs-target="#modal_acceso_rapido_{{$type}}"
                     aria-controls="offcanvasTop">
                    <div class="general-btn-small-text general-btn-small-text-a">Listado de Acceso Rápido</div>
                    <div class="general-btn-small-text general-btn-small-text-b">Ver listado</div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="offcanvas offcanvas-top quick-items quick-items-active" tabindex="-1" id="modal_acceso_rapido_{{$type}}" aria-labelledby="offcanvasTopLabel" style="height: 100vh;overflow-y: scroll;">
        <div class="offcanvas-body quick-items-content">
            <div  class="quick-items-close" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar">
                <img src="/images/close-floating.png" alt="">
            </div>
            <div class="sel-item-list-category">ACCESOS RAPIDOS</div>
            @foreach($rapidAccess as $i)
                <div class="sel-list-item sel-code-{{$i->cpt->code}}" wire:click="selectOption({{ json_encode(['id'=>$i->cpt_id,'name'=>'']) }})">
                    <div class="sel-list-item-code">{{$i->cpt->code}}</div>
                    <div class="sel-list-item-content">{{$i->cpt->description_es}}</div>
                    <div class="preloader-space"></div><div class="preloader-space-2">
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Spinner de Carga -->
    <div wire:loading class="absolute right-2 top-2">
        <div class="animate-spin rounded-full h-5 w-5 border-t-2 border-blue-500"></div>
    </div>


    @if(!empty($results))
        <ul class="absolute bg-white border w-full mt-1 rounded shadow-lg max-h-40 overflow-y-auto" style="z-index: 1000">
            @foreach($results as $result)
                <li  class="p-2 hover:bg-gray-200 cursor-pointer text-sm"  wire:click="selectOption({{ json_encode($result) }})">
                    {{ $result['name'] }}
                </li>
            @endforeach
        </ul>
    @endif
</div>
