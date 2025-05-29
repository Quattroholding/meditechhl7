<x-app-layout>
    @section('scripts')
        <script src="{{ URL::asset('/assets/js/jquery-3.7.1.min.js') }}"></script>
    @stop
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('patient.title') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.create') }} {{ __('patient.title') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>  {{ __('generic.create') }} {{ __('patient.title') }}</h4>
                                </div>
                            </div>
                            <livewire:patient.create/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
