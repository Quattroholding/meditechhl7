<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('patient.titles') }}
                @endslot
                @slot('li_1')
                    {{ __('generic.list') }} {{ __('patient.titles') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-0 table-dash">
                        <div class="card-body">
                            <div class="table-responsive">
                                <livewire:settings.user-procedure-data-table model="{{$model}}"
                                                     :columns="['id', 'name', 'ruc','dv','email','whatsapp','acciones']"
                                                     :actions="['edit','delete']"
                                                     routename="client"
                                                     wire:key="{{\Illuminate\Support\Str::random(5)}}"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
