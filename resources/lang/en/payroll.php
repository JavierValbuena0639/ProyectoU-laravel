<?php

return [
    'title' => 'Payroll',
    'module' => 'Payroll',
    'header' => 'Payroll Management',
    'subheader' => 'Manage employees, salaries, and payroll processes',

    'tabs' => [
        'employees' => 'Employees',
        'payrolls' => 'Payrolls',
        'reports' => 'Reports',
    ],

    'filters' => [
        'department' => 'Department',
        'status' => 'Status',
        'search' => 'Search',
        'search_placeholder' => 'Name or ID...',
        'all' => 'All',
    ],

    'actions' => [
        'new_employee' => 'New Employee',
        'process_payroll' => 'Process Payroll',
    ],

    'table' => [
        'employees_list' => 'Employees List',
        'id' => 'ID',
        'employee' => 'Employee',
        'position' => 'Position',
        'department' => 'Department',
        'salary' => 'Salary',
        'status' => 'Status',
        'actions' => 'Actions',
    ],

    'status_labels' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'vacation' => 'Vacation',
    ],

    'tooltips' => [
        'view_profile' => 'View profile',
        'edit' => 'Edit',
        'payroll' => 'Payroll',
        'reports' => 'Reports',
    ],

    'reports' => [
        'type' => 'Report Type',
        'select_type' => 'Select type',
        'period' => 'Period',
        'last_month' => 'Last month',
        'format' => 'Format',
        'pdf' => 'PDF',
        'generate_report' => 'Generate Report',
        'download' => 'Download',

        'monthly_summary' => 'Monthly Summary',
        'monthly_subtitle' => 'Monthly payroll summary with comparison',
        'total_paid' => 'Total Paid',
        'employees' => 'Employees',
        'average' => 'Average',
        'view_full_report' => 'View Full Report',

        'by_department' => 'By Department',
        'dept_distribution' => 'Cost distribution by department',
        'accounting' => 'Accounting',
        'sales' => 'Sales',
        'administration' => 'Administration',
        'view_breakdown' => 'View Breakdown',

        'deductions' => 'Deductions',
        'tax_summary' => 'Tax and deductions summary',
        'isr' => 'Income Tax (ISR)',
        'imss' => 'Social Security (IMSS)',
        'total' => 'Total',
        'view_detail' => 'View Detail',

        'recent_reports' => 'Recent Reports',

        'recent_table' => [
            'report' => 'Report',
            'period' => 'Period',
            'generated' => 'Generated',
            'format' => 'Format',
            'actions' => 'Actions',
        ],

        'buttons' => [
            'view' => 'View',
            'download' => 'Download',
            'delete' => 'Delete',
        ],

        'filters' => [
            'period' => 'Period',
            'status' => 'Status',
            'type' => 'Type',
            'all' => 'All',
        ],

        'actions' => [
            'new_payroll' => 'New Payroll',
            'export' => 'Export',
            'history' => 'Payroll History',
        ],

        'table' => [
            'number' => 'Number',
            'period' => 'Period',
            'type' => 'Type',
            'employees' => 'Employees',
            'total' => 'Total',
            'status' => 'Status',
            'payment_date' => 'Payment Date',
            'actions' => 'Actions',
        ],

        'payroll_status' => [
            'paid' => 'Paid',
            'approved' => 'Approved',
            'draft' => 'Draft',
        ],

        'preview' => [
            'title' => 'Report preview',
            'note' => 'Use download if you need to save it.',
            'download_pdf' => 'Download PDF',
            'error' => 'Could not display PDF: ',
        ],
    ],

    // Payrolls tab: filters, summary cards and process section
    'payrolls' => [
        'filters' => [
            'period' => 'Period',
            'status' => 'Status',
            'type' => 'Type',
            'all' => 'All',
            'type_options' => [
                'weekly' => 'Weekly',
                'biweekly' => 'Biweekly',
                'monthly' => 'Monthly',
                'extraordinary' => 'Extraordinary',
            ],
            'status_options' => [
                'draft' => 'Draft',
                'approved' => 'Approved',
                'paid' => 'Paid',
            ],
        ],

        'summary_cards' => [
            'total_employees' => 'Total Employees',
            'active_employees' => 'Active Employees',
            'on_vacation' => 'On Vacation',
            'monthly_payroll' => 'Monthly Payroll',
        ],

        'process' => [
            'title' => 'Payroll Process',
            'current_period' => 'Current Period',
            'period_range' => 'From 1 to :end',
            'status' => 'Status',
            'ready' => 'Ready to process',
            'verified' => 'All data verified',
            'total_to_pay' => 'Total to Pay',
            'includes_deductions' => 'Includes deductions',
        ],
    ],
];