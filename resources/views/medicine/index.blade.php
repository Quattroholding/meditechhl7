<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Medicamentos</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            <livewire:medicine.data-table />
        </div>
    </div>
</x-app-layout>>
