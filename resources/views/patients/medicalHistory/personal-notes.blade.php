<div class="medical-notes-content">
    @section('css')
        <link rel="stylesheet" href="{{url('assets/plugins/stickynote/sticky.css')}}">
    @stop
    @section('scripts')
        <script rel="stylesheet" href="{{url('assets/plugins/stickynote/sticky.js')}}"></script>
    @stop

    @if($sectionData && count($sectionData) > 0)
    <div class="sticky-note" id="board" style="height: 959px;">
     @foreach($sectionData as $persoanNote)
        <div class="note ui-draggable ui-draggable-handle" style="">
            <div class="note_cnt">
                <span>{{__('Fecha')}} : {{$persoanNote->created_at}}</span>
                <textarea class="cnt" placeholder="Enter note description here" style="height: 200px;">{{$persoanNote->note}}</textarea>
            </div>
        </div>
     @endforeach
    </div>
    @else
    <div style="text-align: center; padding: 60px; color: #64748b;">
        <div style="font-size: 48px; margin-bottom: 20px;">📒</div>
        <h3>No hay Notas Personales Registradas</h3>
        <p>Este paciente no tiene notas personales registradas en el período seleccionado.</p>
    </div>
    @endif
    <script src="assets/plugins/stickynote/sticky.js"></script>
</div>
