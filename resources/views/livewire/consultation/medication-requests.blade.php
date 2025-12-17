<div x-data="{}"
     x-init="
        $wire.on('option-selected', (data) => {
           document.body.removeAttribute('style');
           document.body.classList.remove('modal-open');
           const backdrop = document.querySelector('.offcanvas-backdrop');
           if (backdrop) {
               backdrop.remove();
           }
        });

        $wire.on('showSaving', (event) => {
            const medicationId = event.medicationId;
            const savingEl = document.getElementById('saving-' + medicationId);
            const savedEl = document.getElementById('saved-' + medicationId);

            if (savingEl) savingEl.style.display = 'flex';
            if (savedEl) savedEl.style.display = 'none';
        });

        $wire.on('showSaved', (event) => {
            const medicationId = event.medicationId;
            const savingEl = document.getElementById('saving-' + medicationId);
            const savedEl = document.getElementById('saved-' + medicationId);

            if (savingEl) savingEl.style.display = 'none';
            if (savedEl) {
                savedEl.style.display = 'flex';

                // Ocultar después de 3 segundos
                setTimeout(() => {
                    savedEl.style.display = 'none';
                }, 3000);
            }
        });

        $wire.on('hideSaving', (event) => {
            const medicationId = event.medicationId;
            const savingEl = document.getElementById('saving-' + medicationId);
            if (savingEl) savingEl.style.display = 'none';
        });
     ">
    @if(count($selectedLists)>0)
        <table style="width:100%;" border="1"
               class="medicine-table">
            <tbody>
            <tr>

            </tr>
            @foreach($selectedLists as $m)
            <tr class="consultation-tr-inputs" style="background: {{ $loop->iteration % 2 == 0 ? '#fff' : '#ededed' }}">
                <td>
                    <b rel="producto-full-name">{{$m->medicine ? $m->medicine->full_name : $m->medication}}</b>
                </td>
                <td>
                    <table style="width:100%;">
                        <tbody>
                        <tr>
                            <td >
                                <div class="input-block local-forms">
                                    <x-input-label for="quantity" :value="__('Cantidad')" />
                                    <x-autosave-input
                                        type="number"
                                        :value="$quantitys[$m->id]"
                                        wire:model.live="quantitys.{{ $m->id }}"
                                        save-method="saveValue"
                                        save-key="quantity-{{ $m->id }}"
                                        class="form-control block w-full"
                                        onkeydown="return (event.keyCode !== 69 && event.keyCode !== 189 && event.keyCode !== 187)"
                                        min="0" step="1"
                                        placeholder="Ejemplo : 2"
                                    />

                                </div>
                                <div class="input-block local-forms">
                                    <x-input-label for="frecuency" :value="__('Frecuencia (Horas)')" />
                                    <x-autosave-input
                                        type="number"
                                        :value="$frecuencies[$m->id]"
                                        onkeydown="return (event.keyCode !== 69 && event.keyCode !== 189 && event.keyCode !== 187)"
                                        min="0" step="1"
                                        placeholder="Ejemplo : 2"
                                        wire:model.live="frecuencies.{{ $m->id }}"
                                        save-method="updateField"
                                        save-key="frecuency-{{ $m->id }}"
                                        class="form-control block w-full"
                                    />
                                </div>

                            </td>
                            <td>
                                <div class="input-block local-forms">
                                    <x-input-label for="route" :value="__('Via')" />
                                    <x-autosave-input
                                        type="select"
                                        :value="$routes[$m->id]"
                                        :options="\App\Models\Lista::medicationVias()"
                                        :selected="$routes[$m->id]"
                                        wire:model.live="routes.{{ $m->id }}"
                                        save-method="updateField"
                                        save-key="route-{{ $m->id }}"
                                        class="form-control block w-full"
                                    />
                                </div>

                                <div class="input-block local-forms">
                                    <x-input-label for="duration" :value="__('Duración (Días)')" />
                                    <x-autosave-input
                                        type="number"
                                        :value="$durations[$m->id]"
                                        onkeydown="return (event.keyCode !== 69 && event.keyCode !== 189 && event.keyCode !== 187)"
                                        min="0" step="1"
                                        placeholder="Ejemplo : 5"
                                        wire:model.live="durations.{{ $m->id }}"
                                        save-method="updateField"
                                        save-key="duration-{{ $m->id }}"
                                        class="form-control block w-full"
                                    />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="input-block local-forms">
                                    <x-input-label for="dosage_text" :value="__('Indicciones')"/>
                                    <x-autosave-input
                                        type="textarea"
                                        :value="$dosage_texts[$m->id]"
                                        disabled="disabled"
                                        class="form-control mt-1 block w-full"
                                        rows="2"
                                        wire:model.live="dosage_texts.{{$m->id}}"
                                        laceholder="Ejemplo: Una tableta cada 8 horas vía oral por 5 días"
                                        save-method="updateField"
                                        save-key="dosage_text_{{ $m->id }}"
                                    />
                                </div>
                            </td>
                            {{--}}
                            <td>
                                <div class="input-block local-forms">
                                    <x-input-label for="refills" :value="__('Meses de Refill')" />
                                    <select class="form-control" wire:change="updateField({{$m->id}},$event.target.value,'refills')">
                                        <option value="0">Sin Refill</option>
                                        @for($i=2;$i<6;$i++)
                                            <option value="{{$i}}" @if($m->refills==$i) selected @endif>{{$i}} meses</option>
                                        @endfor
                                    </select>
                                </div>
                            </td>
                            {{--}}
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <div class="sprite-trash-container" ani="1" style="cursor:pointer" wire:click="delete({{$m->id}})">
                        <div class="sprite-trash"></div>
                        <div>Borrar</div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    @endif
    <div class="my-3"></div>
    <div class="general-btn-small" wire:click="medical_request_history">
        <div class="general-btn-small-text general-btn-small-text-a">Historial de Medicamentos</div>
        <div class="general-btn-small-text general-btn-small-text-b">Ver listado</div>
    </div>
    <p>&nbsp;</p>
    @php $id =\Illuminate\Support\Str::uuid();@endphp
    <div class="selector-field selector-field-on">
        <x-autosave-action save-key="medication-search" />

        <table style="width:100%">
            <tbody>
            <tr>
                <td style="width:80%;padding:20px;">
                    <input type="text"  wire:model.live="query"   class="form-control" placeholder="Buscar medicamento por nombre, código NDC o nombre genérico" >
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
        <div class="offcanvas offcanvas-end quick-items quick-items-active" tabindex="-1" id="offcanvasRight{{$id}}" aria-labelledby="offcanvasRightLabel" >
            <div class="offcanvas-body  quick-items-content">
                <div  class="quick-items-close" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar">
                    <img src="/images/close-floating.png" alt="">
                </div>
                <div class="sel-item-list-category">ACCESOS RAPIDOS</div>
                @if(count($rapidAccess) > 0)
                    @foreach($rapidAccess as $i)
                        <div class="sel-list-item mb-2"
                             style="cursor: pointer; padding: 10px; border-radius: 5px; border: 1px solid #dee2e6;">

                            {{-- Contenido principal clickeable --}}
                            <div wire:click="selectOption({{ json_encode(['id'=>$i->medicine_id,'name'=>'']) }})">
                                <div class="sel-list-item-code fw-bold">{{$i->medicine->home_name}}</div>
                                <div class="sel-list-item-content">{{$i->medicine->full_name}}</div>
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
                        <div
                            class="sel-list-item row"
                            wire:click.debounce.300ms="selectOption({{ json_encode($result) }})"
                            x-on:click=" window.dispatchEvent( new CustomEvent('autosave-start', { detail: 'medication-search' }))"
                        >
                        <div class="col-md-9">{{ $result['name'] }}</div>
                        <div class="col-md-3 text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click.stop="addToRapidAccess({{ $result['id'] }})" title="Agregar a accesos rápidos">
                                <i class="fas fa-star"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

   <div style="height:200px;">&nbsp;</div>

   <!-- Componente del historial de medicamentos -->
   <livewire:patient.medication-history />
</div>
