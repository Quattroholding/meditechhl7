<x-app-layout>

    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('Cóodigo de Referidos') }}
                @endslot
                @slot('li_1')

                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <x-referral-code-display :client="auth()->user()->getCurrentClient()" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
