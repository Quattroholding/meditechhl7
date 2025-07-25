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
            <div class="btn-enviar" style="display: none">
                <form method="POST" action="http://hcasystemv10.test/consultation/50153/change_status" accept-charset="UTF-8" role="form" class="form-horizontal"><input name="_method" type="hidden" value="PUT"><input name="_token" type="hidden" value="M28mpwkquY3ieIk3fhzBzLNNXiYUVylutb5REqyq">
                    <button type=" submit " class="btn btn-custom pull-right button-prevent-multiple-submit  btn-primary  btn-block snd_btn">Finalizar consulta</button>                </form>
            </div>
        </div>
    </div>
    @endif
</div>
