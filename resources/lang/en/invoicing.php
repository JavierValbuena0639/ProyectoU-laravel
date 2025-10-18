<?php

return [
    'breadcrumb' => 'Invoicing',
    'page_title' => 'Invoicing - SumAxia',

    'title' => 'Invoice Management',
    'subtitle' => 'Manage invoices, quotes, and tax documents',

    'filters' => [
        'status' => 'Status',
        'client' => 'Client',
        'date' => 'Date',
        'all' => 'All',
        'search_client_placeholder' => 'Search client...',
        'filter' => 'Filter',
    ],

    'actions' => [
        'new_invoice' => 'New Invoice',
        'new_quote' => 'Quote',
        'view' => 'View',
        'edit' => 'Edit',
        'pdf' => 'PDF',
        'send' => 'Send',
        'delete' => 'Delete',
    ],

    'table' => [
        'list_title' => 'Invoice List',
        'headers' => [
            'number' => 'Number',
            'client' => 'Client',
            'date' => 'Date',
            'due' => 'Due Date',
            'total' => 'Total',
            'status' => 'Status',
            'actions' => 'Actions',
        ],
    ],

    'status_labels' => [
        'paid' => 'Paid',
        'pending' => 'Pending',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ],

    'summary' => [
        'total_invoices' => 'Total Invoices',
        'paid_invoices' => 'Paid Invoices',
        'pending' => 'Pending',
        'overdue' => 'Overdue',
    ],

    'chart' => [
        'revenue_title' => 'Billing Revenue',
        'monthly_income_chart' => 'Monthly revenue chart',
        'chartjs_pending' => 'Chart.js integration pending',
    ],
];