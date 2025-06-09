<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <div class="error-box">
                <img class="img-fluid" src="{{URL::asset('/assets/img/error-04.png')}}" alt="Logo" >
                <h3 >
                    <img style="display: inline" class="img-fluid mb-0" src="{{URL::asset('/assets/img/icons/danger.svg')}}" alt="Logo">  {{__('Acceso denegado')}}
                </h3>
                <p>{{__('Parece que estas intentado entrar algo que no tienes acceso.')}}</p>
                <a href="{{url()->previous()}}" class="btn btn-primary go-home">{{__('Volver atras')}}</a>
            </div>
        </div>
    </div>
</x-app-layout>
