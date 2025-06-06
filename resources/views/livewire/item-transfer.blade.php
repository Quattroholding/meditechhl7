<div>
    <!-- Dragula JS -->
    <script src="{{ url('assets/plugins/dragula/js/dragula.min.js') }}"></script>
    <script src="{{ url('assets/plugins/dragula/js/drag-drop.min.js') }}"></script>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    <div class="row" x-data>
        <!-- Tabla de disponibles -->
        <div class="col-xl-5" style="border: 3px solid #2E37A4;padding: 20px;">
            <h2 class="text-center">{{__('Secciones Disponibles')}}</h2>
            <ul class="list-group" id="basic-list-group">
                @foreach($availableItems as $item)
                    <li class="list-group-item">
                        <div class="text-end" style="display: inline-block"
                             data-bs-toggle="tooltip"
                             data-bs-html="true"
                             data-bs-original-title="<ul> @foreach($item['livewire_component_fields'] as $field) <li>{{$field['name']}}</li> @endforeach </ul>">{{ $item['name_esp'] }}</div>
                        <button wire:click="moveToSelected({{ $item['id'] }})" type="button"  title="hacer click para mover a la derecha">
                            <i class="fa fa-arrow-right" class="text-end"></i>
                        </button>

                    </li>
                @endforeach
            </ul>
        </div>
        <div class="col-xl-2  text-center d-none d-lg-block"><i class="fa fa-arrow-right fa-4x" ></i><br/><i class="fa fa-arrow-left fa-4x" ></i> </div>
        <div class="col-xl-2  text-center d-block d-md-none"><i class="fa fa-arrow-down fa-4x" style="margin:0.1em;"></i><i class="fa fa-arrow-up fa-4x" style="margin:0.1em;"></i> </div>
        <!-- Tabla de seleccionados -->
        <div class="col-xl-5" style="border: 3px solid #2E37A4;padding: 20px;">
            <h2 class="text-center">{{__('Secciones Seleccionados')}}</h2>
            <ul  class="list-group" id="basic-list-group">
                @foreach($selectedItems as $item)
                    <li class="list-group-item draggable ">
                        <div class="text-left" style="display: inline-block"
                             data-bs-toggle="tooltip"
                             data-bs-html="true"
                             data-bs-original-title="<ul> @foreach($item['livewire_component_fields'] as $field) <li>{{$field['name']}}</li> @endforeach </ul>">{{ $item['name_esp'] }}</div>
                        <button  wire:click="moveToAvailable({{ $item['id'] }})" type="button" title="hacer click para mover a la izquierda">
                            <i class="fa fa-arrow-left"></i>

                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <script>
            document.addEventListener('livewire:load', () => {
                let el = document.getElementById('sortable-list');
                new Sortable(el, {
                    animation: 150,
                    onEnd: function () {
                        let orderedIds = [...el.children].map(li => li.getAttribute('data-id'));
                        Livewire.emit('updateOrder', orderedIds);
                    }
                });
            });
        </script>
    </div>
</div>
