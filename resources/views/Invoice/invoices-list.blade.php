<?php $page = 'invoices-list'; ?>
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
                    Invoice List
                @endslot
            @endcomponent
            <!-- /Page Header -->
            @component('components.invoices-active-tab')
            @endcomponent
            <!-- Report Filter -->
            @component('components.invoices-report-filter')
            @endcomponent
            @component('components.invoices-tab')
            @endcomponent
            @component('components.invoices-card')
            @endcomponent
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table custom-table comman-table mb-0 datatable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Invoice ID</th>
                                            <th>Category</th>
                                            <th>Created on</th>
                                            <th>Invoice to</th>
                                            <th>Amount</th>
                                            <th>Due date</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $json = file_get_contents(public_path('../public/assets/json/invoices-list.json'));
                                            $invoices = json_decode($json, true);
                                        @endphp
                                        @foreach ($invoices as $invoice)
                                            <tr>
                                                <td>
                                                    <label class="custom_check">
                                                        <input type="checkbox" name="invoice">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                    <a href="{{ url('view-invoice') }}"
                                                        class="invoice-link">{{ $invoice['InvoiceID'] }}</a>
                                                </td>
                                                <td>{{ $invoice['Category'] }}</td>
                                                <td>{{ $invoice['Createdon'] }}</td>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a href="{{ url('profile') }}"><img
                                                                class="avatar avatar-sm me-2 avatar-img rounded-circle"
                                                                src="{{ URL::asset('/assets/img/profiles/' . $invoice['Image']) }}"
                                                                alt="User Image">
                                                            {{ $invoice['Invoiceto'] }}</a>
                                                    </h2>
                                                </td>
                                                <td class="text-primary">{{ $invoice['Amount'] }}</td>
                                                <td>{{ $invoice['Duedate'] }}</td>
                                                <td><span class="{{ $invoice['Class'] }}">{{ $invoice['Status'] }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="javascript:;" class="action-icon dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="fas fa-ellipsis-v"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="{{ url('edit-invoices') }}"><i
                                                                    class="far fa-edit me-2"></i>Edit</a>
                                                            <a class="dropdown-item" href="{{ url('view-invoice') }}"><i
                                                                    class="far fa-eye me-2"></i>View</a>
                                                            <a class="dropdown-item" href="javascript:void(0);"><i
                                                                    class="far fa-trash-alt me-2"></i>Delete</a>
                                                            <a class="dropdown-item" href="javascript:void(0);"><i
                                                                    class="far fa-check-circle me-2"></i>Mark as sent</a>
                                                            <a class="dropdown-item" href="javascript:void(0);"><i
                                                                    class="far fa-paper-plane me-2"></i>Send Invoice</a>
                                                            <a class="dropdown-item" href="javascript:void(0);"><i
                                                                    class="far fa-copy me-2"></i>Clone Invoice</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection
