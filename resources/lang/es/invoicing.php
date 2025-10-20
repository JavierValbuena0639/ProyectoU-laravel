<?php

return [
    'breadcrumb' => 'Facturación',
    'page_title' => 'Facturación - SumAxia',

    'title' => 'Gestión de Facturas',
    'subtitle' => 'Administra facturas, cotizaciones y documentos fiscales',

    'filters' => [
        'status' => 'Estado',
        'client' => 'Cliente',
        'date' => 'Fecha',
        'all' => 'Todos',
        'search_client_placeholder' => 'Buscar cliente...',
        'filter' => 'Filtrar',
    ],

    'actions' => [
        'new_invoice' => 'Nueva Factura',
        'new_quote' => 'Cotización',
        'view' => 'Ver',
        'edit' => 'Editar',
        'pdf' => 'PDF',
        'send' => 'Enviar',
        'send_to_dian' => 'Enviar a DIAN',
        'delete' => 'Eliminar',
    ],

    'table' => [
        'list_title' => 'Lista de Facturas',
        'headers' => [
            'number' => 'Número',
            'client' => 'Cliente',
            'date' => 'Fecha',
            'due' => 'Vencimiento',
            'total' => 'Total',
            'status' => 'Estado',
            'actions' => 'Acciones',
        ],
    ],

    'status_labels' => [
        'paid' => 'Pagada',
        'pending' => 'Pendiente',
        'overdue' => 'Vencida',
        'cancelled' => 'Cancelada',
    ],

    'summary' => [
        'total_invoices' => 'Total Facturas',
        'paid_invoices' => 'Facturas Pagadas',
        'pending' => 'Pendientes',
        'overdue' => 'Vencidas',
    ],

    'chart' => [
        'revenue_title' => 'Ingresos por Facturación',
        'monthly_income_chart' => 'Gráfico de ingresos mensuales',
        'chartjs_pending' => 'Integración con Chart.js pendiente',
    ],

    'sent_to_dian_success' => 'Factura enviada/puesta en cola hacia DIAN.',
    'sent_to_dian_error' => 'Error al enviar la factura a DIAN.',
];