<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('user.title') }}
                @endslot
                @slot('li_1')
                    {{ __('Perfil') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->
             <livewire:user.profile/>
        </div>
    </div>
</x-app-layout>
