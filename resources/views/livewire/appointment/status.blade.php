<div>
    @if(in_array($appointment->status,['booked','arrived','fulfilled','proposed','pending','checked-in']))
        <div class="btn-group" role="group">
            <button id="btngroupverticaldrop1"
                    type="button" class="badge  dropdown-toggle"
                    style="background-color: #{{$color}}"
                    data-bs-toggle="dropdown" aria-expanded="false">
                {{ __('appointment.status.'.$status) }}
            </button>
            @if(auth()->user()->can('changeStatus',$appointment))
            <div class="dropdown-menu" aria-labelledby="btngroupverticaldrop1" style="">
                @if(auth()->user()->can('booked',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('booked')" ><i class="fa fa-door-open"></i> {{__('Confirmar')}}</a>
                @endif
                @if(auth()->user()->can('arrived',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('arrived')" ><i class="fa fa-door-open"></i> {{__('Llegada')}}</a>
                @endif
                @if(auth()->user()->can('noshow',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('noshow')" ><i class="fa fa-door-closed"></i> {{__('No Asistio')}}</a>
                @endif
                @if(auth()->user()->can('cancelled',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('cancelled')" ><i class="fa fa-close"></i> {{__('Cancelar')}}</a>
                @endif
                @if(auth()->user()->can('entered-in-error',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('entered-in-error')" ><i class="fa fa-warning"></i> {{__('Ingresado por error')}}</a>
                @endif
                @if(auth()->user()->can('checked-in',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('checked-in')" ><i class="fa fa-clock-o"></i> {{__('Iniciar Consulta')}}</a>
                @endif
                @if(auth()->user()->can('fulfilled',$appointment))
                    <a class="dropdown-item" href="{{route('consultation.show',$appointment->id)}}"><i class="fa fa-clock-o"></i> {{__('Finalizar Consulta')}}</a>
                @endif
                @if(auth()->user()->can('viewConsultation',$appointment))
                    <a class="dropdown-item" href="{{route('consultation.show',$appointment->id)}}" ><i class="fa fa-eye"></i> {{__('Ver Consulta')}}</a>
                @endif
            </div>
            @endif
        </div>
    @else
        <button type="button" class="badge" style="background-color: #{{$color}};color:#fff;">   {{ __('appointment.status.'.$status) }}  </button>
    @endif
    <script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('showToastr{{$appointment->id}}', (event) => {
            toastr[event.type](event.message, '', {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 5000,
                onHidden: function() {
                    window.location.href = '{{route('consultation.show',$appointment->id)}}'; // Replace with your desired URL
                }
            });
        });
    });
    </script>
</div>
