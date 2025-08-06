<div>
    <form method="POST" action="{{ route('consultation.finished',$this->encounter->appointment_id) }}">
        @csrf
        <div class="text-end">
            <button type="submit" class="btn btn-success" id="finishedButton" @if(!$enabled) disabled @endif>{{__('Finalizar Consulta')}}</button>
        </div>
    </form>
    @if(count($messages)>0)
    <div id="menu-right-info" class="menu-right-item">
        <div class="campos-obligatorios-label" style="">
            <b>Para finalizar consulta agregue la siguiente información:</b>
        </div>
        <div class="btn-enviar-by-diagnostic-tester">
            <div class="campos-obligatorios-scroll">
                @foreach($messages as $msg)
                    <div id="mandatory-{{$loop->index}}" class="mandatory-field mandatory-empty" onclick="scroll_mandatory(this)">{{$msg}}</div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
