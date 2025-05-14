<?php $page = 'invoices-paid'; ?>
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
                    Invoice Paid
                @endslot
            @endcomponent
            <!-- /Page Header -->
            @component('components.invoices-active-tab')
            @endcomponent
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
                                            <th>Invoice number</th>
                                            <th>Created on</th>
                                            <th>Invoice to</th>
                                            <th>Amount</th>
                                            <th>Paid on</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $json = file_get_contents(public_path('../public/assets/json/invoices-paid.json'));
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
                                                        class="invoice-link">{{ $invoice['Invoicenumber'] }}</a>
                                                </td>
                                                <td>{{ $invoice['Createdon'] }}</td>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a href="{{ url('profile') }}"><img
                                                                class="avatar avatar-sm me-2 avatar-img rounded-circle"
                                                                src="{{ URL::asset('/assets/img/profiles/' . $invoice['Image']) }}"
                                                                alt="User Image"> {{ $invoice['Invoiceto'] }}</a>
                                                    </h2>
                                                </td>
                                                <td class="text-primary">{{ $invoice['Amount'] }}</td>
                                                <td>{{ $invoice['Paidon'] }}</td>
                                                <td><span class="badge bg-success-light">{{ $invoice['Status'] }}</span></td>
                                                <td class="text-end">
                                                    <a href="{{ url('edit-invoices') }}"
                                                        class="btn btn-sm btn-white text-success me-2"><i
                                                            class="far fa-edit me-1"></i> Edit</a>
                                                    <a class="btn btn-sm btn-white text-danger" href="javascript:;"
                                                        data-bs-toggle="modal" data-bs-target="#delete_patient"><i
                                                            class="far fa-trash-alt me-1"></i>Delete</a>
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
