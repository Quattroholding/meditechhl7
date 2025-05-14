<div class="card invoices-tabs-card">
    <div class="card-body card-body pt-0 pb-0">
        <div class="invoices-main-tabs">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-8">
                    <div class="invoices-tabs">
                        <ul>
                            <li><a href="{{ url('invoices-list') }}"
                                    class="{{ Request::is('invoices-list') ? 'active' : '' }}">All
                                    Invoice</a></li>
                            <li><a href="{{ url('invoices-paid') }}"
                                    class="{{ Request::is('invoices-paid') ? 'active' : '' }}">Paid</a></li>
                            <li><a href="{{ url('invoices-overdue') }}"
                                    class="{{ Request::is('invoices-overdue') ? 'active' : '' }}">Overdue</a></li>
                            <li><a href="{{ url('invoices-draft') }}"
                                    class="{{ Request::is('invoices-draft') ? 'active' : '' }}">Draft</a></li>
                            <li><a href="{{ url('invoices-recurring') }}"
                                    class="{{ Request::is('invoices-recurring') ? 'active' : '' }}">Recurring</a></li>
                            <li><a href="{{ url('invoices-cancelled') }}"
                                    class="{{ Request::is('invoices-cancelled') ? 'active' : '' }}">Cancelled</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
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
