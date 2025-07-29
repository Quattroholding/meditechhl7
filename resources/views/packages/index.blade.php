<x-app-layout>

    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Paquetes
                @endslot
                @slot('li_1')
                     Lista de Paquetes
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table show-entire p-2">
                        <div class="card-body">
                            <livewire:data-table model="{{$model}}"
                                                 :columns="['id', 'name', 'description', 'max_users', 'is_active', 'acciones']"
                                                 :actions="['edit','delete']"
                                                 routename="package"
                                                 wire:key="{{\Illuminate\Support\Str::random(5)}}"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>