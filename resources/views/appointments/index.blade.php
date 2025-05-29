<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('appointment.titles') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.list') }} {{ __('appointment.titles') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-0 table-dash">
                        <div class="card-body">
                            @if(\App\Models\Patient::count()>0)
                                <livewire:appointment.data-table/>
                            @else
                                <div class="text-center" style="margin: 40px;">
                                    <h2>Para poder crear citas debes primero registrar un paciente</h2>
                                    <a  class="btn btn-primary" style="margin-top: 20px;" href="{{route('patient.create')}}">
                                        + {{__('Registrar paciente')}}
                                    </a>
                                </div>

                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>


</x-app-layout>
