<?php

return [
    'title' => 'Nómina',
    'module' => 'Nómina',
    'header' => 'Gestión de Nómina',
    'subheader' => 'Administra empleados, salarios y procesos de nómina',

    'tabs' => [
        'employees' => 'Empleados',
        'payrolls' => 'Nóminas',
        'reports' => 'Reportes',
    ],

    'filters' => [
        'department' => 'Departamento',
        'status' => 'Estado',
        'search' => 'Buscar',
        'search_placeholder' => 'Nombre o ID...',
        'all' => 'Todos',
    ],

    'actions' => [
        'new_employee' => 'Nuevo Empleado',
        'process_payroll' => 'Procesar Nómina',
    ],

    'table' => [
        'employees_list' => 'Lista de Empleados',
        'id' => 'ID',
        'employee' => 'Empleado',
        'position' => 'Puesto',
        'department' => 'Departamento',
        'salary' => 'Salario',
        'status' => 'Estado',
        'actions' => 'Acciones',
    ],

    'status_labels' => [
        'active' => 'Activo',
        'inactive' => 'Inactivo',
        'vacation' => 'Vacaciones',
    ],

    'tooltips' => [
        'view_profile' => 'Ver perfil',
        'edit' => 'Editar',
        'payroll' => 'Nómina',
        'reports' => 'Reportes',
    ],

    'reports' => [
        'type' => 'Tipo de Reporte',
        'select_type' => 'Seleccionar tipo',
        'period' => 'Período',
        'last_month' => 'Último mes',
        'format' => 'Formato',
        'pdf' => 'PDF',
        'generate_report' => 'Generar Reporte',
        'download' => 'Descargar',

        'monthly_summary' => 'Resumen Mensual',
        'monthly_subtitle' => 'Resumen de nómina del mes actual con comparativo',
        'total_paid' => 'Total Pagado',
        'employees' => 'Empleados',
        'average' => 'Promedio',
        'view_full_report' => 'Ver Reporte Completo',

        'by_department' => 'Por Departamento',
        'dept_distribution' => 'Distribución de costos por departamento',
        'accounting' => 'Contabilidad',
        'sales' => 'Ventas',
        'administration' => 'Administración',
        'view_breakdown' => 'Ver Desglose',

        'deductions' => 'Deducciones',
        'tax_summary' => 'Resumen de impuestos y deducciones',
        'isr' => 'ISR',
        'imss' => 'IMSS',
        'total' => 'Total',
        'view_detail' => 'Ver Detalle',

        'recent_reports' => 'Reportes Recientes',

        'recent_table' => [
            'report' => 'Reporte',
            'period' => 'Período',
            'generated' => 'Generado',
            'format' => 'Formato',
            'actions' => 'Acciones',
        ],

        'buttons' => [
            'view' => 'Ver',
            'download' => 'Descargar',
            'delete' => 'Eliminar',
        ],

        'filters' => [
            'period' => 'Período',
            'status' => 'Estado',
            'type' => 'Tipo',
            'all' => 'Todos',
        ],

        'actions' => [
            'new_payroll' => 'Nueva Nómina',
            'export' => 'Exportar',
            'history' => 'Historial de Nóminas',
        ],

        'table' => [
            'number' => 'Número',
            'period' => 'Período',
            'type' => 'Tipo',
            'employees' => 'Empleados',
            'total' => 'Total',
            'status' => 'Estado',
            'payment_date' => 'Fecha Pago',
            'actions' => 'Acciones',
        ],

        'payroll_status' => [
            'paid' => 'Pagada',
            'approved' => 'Aprobada',
            'draft' => 'Borrador',
        ],

        'preview' => [
            'title' => 'Vista previa del reporte',
            'note' => 'Usa descargar si necesitas guardarlo.',
            'download_pdf' => 'Descargar PDF',
            'error' => 'No se pudo mostrar el PDF: ',
        ],
    ],

    // Pestaña Nóminas: filtros, tarjetas de resumen y sección de proceso
    'payrolls' => [
        'filters' => [
            'period' => 'Período',
            'status' => 'Estado',
            'type' => 'Tipo',
            'all' => 'Todos',
            'type_options' => [
                'weekly' => 'Semanal',
                'biweekly' => 'Quincenal',
                'monthly' => 'Mensual',
                'extraordinary' => 'Extraordinaria',
            ],
            'status_options' => [
                'draft' => 'Borrador',
                'approved' => 'Aprobada',
                'paid' => 'Pagada',
            ],
        ],

        'summary_cards' => [
            'total_employees' => 'Total Empleados',
            'active_employees' => 'Empleados Activos',
            'on_vacation' => 'En Vacaciones',
            'monthly_payroll' => 'Nómina Mensual',
        ],

        'process' => [
            'title' => 'Proceso de Nómina',
            'current_period' => 'Período Actual',
            'period_range' => 'Del 1 al :end',
            'status' => 'Estado',
            'ready' => 'Listo para procesar',
            'verified' => 'Todos los datos verificados',
            'total_to_pay' => 'Total a Pagar',
            'includes_deductions' => 'Incluye deducciones',
        ],
    ],
];