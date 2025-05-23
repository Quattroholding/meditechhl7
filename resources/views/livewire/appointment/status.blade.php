<div>
    @if(in_array($appointment->status,['booked','arrived','fulfilled','proposed','pending','checked-in']))
        <div class="btn-group" role="group">
            <button id="btngroupverticaldrop1"
                    type="button" class="badge  dropdown-toggle"
                    style="background-color: #{{$color}}"
                    data-bs-toggle="dropdown" aria-expanded="false">
                {{ __('appointment.status.'.$status) }}
            </button>
            <div class="dropdown-menu" aria-labelledby="btngroupverticaldrop1" style="">
                @if($appointment->status=='booked')
                    <a class="dropdown-item" wire:click="changeStatus('arrived')" ><i class="fa fa-door-open"></i> {{__('Llegada')}}</a>
                    <a class="dropdown-item" wire:click="changeStatus('noshow')" ><i class="fa fa-door-closed"></i> {{__('No Asistio')}}</a>
                    <a class="dropdown-item" wire:click="changeStatus('cancelled')" ><i class="fa fa-close"></i> {{__('Cancelar')}}</a>
                    <a class="dropdown-item" wire:click="changeStatus('entered-in-error')" ><i class="fa fa-warning"></i> {{__('Ingresado por error')}}</a>
                @elseif($appointment->status=='arrived')
                    <a class="dropdown-item" wire:click="changeStatus('checked-in')" ><i class="fa fa-clock-o"></i> {{__('Iniciar Consulta')}}</a>
                    <a class="dropdown-item" wire:click="changeStatus('cancelled')"><i class="fa fa-close"></i> {{__('Cancelar')}}</a>
                    <a class="dropdown-item" wire:click="changeStatus('entered-in-error')" ><i class="fa fa-warning"></i> {{__('Ingresado por error')}}</a>
                @elseif($appointment->status=='checked-in')
                    <a class="dropdown-item" href="{{route('consultation.show',$appointment->id)}}"><i class="fa fa-clock-o"></i> {{__('Finalizar Consulta')}}</a>
                    <a class="dropdown-item" wire:click="changeStatus('cancelled')"><i class="fa fa-close"></i> {{__('Cancelar')}}</a>
                    <a class="dropdown-item" wire:click="changeStatus('entered-in-error')" ><i class="fa fa-warning"></i> {{__('Ingresado por error')}}</a>
                @elseif($appointment->status=='fulfilled')
                    <a class="dropdown-item" href="{{route('consultation.show',$appointment->id)}}" ><i class="fa fa-eye"></i> {{__('Ver Consulta')}}</a>
                @elseif(in_array($appointment->status,['proposed','pending']))
                    <a class="dropdown-item" wire:click="changeStatus('booked')" ><i class="fa fa-door-open"></i> {{__('Confirmar')}}</a>
                    <a class="dropdown-item" wire:click="changeStatus('cancelled')" ><i class="fa fa-close"></i> {{__('Cancelar')}}</a>
                    <a class="dropdown-item" wire:click="changeStatus('entered-in-error')" ><i class="fa fa-warning"></i> {{__('Ingresado por error')}}</a>
                @endif
            </div>
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
