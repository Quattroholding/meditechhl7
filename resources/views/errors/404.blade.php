<x-guest-layout>
@section('content')
    <div class="error-box">
        <img class="img-fluid" src="{{URL::asset('/assets/img/error-01.png')}}" alt="Logo" >
        <h3><img class="img-fluid mb-0" src="{{URL::asset('/assets/img/icons/danger.svg')}}" alt="Logo">  {{__('Servicio no disponible')}}</h3>
        <p>{{__('Es posible que haya escrito mal la dirección o que la página se haya movido.')}}</p>
        <a href="{{url()->previous()}}" class="btn btn-primary go-home">{{__('Volver atras')}}</a>
    </div>
</x-guest-layout>
