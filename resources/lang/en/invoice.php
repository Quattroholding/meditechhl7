<?php

return [
    // General
    'title' => 'Invoices',
    'invoice' => 'Invoice',
    'invoices' => 'Invoices',
    'invoice_id' => 'Invoice ID',
    'invoice_number' => 'Invoice Number',
    'invoice_date' => 'Invoice Date',
    'due_date' => 'Due Date',
    'issue_date' => 'Issue Date',
    'created_at' => 'Created On',
    'updated_at' => 'Updated On',
    'code' => 'code',
    // Search and Filters
    'search' => 'Search',
    'search_placeholder' => 'Search by ID, number or patient...',
    'filter_by_status' => 'Filter by status',
    'all_statuses' => 'All statuses',
    'items_per_page' => 'items per page',
    'showing' => 'Showing',
    'to' => 'to',
    'of' => 'of',
    'results' => 'results',
    'clear_search' => 'Clear Search',
    'clear_filter' => 'Clear Filter',
    'clear_filters' => 'Clear Filters',

    // Table Headers
    'type' => 'Type',
    'bill_to' => 'Bill To',
    'bill_by' => 'Billed By',
    'patient' => 'Patient',
    'amount' => 'Amount',
    'total_amount' => 'Total Amount',
    'subtotal' => 'Subtotal',
    'tax' => 'Tax',
    'statuss' => 'Status',
    'payment_statuss' => 'Payment Status',
    'actions' => 'Actions',
    'practitioner' => 'Practitioner',
    'client' => 'Client',

    // Status Values
    'status' => [
        'draft' => 'Draft',
        'issued' => 'Issued',
        'sent' => 'Sent',
        'paid' => 'Paid',
        'cancelled' => 'Cancelled',
        'partially_paid' => 'Partially Paid',
        'overdue' => 'Overdue',
    ],

    // Payment Status
    'payment_status' => [
        'unpaid' => 'Unpaid',
        'paid' => 'Paid',
        'partial' => 'Partial Payment',
        'overdue' => 'Overdue',
        'pending' => 'Pending',
    ],

    // Payment Status
    'payment_status_class' => [
        'unpaid' => 'status-pink',
        'paid' => 'status-green',
        'partial' => 'status-orange',
        'overdue' => 'status-default',
        'pending' => 'Pending',
    ],

    // Types
    'types' => [
        'invoice' => 'Invoice',
        'credit_note' => 'Credit Note',
        'debit_note' => 'Debit Note',
        'estimate' => 'Estimate',
        'receipt' => 'Receipt',
    ],

    // Actions
    'view_details' => 'View Details',
    'download_pdf' => 'Download PDF',
    'mark_as_sent' => 'Mark as Sent',
    'mark_as_paid' => 'Mark as Paid',
    'send_invoice' => 'Send Invoice',
    'clone_invoice' => 'Clone Invoice',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'cancel' => 'Cancel',
    'print' => 'Print',
    'export' => 'Export',

    // Messages
    'no_invoices_found' => 'No invoices found',
    'no_invoices_message' => 'There are no invoices registered in the system.',
    'no_invoices_filtered' => 'There are no invoices matching the applied filters.',
    'invoice_marked_paid' => 'Invoice successfully marked as paid.',
    'invoice_marked_sent' => 'Invoice successfully marked as sent.',
    'invoice_deleted' => 'Invoice successfully deleted.',
    'loading' => 'Loading...',
    'loading_data' => 'Loading data...',

    // Confirmations
    'confirm_mark_paid' => 'Are you sure you want to mark this invoice as paid?',
    'confirm_mark_sent' => 'Are you sure you want to mark this invoice as sent?',
    'confirm_delete' => 'Are you sure you want to delete this invoice? This action cannot be undone.',
    'confirm_cancel' => 'Are you sure you want to cancel this invoice?',

    // Details
    'invoice_details' => 'Invoice Details',
    'line_items' => 'Line Items',
    'service_description' => 'Service Description',
    'quantity' => 'Quantity',
    'unit_price' => 'Unit Price',
    'line_total' => 'Line Total',
    'subtotal_amount' => 'Subtotal',
    'tax_amount' => 'Taxes',
    'total_net' => 'Net Total',
    'amount_paid' => 'Amount Paid',
    'amount_due' => 'Amount Due',
    'balance' => 'Balance',
    'identifier' => 'Identifier',
    'cpt_code' => 'CPT Code',
    'no_line_items' => 'No line items recorded.',
    'notes' => 'Notes',
    'default_terms' => 'Standard terms and conditions apply.',
    'organization_info' => 'Organization information',
    'patient_id' => 'Patient ID',
    'patient_not_available' => 'Patient not available',
    'phone' => 'Phone',
    'email' => 'Email',
    'tax_id' => 'Tax ID',

    // Payment
    'payment_information' => 'Payment Information',
    'payment_date' => 'Payment Date',
    'payment_method' => 'Payment Method',
    'payment_reference' => 'Payment Reference',
    'transaction_id' => 'Transaction ID',
    'payment_notes' => 'Payment Notes',
    'payment_terms' => 'Payment Terms',

    // Billing Information
    'billing_information' => 'Billing Information',
    'billing_address' => 'Billing Address',
    'issuer_organization' => 'Issuing Organization',
    'recipient' => 'Recipient',
    'encounter' => 'Encounter',
    'encounter_id' => 'Encounter ID',

    // Navigation
    'create_invoice' => 'Create Invoice',
    'new_invoice' => 'New Invoice',
    'invoice_list' => 'Invoice List',
    'back_to_list' => 'Back to List',

    // Currencies
    'currency' => 'Currency',
    'usd' => 'USD',
    'eur' => 'EUR',
    'pab' => 'PAB',

    // Periods
    'days' => 'days',
    'overdue_by' => 'Overdue by',
    'due_in' => 'Due in',
    'due_today' => 'Due today',

    // File Operations
    'download' => 'Download',
    'preview' => 'Preview',
    'email' => 'Send by Email',
    'share' => 'Share',

    // Statistics
    'total_invoices' => 'Total Invoices',
    'paid_invoices' => 'Paid Invoices',
    'pending_invoices' => 'Pending Invoices',
    'overdue_invoices' => 'Overdue Invoices',
    'revenue' => 'Revenue',
    'outstanding_amount' => 'Outstanding Amount',

    // Validation Messages
    'validation' => [
        'required' => 'This field is required.',
        'numeric' => 'This field must be numeric.',
        'date' => 'This field must be a valid date.',
        'min' => 'This field must be at least :min characters.',
        'max' => 'This field must not exceed :max characters.',
        'email' => 'This field must be a valid email address.',
    ],

    // Error Messages
    'errors' => [
        'not_found' => 'Invoice not found.',
        'already_paid' => 'This invoice is already paid.',
        'cannot_delete_paid' => 'Cannot delete a paid invoice.',
        'insufficient_amount' => 'The amount is insufficient.',
        'invalid_status' => 'Invalid invoice status.',
        'permission_denied' => 'You do not have permission to perform this action.',
    ],
];
