<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('doctor.titles') }}
                @endslot
                @slot('li_1')
                        {{ __('doctor.profile') }}   {{ __('doctor.title') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">

                    <livewire:doctor.head practitioner_id="{{$data->id}}"/>
                    <div class="row">
                        <livewire:doctor.profile-about practitioner_id="{{$data->id}}"/>
                        <livewire:doctor.profile-details practitioner_id="{{$data->id}}"/>
                        <div class="col-12 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    @livewire('user.two-factor-authentication')
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->hasAnyRole(['admin','doctor']))
                    <div class="row mt-4">

                    </div>
                    @endif
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>

