<div style="display: inline-block" wire:poll.10s>
    @if(in_array($appointment->status,['proposed','booked','arrived','fulfilled','pending','checked-in']) && auth()->user()->can('changeStatus',$appointment))
        <div class="btn-group" role="group">
            <button id="btngroupverticaldrop1"
                    type="button" class="badge  dropdown-toggle appointment-status-{{$status}}"
                    data-bs-toggle="dropdown" aria-expanded="false">
                {{ __('appointment.status.'.$status) }}
            </button>
            <div class="dropdown-menu" aria-labelledby="btngroupverticaldrop1" style="">
                @if(auth()->user()->can('booked',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('booked')" >✅ {{__('Confirmar')}}</a>
                @endif
                @if(auth()->user()->can('arrived',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('arrived')" > 🚪 {{__('Registrar Llegada')}}</a>
                @endif
                @if(auth()->user()->can('noshow',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('noshow')" > 👻 {{__('No aparecio')}}</a>
                @endif
                @if(auth()->user()->can('cancelled',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('cancelled')" > ❌ {{__('Cancelar')}}</a>
                @endif
                @if(auth()->user()->can('entered-in-error',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('entered-in-error')" > ❌ {{__('Ingresado por error')}}</a>
                @endif
                @if(auth()->user()->can('checked_in',$appointment))
                    <a class="dropdown-item" wire:click="changeStatus('checked-in')" >  ▶️ {{__('Iniciar Consulta')}}</a>
                @endif
                @if(auth()->user()->can('fulfilled',$appointment))
                    <a class="dropdown-item" href="{{route('consultation.show',$appointment->id)}}"><i class="fa fa-pencil"></i> {{__('Llenar Consulta')}}</a>
                @endif
                @if(auth()->user()->can('viewConsultation',$appointment))
                    <a class="dropdown-item" href="{{route('consultation.show',$appointment->id)}}" > 👁️ {{__('Ver Consulta')}}</a>
                @endif
            </div>
        </div>
    @else
        <button type="button" class="badge appointment-status-{{$status}}" >   {{ __('appointment.status.'.$status) }}  </button>
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
                    @if($appointment->status =='checked-in' && auth()->user()->hasRole('doctor'))
                        window.location.href = '{{route('consultation.show',$appointment->id)}}'; // Replace with your desired URL
                    @endif
                }
            });
        });
    });
    </script>
</div>
