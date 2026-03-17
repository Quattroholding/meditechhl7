<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Detalle del Resultado
                @endslot
                @slot('li_1')
                    <a href="{{ route('hemoscreen.dashboard') }}">Dashboard</a>
                @endslot
                @slot('li_2')
                    Resultado #{{ $result->id }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            @livewire(\App\Livewire\HemoScreen\StandaloneResultDetail::class, ['resultId' => $result->id])

        </div>
    </div>
</x-app-layout>
