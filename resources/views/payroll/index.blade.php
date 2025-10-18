<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('payroll.title') }} - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">SumAxia</a>
                    <span class="ml-2 text-sm text-gray-500">/ {{ __('payroll.module') }}</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    @php $currentLocale = app()->getLocale(); @endphp
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('locale.switch', ['lang' => 'es']) }}" aria-label="Español" title="Español"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'es' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">🇪🇸</a>
                        <a href="{{ route('locale.switch', ['lang' => 'en']) }}" aria-label="English" title="English"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'en' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">🇺🇸</a>
                    </div>
                    <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-home mr-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('payroll.header') }}</h1>
            <p class="text-gray-600">{{ __('payroll.subheader') }}</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6">
            <nav class="flex space-x-8">
                <a href="#" onclick="showTab('empleados')" id="tab-empleados" class="border-b-2 border-blue-500 text-blue-600 py-2 px-1 text-sm font-medium">
                    {{ __('payroll.tabs.employees') }}
                </a>
                <a href="#" onclick="showTab('nominas')" id="tab-nominas" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-2 px-1 text-sm font-medium">
                    {{ __('payroll.tabs.payrolls') }}
                </a>
                <a href="#" onclick="showTab('reportes')" id="tab-reportes" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-2 px-1 text-sm font-medium">
                    {{ __('payroll.tabs.reports') }}
                </a>
            </nav>
        </div>

        <!-- Actions and Filters -->
        <div id="content-empleados" class="tab-content">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('payroll.filters.department') }}</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>{{ __('payroll.filters.all') }}</option>
                                <option>Administración</option>
                                <option>Contabilidad</option>
                                <option>Ventas</option>
                                <option>Recursos Humanos</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('payroll.filters.status') }}</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>{{ __('payroll.filters.all') }}</option>
                                <option>{{ __('payroll.status_labels.active') }}</option>
                                <option>{{ __('payroll.status_labels.inactive') }}</option>
                                <option>{{ __('payroll.status_labels.vacation') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('payroll.filters.search') }}</label>
                            <input type="text" placeholder="{{ __('payroll.filters.search_placeholder') }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('payroll.employees.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-plus mr-2"></i>{{ __('payroll.actions.new_employee') }}
                        </a>
                        <a href="{{ route('payroll.process') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-calculator mr-2"></i>{{ __('payroll.actions.process_payroll') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Employees Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('payroll.table.employees_list') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.table.id') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.table.employee') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.table.position') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.table.department') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.table.salary') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.table.status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $employees = [
                                    ['id' => 'EMP001', 'name' => 'Juan Pérez', 'email' => 'juan.perez@empresa.com', 'position' => 'Contador Senior', 'department' => 'Contabilidad', 'salary' => 45000, 'status' => 'active'],
                                    ['id' => 'EMP002', 'name' => 'María González', 'email' => 'maria.gonzalez@empresa.com', 'position' => 'Gerente de Ventas', 'department' => 'Ventas', 'salary' => 55000, 'status' => 'active'],
                                    ['id' => 'EMP003', 'name' => 'Carlos Rodríguez', 'email' => 'carlos.rodriguez@empresa.com', 'position' => 'Analista Financiero', 'department' => 'Administración', 'salary' => 38000, 'status' => 'active'],
                                    ['id' => 'EMP004', 'name' => 'Ana Martínez', 'email' => 'ana.martinez@empresa.com', 'position' => 'Especialista en RRHH', 'department' => 'Recursos Humanos', 'salary' => 42000, 'status' => 'vacation'],
                                    ['id' => 'EMP005', 'name' => 'Luis Hernández', 'email' => 'luis.hernandez@empresa.com', 'position' => 'Asistente Contable', 'department' => 'Contabilidad', 'salary' => 28000, 'status' => 'active'],
                                    ['id' => 'EMP006', 'name' => 'Sofia López', 'email' => 'sofia.lopez@empresa.com', 'position' => 'Ejecutiva de Ventas', 'department' => 'Ventas', 'salary' => 35000, 'status' => 'active'],
                                    ['id' => 'EMP007', 'name' => 'Diego Torres', 'email' => 'diego.torres@empresa.com', 'position' => 'Auditor Interno', 'department' => 'Administración', 'salary' => 48000, 'status' => 'inactive'],
                                ];
                            @endphp
                            @foreach($employees as $employee)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                    {{ $employee['id'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">{{ substr($employee['name'], 0, 2) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $employee['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $employee['email'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $employee['position'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $employee['department'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ${{ number_format($employee['salary'], 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @switch($employee['status'])
                                            @case('active') bg-green-100 text-green-800 @break
                                            @case('inactive') bg-red-100 text-red-800 @break
                                            @case('vacation') bg-yellow-100 text-yellow-800 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch">
                                        @switch($employee['status'])
                                            @case('active') {{ __('payroll.status_labels.active') }} @break
                                            @case('inactive') {{ __('payroll.status_labels.inactive') }} @break
                                            @case('vacation') {{ __('payroll.status_labels.vacation') }} @break
                                            @default {{ ucfirst($employee['status']) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3" title="{{ __('payroll.tooltips.view_profile') }}"
                                            data-action="view"
                                            data-id="{{ $employee['id'] }}"
                                            data-name="{{ $employee['name'] }}"
                                            data-position="{{ $employee['position'] }}"
                                            data-department="{{ $employee['department'] }}"
                                            data-salary="{{ $employee['salary'] }}"
                                            data-status="{{ $employee['status'] }}"
                                            aria-label="{{ __('payroll.tooltips.view_profile') }} {{ $employee['name'] }}">
                                        <i class="fas fa-user"></i>
                                    </button>
                                    <button class="text-green-600 hover:text-green-900 mr-3" title="{{ __('payroll.tooltips.edit') }}"
                                            data-action="edit"
                                            data-id="{{ $employee['id'] }}"
                                            data-name="{{ $employee['name'] }}"
                                            aria-label="{{ __('payroll.tooltips.edit') }} {{ $employee['name'] }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-purple-600 hover:text-purple-900 mr-3" title="{{ __('payroll.tooltips.payroll') }}"
                                            data-action="payroll"
                                            data-id="{{ $employee['id'] }}"
                                            data-name="{{ $employee['name'] }}"
                                            aria-label="{{ __('payroll.tooltips.payroll') }} {{ $employee['name'] }}">
                                        <i class="fas fa-money-bill"></i>
                                    </button>
                                    <button class="text-yellow-600 hover:text-yellow-900 mr-3" title="{{ __('payroll.tooltips.reports') }}"
                                            data-action="reports"
                                            data-id="{{ $employee['id'] }}"
                                            data-name="{{ $employee['name'] }}"
                                            aria-label="{{ __('payroll.tooltips.reports') }} {{ $employee['name'] }}">
                                        <i class="fas fa-chart-bar"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Nóminas Tab Content -->
        <div id="content-nominas" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('payroll.payrolls.filters.period') }}</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>{{ __('payroll.payrolls.filters.all') }}</option>
                                <option>Enero 2024</option>
                                <option>Febrero 2024</option>
                                <option>Marzo 2024</option>
                                <option>Abril 2024</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('payroll.payrolls.filters.status') }}</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>{{ __('payroll.payrolls.filters.all') }}</option>
                                <option>{{ __('payroll.payrolls.filters.status_options.draft') }}</option>
                                <option>{{ __('payroll.payrolls.filters.status_options.approved') }}</option>
                                <option>{{ __('payroll.payrolls.filters.status_options.paid') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('payroll.payrolls.filters.type') }}</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>{{ __('payroll.payrolls.filters.all') }}</option>
                                <option>{{ __('payroll.payrolls.filters.type_options.weekly') }}</option>
                                <option>{{ __('payroll.payrolls.filters.type_options.biweekly') }}</option>
                                <option>{{ __('payroll.payrolls.filters.type_options.monthly') }}</option>
                                <option>{{ __('payroll.payrolls.filters.type_options.extraordinary') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('payroll.process') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-calculator mr-2"></i>{{ __('payroll.reports.actions.new_payroll') }}
                        </a>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-download mr-2"></i>{{ __('payroll.reports.actions.export') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('payroll.reports.actions.history') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.table.number') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.table.period') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.table.type') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.table.employees') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.table.total') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.table.status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.table.payment_date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $payrolls = [
                                    ['number' => 'NOM-2024-04-001', 'period' => '01/04/2024 - 15/04/2024', 'type' => 'Quincenal', 'employees' => 5, 'total' => 208000, 'status' => 'paid', 'payment_date' => '16/04/2024'],
                                    ['number' => 'NOM-2024-03-002', 'period' => '16/03/2024 - 31/03/2024', 'type' => 'Quincenal', 'employees' => 5, 'total' => 208000, 'status' => 'paid', 'payment_date' => '01/04/2024'],
                                    ['number' => 'NOM-2024-03-001', 'period' => '01/03/2024 - 15/03/2024', 'type' => 'Quincenal', 'employees' => 4, 'total' => 166000, 'status' => 'paid', 'payment_date' => '16/03/2024'],
                                    ['number' => 'NOM-2024-02-002', 'period' => '16/02/2024 - 29/02/2024', 'type' => 'Quincenal', 'employees' => 4, 'total' => 166000, 'status' => 'approved', 'payment_date' => '01/03/2024'],
                                    ['number' => 'NOM-2024-02-001', 'period' => '01/02/2024 - 15/02/2024', 'type' => 'Quincenal', 'employees' => 4, 'total' => 166000, 'status' => 'draft', 'payment_date' => '16/02/2024'],
                                ];
                            @endphp
                            @foreach($payrolls as $payroll)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payroll['number'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payroll['period'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payroll['type'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payroll['employees'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($payroll['total']) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($payroll['status'] === 'paid')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('payroll.reports.payroll_status.paid') }}</span>
                                    @elseif($payroll['status'] === 'approved')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ __('payroll.reports.payroll_status.approved') }}</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('payroll.reports.payroll_status.draft') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payroll['payment_date'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900" title="{{ __('payroll.reports.buttons.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-green-600 hover:text-green-900" title="{{ __('payroll.reports.buttons.download') }} PDF">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        @if($payroll['status'] === 'draft')
                                        <button class="text-yellow-600 hover:text-yellow-900" title="{{ __('payroll.tooltips.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="{{ __('payroll.reports.buttons.delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reportes Tab Content -->
        <div id="content-reportes" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('payroll.reports.type') }}</label>
                            <select id="report-type" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">{{ __('payroll.reports.select_type') }}</option>
                                <option value="summary">{{ __('payroll.tabs.payrolls') }} {{ __('payroll.reports.monthly_summary') }}</option>
                                <option value="detail">{{ __('payroll.tabs.employees') }} Detail</option>
                                <option value="compare">Monthly Comparison</option>
                                <option value="taxes">{{ __('payroll.reports.deductions') }} &amp; Taxes</option>
                                <option value="departments">{{ __('payroll.reports.by_department') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('payroll.reports.period') }}</label>
                            <select id="report-period" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="month">{{ __('payroll.reports.last_month') }}</option>
                                <option value="quarter">Last 3 months</option>
                                <option value="year">Last year</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('payroll.reports.format') }}</label>
                            <select id="report-format" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="pdf">{{ __('payroll.reports.pdf') }}</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button id="btn-generate-report" type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-chart-bar mr-2"></i>{{ __('payroll.reports.generate_report') }}
                        </button>
                        <button id="btn-download-report" type="button" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-download mr-2"></i>{{ __('payroll.reports.buttons.download') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reports Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">{{ __('payroll.reports.monthly_summary') }}</h4>
                        <i class="fas fa-chart-line text-blue-500 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">{{ __('payroll.reports.monthly_subtitle') }}</p>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('payroll.reports.total_paid') }}:</span>
                            <span class="text-sm font-medium">@money(416000)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('payroll.reports.employees') }}:</span>
                            <span class="text-sm font-medium">5</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('payroll.reports.average') }}:</span>
                            <span class="text-sm font-medium">@money(83200)</span>
                        </div>
                    </div>
                    <button type="button" class="js-card-action w-full mt-4 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded text-sm" data-action="full" data-format="pdf" data-type="summary">
                        {{ __('payroll.reports.view_full_report') }}
                    </button>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">{{ __('payroll.reports.by_department') }}</h4>
                        <i class="fas fa-building text-green-500 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">{{ __('payroll.reports.dept_distribution') }}</p>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('payroll.reports.accounting') }}:</span>
                            <span class="text-sm font-medium">@money(146000)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('payroll.reports.sales') }}:</span>
                            <span class="text-sm font-medium">@money(110000)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('payroll.reports.administration') }}:</span>
                            <span class="text-sm font-medium">@money(76000)</span>
                        </div>
                    </div>
                    <button type="button" class="js-card-action w-full mt-4 bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded text-sm" data-action="breakdown" data-format="excel" data-type="departments">
                        {{ __('payroll.reports.view_breakdown') }}
                    </button>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">{{ __('payroll.reports.deductions') }}</h4>
                        <i class="fas fa-calculator text-red-500 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">{{ __('payroll.reports.tax_summary') }}</p>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('payroll.reports.isr') }}:</span>
                            <span class="text-sm font-medium">@money(62400)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('payroll.reports.imss') }}:</span>
                            <span class="text-sm font-medium">@money(41600)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('payroll.reports.total') }}:</span>
                            <span class="text-sm font-medium">@money(104000)</span>
                        </div>
                    </div>
                    <button type="button" class="js-card-action w-full mt-4 bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded text-sm" data-action="detail" data-format="pdf" data-type="taxes">
                        {{ __('payroll.reports.view_detail') }}
                    </button>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('payroll.reports.recent_reports') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.recent_table.report') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.recent_table.period') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.recent_table.generated') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.recent_table.format') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('payroll.reports.recent_table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $reports = session('recent_reports', [
                                    ['id' => 'sample-1', 'name' => 'Resumen de Nómina', 'period' => 'Abril 2024', 'period_key' => 'month', 'type' => 'summary', 'generated' => '15/04/2024 10:30', 'format' => 'PDF'],
                                    ['id' => 'sample-2', 'name' => 'Detalle por Empleado', 'period' => 'Q1 2024', 'period_key' => 'quarter', 'type' => 'employees', 'generated' => '31/03/2024 16:45', 'format' => 'Excel'],
                                    ['id' => 'sample-3', 'name' => 'Comparativo Mensual', 'period' => 'Ene-Mar 2024', 'period_key' => 'quarter', 'type' => 'compare', 'generated' => '28/03/2024 09:15', 'format' => 'PDF'],
                                    ['id' => 'sample-4', 'name' => 'Costos por Departamento', 'period' => 'Marzo 2024', 'period_key' => 'month', 'type' => 'departments', 'generated' => '25/03/2024 14:20', 'format' => 'CSV'],
                                ]);
                            @endphp
                            @foreach($reports as $report)
                            <tr class="hover:bg-gray-50" data-id="{{ $report['id'] ?? '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $report['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report['period'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report['generated'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $report['format'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button type="button" class="js-report-view text-blue-600 hover:text-blue-900" title="{{ __('payroll.reports.buttons.view') }}" data-format="pdf" data-period="{{ $report['period_key'] ?? 'month' }}" data-type="{{ $report['type'] ?? 'summary' }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="js-report-download text-green-600 hover:text-green-900" title="{{ __('payroll.reports.buttons.download') }}" data-format="{{ strtolower($report['format']) }}" data-period="{{ $report['period_key'] ?? 'month' }}" data-type="{{ $report['type'] ?? 'summary' }}">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button type="button" class="js-report-delete text-red-600 hover:text-red-900" title="{{ __('payroll.reports.buttons.delete') }}" data-id="{{ $report['id'] ?? '' }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('payroll.payrolls.summary_cards.total_employees') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ count($employees) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-user-check text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('payroll.payrolls.summary_cards.active_employees') }}</p>
                        <p class="text-2xl font-semibold text-green-600">{{ count(array_filter($employees, fn($e) => $e['status'] === 'active')) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-umbrella-beach text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('payroll.payrolls.summary_cards.on_vacation') }}</p>
                        <p class="text-2xl font-semibold text-yellow-600">{{ count(array_filter($employees, fn($e) => $e['status'] === 'vacation')) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-dollar-sign text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('payroll.payrolls.summary_cards.monthly_payroll') }}</p>
                        <p class="text-2xl font-semibold text-purple-600">${{ number_format(array_sum(array_column($employees, 'salary')), 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll Process Section -->
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('payroll.payrolls.process.title') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 border border-gray-200 rounded-lg">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">{{ __('payroll.payrolls.process.current_period') }}</h4>
                    <p class="text-sm text-gray-600">{{ date('F Y') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('payroll.payrolls.process.period_range', ['end' => date('t')]) }}</p>
                </div>
                
                <div class="text-center p-4 border border-gray-200 rounded-lg">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">{{ __('payroll.payrolls.process.status') }}</h4>
                    <p class="text-sm text-green-600 font-medium">{{ __('payroll.payrolls.process.ready') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('payroll.payrolls.process.verified') }}</p>
                </div>
                
                <div class="text-center p-4 border border-gray-200 rounded-lg">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-calculator text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">{{ __('payroll.payrolls.process.total_to_pay') }}</h4>
                    <p class="text-sm text-purple-600 font-medium">${{ number_format(array_sum(array_column($employees, 'salary')), 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ __('payroll.payrolls.process.includes_deductions') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Vista previa PDF de Reporte -->
    <div id="pdf-preview-modal" class="fixed inset-0 z-50 hidden">
        <div id="pdf-preview-overlay" class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="relative mx-auto mt-10 bg-white rounded-lg shadow-xl w-11/12 md:w-3/4 lg:w-2/3">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 id="pdf-preview-title" class="text-lg font-semibold text-gray-900">{{ __('payroll.reports.preview.title') }}</h3>
                <button id="pdf-preview-close" class="text-gray-600 hover:text-gray-900" title="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-0">
                <iframe id="pdf-preview-frame" class="w-full" style="height: 70vh;" title="Vista previa PDF"></iframe>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                <div class="text-xs text-gray-500">{{ __('payroll.reports.preview.note') }}</div>
                <button id="pdf-preview-download" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm">
                    <i class="fas fa-download mr-1"></i> {{ __('payroll.reports.preview.download_pdf') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Perfil de Empleado -->
    <div id="employee-profile-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Perfil de Empleado</h3>
                    <button id="modal-close" class="text-gray-500 hover:text-gray-700" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Nombre</p>
                        <p id="modal-emp-name" class="text-base font-medium text-gray-900">-</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Puesto</p>
                            <p id="modal-emp-position" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Departamento</p>
                            <p id="modal-emp-department" class="text-base text-gray-900">-</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Salario</p>
                            <p id="modal-emp-salary" class="text-base text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Estado</p>
                            <span id="modal-emp-status" class="inline-flex text-xs leading-5 font-semibold rounded-full px-2 py-1 bg-gray-100 text-gray-800">-</span>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t flex items-center justify-between">
                    <div class="text-xs text-gray-500">Acciones rápidas</div>
                    <div class="flex space-x-2">
                        <button id="modal-go-payroll" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded text-sm">
                            <i class="fas fa-money-bill mr-1"></i> Nómina
                        </button>
                        <button id="modal-go-reports" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded text-sm">
                            <i class="fas fa-chart-bar mr-1"></i> Reportes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- Report PDF Preview Modal helpers ---
        let lastPreviewBlobUrl = null;
        function openPdfPreview(url, title = '{{ __('payroll.reports.preview.title') }}', downloadUrl = null, downloadFormat = 'pdf') {
            const modal = document.getElementById('pdf-preview-modal');
            const frame = document.getElementById('pdf-preview-frame');
            const titleEl = document.getElementById('pdf-preview-title');
            const dlBtn = document.getElementById('pdf-preview-download');
            titleEl.textContent = title || '{{ __('payroll.reports.preview.title') }}';
            dlBtn.dataset.downloadUrl = downloadUrl || url;
            const fmt = String(downloadFormat || 'pdf').toLowerCase();
            const labelMap = { pdf: 'PDF', excel: 'Excel', csv: 'CSV' };
            const fmtLabel = labelMap[fmt] || fmt.toUpperCase();
            dlBtn.innerHTML = '<i class="fas fa-download mr-1"></i> ' + (fmtLabel === 'PDF' ? '{{ __('payroll.reports.preview.download_pdf') }}' : '{{ __('payroll.reports.buttons.download') }} ' + fmtLabel);
            fetch(url, { headers: { 'Accept': 'application/pdf' } })
                .then(res => {
                    if (!res.ok) throw new Error('Error loading PDF');
                    return res.blob();
                })
                .then(blob => {
                    if (lastPreviewBlobUrl) URL.revokeObjectURL(lastPreviewBlobUrl);
                    lastPreviewBlobUrl = URL.createObjectURL(blob);
                    frame.src = lastPreviewBlobUrl;
                    modal.classList.remove('hidden');
                })
                .catch(err => {
                    alert('{{ __('payroll.reports.preview.error') }}' + err.message);
                });
        }
        function closePdfPreview() {
            const modal = document.getElementById('pdf-preview-modal');
            const frame = document.getElementById('pdf-preview-frame');
            modal.classList.add('hidden');
            frame.src = '';
            if (lastPreviewBlobUrl) { URL.revokeObjectURL(lastPreviewBlobUrl); lastPreviewBlobUrl = null; }
        }
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('pdf-preview-close').addEventListener('click', closePdfPreview);
            document.getElementById('pdf-preview-overlay').addEventListener('click', closePdfPreview);
            document.getElementById('pdf-preview-download').addEventListener('click', (e) => {
                const url = e.currentTarget.dataset.downloadUrl;
                if (url) window.location.href = url;
            });
        });
        function showTab(tabName) {
            // Hide all tab contents
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('[id^="tab-"]');
            tabs.forEach(tab => {
                tab.classList.remove('border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab content
            const selectedContent = document.getElementById('content-' + tabName);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
            
            // Add active class to selected tab
            const selectedTab = document.getElementById('tab-' + tabName);
            if (selectedTab) {
                selectedTab.classList.remove('border-transparent', 'text-gray-500');
                selectedTab.classList.add('border-blue-500', 'text-blue-600');
            }
        }

        function formatCurrencyMXN(value) {
            const num = Number(value) || 0;
            return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 0 }).format(num);
        }

        function setStatusBadge(el, status) {
            const classes = {
                active: 'bg-green-100 text-green-800',
                inactive: 'bg-red-100 text-red-800',
                vacation: 'bg-yellow-100 text-yellow-800'
            };
            el.className = 'inline-flex text-xs leading-5 font-semibold rounded-full px-2 py-1 ' + (classes[status] || 'bg-gray-100 text-gray-800');
            el.textContent = status === 'active' ? 'Activo' : status === 'inactive' ? 'Inactivo' : status === 'vacation' ? 'Vacaciones' : (status || '-');
        }

        function openEmployeeModal(data) {
            document.getElementById('modal-emp-name').textContent = data.name || '-';
            document.getElementById('modal-emp-position').textContent = data.position || '-';
            document.getElementById('modal-emp-department').textContent = data.department || '-';
            document.getElementById('modal-emp-salary').textContent = formatCurrencyMXN(data.salary);
            setStatusBadge(document.getElementById('modal-emp-status'), data.status);

            const payrollBtn = document.getElementById('modal-go-payroll');
            const reportsBtn = document.getElementById('modal-go-reports');
            payrollBtn.dataset.id = data.id || '';
            payrollBtn.dataset.name = data.name || '';
            reportsBtn.dataset.id = data.id || '';
            reportsBtn.dataset.name = data.name || '';

            document.getElementById('employee-profile-modal').classList.remove('hidden');
        }

        // Initialize the first tab as active on page load
        document.addEventListener('DOMContentLoaded', function() {
            showTab('empleados');

            // Wire up action buttons
            document.querySelectorAll('button[data-action="view"]').forEach(btn => {
                btn.addEventListener('click', () => openEmployeeModal(btn.dataset));
            });

            document.querySelectorAll('button[data-action="edit"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    // En esta demo, redirigimos a crear empleado como punto de partida
                    window.location.href = "{{ route('payroll.employees.create') }}" + '?employee_id=' + encodeURIComponent(btn.dataset.id || '');
                });
            });

            document.querySelectorAll('button[data-action="payroll"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    window.location.href = "{{ route('payroll.process') }}" + '?employee_id=' + encodeURIComponent(btn.dataset.id || '');
                });
            });

            document.querySelectorAll('button[data-action="reports"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    showTab('reportes');
                });
            });

            // Modal controls
            document.getElementById('modal-close').addEventListener('click', () => {
                document.getElementById('employee-profile-modal').classList.add('hidden');
            });
            document.getElementById('employee-profile-modal').addEventListener('click', (e) => {
                if (e.target.id === 'employee-profile-modal') {
                    document.getElementById('employee-profile-modal').classList.add('hidden');
                }
            });
            document.getElementById('modal-go-payroll').addEventListener('click', (e) => {
                const id = e.currentTarget.dataset.id || '';
                window.location.href = "{{ route('payroll.process') }}" + '?employee_id=' + encodeURIComponent(id);
            });
            document.getElementById('modal-go-reports').addEventListener('click', (e) => {
                showTab('reportes');
                document.getElementById('employee-profile-modal').classList.add('hidden');
            });

            const exportRouteTemplate = "{{ route('reports.export', ['format' => 'FORMAT_PLACEHOLDER']) }}";
            const deleteRouteTemplate = "{{ route('reports.delete', ['id' => '__ID__']) }}";

            const periodSelect = document.getElementById('report-period');
            const formatSelect = document.getElementById('report-format');
            const typeSelect = document.getElementById('report-type');

            const buildExportUrl = (fmt, prd, type) => {
                const q = new URLSearchParams({ period: prd || 'month', type: type || 'summary' }).toString();
                return exportRouteTemplate.replace('FORMAT_PLACEHOLDER', fmt || 'pdf') + '?' + q;
            };

            const buildViewUrl = (prd, type) => {
                // Para vista previa, usamos PDF del endpoint público de exportación
                return buildExportUrl('pdf', prd, type);
            };

            const btnGenerate = document.getElementById('btn-generate-report');
            const btnDownload = document.getElementById('btn-download-report');

            if (btnGenerate) {
                btnGenerate.addEventListener('click', () => {
                    const prd = periodSelect ? periodSelect.value : 'month';
                    const type = typeSelect ? typeSelect.value || 'summary' : 'summary';
                    const url = buildViewUrl(prd, type);
                    openPdfPreview(url, 'Vista previa: ' + (typeSelect?.options[typeSelect.selectedIndex]?.text || 'Reporte'), url, 'pdf');
                    showTab('reportes');
                });
            }

            if (btnDownload) {
                btnDownload.addEventListener('click', () => {
                    const prd = periodSelect ? periodSelect.value : 'month';
                    const fmt = formatSelect ? formatSelect.value : 'pdf';
                    const type = typeSelect ? typeSelect.value || 'summary' : 'summary';
                    const url = buildExportUrl(fmt, prd, type);
                    window.location.href = url;
                });
            }

            document.querySelectorAll('.js-card-action').forEach(btn => {
                btn.addEventListener('click', () => {
                    const prd = periodSelect ? periodSelect.value : 'month';
                    const type = btn.dataset.type || 'summary';
                    const fmt = btn.dataset.format || 'pdf';
                    const viewUrl = buildViewUrl(prd, type);
                    const downloadUrl = buildExportUrl(fmt, prd, type);
                    const title = 'Vista previa: ' + (btn.closest('.bg-white')?.querySelector('h4')?.textContent || 'Reporte');
                    const isDepartmentsExcel = (type === 'departments' && fmt === 'excel');
                    if (isDepartmentsExcel) {
                        // Solicitud: Por Departamento debe descargar Excel directo (comportamiento anterior)
                        window.location.href = downloadUrl;
                    } else {
                        openPdfPreview(viewUrl, title, downloadUrl, fmt);
                    }
                });
            });

            document.querySelectorAll('.js-report-view').forEach(btn => {
                btn.addEventListener('click', () => {
                    const prd = btn.dataset.period || 'month';
                    const type = btn.dataset.type || 'summary';
                    const url = buildViewUrl(prd, type);
                    openPdfPreview(url, 'Vista previa: ' + (btn.closest('tr')?.querySelector('td')?.textContent || 'Reporte'), url, 'pdf');
                });
            });

            document.querySelectorAll('.js-report-download').forEach(btn => {
                btn.addEventListener('click', () => {
                    const fmt = btn.dataset.format || 'pdf';
                    const prd = btn.dataset.period || 'month';
                    const type = btn.dataset.type || 'summary';
                    const url = buildExportUrl(fmt, prd, type);
                    window.location.href = url;
                });
            });

            // Eliminar reporte (actualiza sesión y UI)
            const csrf = "{{ csrf_token() }}";
            document.querySelectorAll('.js-report-delete').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const id = btn.dataset.id || btn.closest('tr')?.dataset.id || '';
                    if (!id) return;
                    const row = btn.closest('tr');
                    if (!confirm('¿Eliminar este reporte de la lista?')) return;
                    try {
                        const resp = await fetch(deleteRouteTemplate.replace('__ID__', encodeURIComponent(id)), {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf }
                        });
                        if (!resp.ok) throw new Error('Error ' + resp.status);
                    } catch (err) {
                        console.warn('Fallo eliminando en servidor, removiendo de UI:', err);
                    }
                    if (row) row.remove();
                });
            });
        });
    </script>
</body>
</html>