<div class="row ">
    <div class="col-lg-12">
        <div class="py-3 d-flex justify-content-end">
            <a href="{{ url('invoices-list') }}" class="invoices-links {{ Request::is('invoices-list','invoices-cancelled','invoices-draft','invoices-overdue','invoices-paid','invoices-recurring') ? 'active' : '' }}">
                <i data-feather="list"></i>
            </a>
            <a href="{{ url('invoices-grid') }}" class="invoices-links {{ Request::is('invoices-grid') ? 'active' : '' }}">
                <i data-feather="grid"></i>
            </a>
        </div>
    </div>
</div>