<?php $page = 'invoices-grid'; ?>
@extends('layout.mainlayout')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Invoice
                @endslot
                @slot('li_1')
                    Invoice Grid
                @endslot
            @endcomponent
            <!-- /Page Header -->
            @component('components.invoices-active-tab')
            @endcomponent
            <!-- Report Filter -->
            @component('components.invoices-report-filter')
            @endcomponent
            <div class="card invoices-tabs-card">
                <div class="card-body card-body pt-0 pb-0">
                    <div class="invoices-main-tabs">
                        <div class="row align-items-center">
                            <div class="col-lg-12 col-md-12">
                                <div class="invoices-settings-btn">
                                    <a href="{{ url('invoices-settings') }}" class="invoices-settings-icon">
                                        <i data-feather="settings"></i>
                                    </a>
                                    <a href="{{ url('add-invoice') }}" class="btn">
                                        <i data-feather="plus-circle"></i> New Invoice
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                @php
                    $json = file_get_contents(public_path('../public/assets/json/invoices-grid.json'));
                    $invoices = json_decode($json, true);
                @endphp
                @foreach ($invoices as $invoice)
                    <div class="col-sm-6 col-lg-4 col-xl-3 d-flex">
                        <div class="card invoices-grid-card w-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <a href="{{ url('view-invoice') }}"
                                    class="invoice-grid-link">{{ $invoice['InvoiceNo'] }}</a>
                                <div class="dropdown dropdown-action">
                                    <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false"><i class="fas fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ url('edit-invoices') }}"><i
                                                class="far fa-edit me-2"></i>Edit</a>
                                        <a class="dropdown-item" href="{{ url('view-invoice') }}"><i
                                                class="far fa-eye me-2"></i>View</a>
                                        <a class="dropdown-item" href="javascript:void(0);"><i
                                                class="far fa-trash-alt me-2"></i>Delete</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-middle">
                                <h2 class="card-middle-avatar">
                                    <a href="{{ url('profile') }}"><img
                                            class="avatar avatar-sm me-2 avatar-img rounded-circle"
                                            src="{{ URL::asset('/assets/img/profiles/' . $invoice['Image']) }}"
                                            alt="User Image">
                                        {{ $invoice['Name'] }}</a>
                                </h2>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <span><i class="far fa-money-bill-alt"></i> Amount</span>
                                        <h6 class="mb-0">{{ $invoice['Amount'] }}</h6>
                                    </div>
                                    <div class="col-auto">
                                        <span><i class="far fa-calendar-alt"></i> Due Date</span>
                                        <h6 class="mb-0">{{ $invoice['DueDate'] }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row align-items-center">
                                    @if ($invoice['Status'] === 'Overdue')
                                        <div class="col-auto">
                                            <span class="{{ $invoice['Class'] }}">{{ $invoice['Status'] }}</span>
                                        </div>
                                        <div class="col text-end">
                                            <span class="text-danger text-sm">{{ $invoice['OverdueDays'] }}</span>
                                        </div>
                                    @else
                                        <div class="col-auto">
                                            <span class="{{ $invoice['Class'] }}">{{ $invoice['Status'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="col-lg-12">
                    <div class="invoice-load-btn">
                        <a href="javascript:;" class="btn">
                            <span class="spinner-border text-primary"></span> Load more
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
