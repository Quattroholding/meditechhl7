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
                    </div>
                    <div class="row mt-4">
                        <div class="col-12 col-lg-7">
                            <livewire:doctor.signature-manager practitioner_id="{{$data->id}}"/>
                        </div>
                        <div class="col-12 col-lg-5">
                            <x-referral-code-display :client="$data->user->getCurrentClient()" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>

