<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nómina - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">SumAxia</a>
                    <span class="ml-2 text-sm text-gray-500">/ Nómina</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
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
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestión de Nómina</h1>
            <p class="text-gray-600">Administra empleados, salarios y procesos de nómina</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6">
            <nav class="flex space-x-8">
                <a href="#" onclick="showTab('empleados')" id="tab-empleados" class="border-b-2 border-blue-500 text-blue-600 py-2 px-1 text-sm font-medium">
                    Empleados
                </a>
                <a href="#" onclick="showTab('nominas')" id="tab-nominas" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-2 px-1 text-sm font-medium">
                    Nóminas
                </a>
                <a href="#" onclick="showTab('reportes')" id="tab-reportes" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-2 px-1 text-sm font-medium">
                    Reportes
                </a>
            </nav>
        </div>

        <!-- Actions and Filters -->
        <div id="content-empleados" class="tab-content">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>Todos</option>
                                <option>Administración</option>
                                <option>Contabilidad</option>
                                <option>Ventas</option>
                                <option>Recursos Humanos</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>Todos</option>
                                <option>Activo</option>
                                <option>Inactivo</option>
                                <option>Vacaciones</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                            <input type="text" placeholder="Nombre o ID..." class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('payroll.employees.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-plus mr-2"></i>Nuevo Empleado
                        </a>
                        <a href="{{ route('payroll.process') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-calculator mr-2"></i>Procesar Nómina
                        </a>
                    </div>
                </div>
            </div>

            <!-- Employees Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Lista de Empleados</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puesto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
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
                                            @case('active') Activo @break
                                            @case('inactive') Inactivo @break
                                            @case('vacation') Vacaciones @break
                                            @default {{ ucfirst($employee['status']) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3" title="Ver perfil">
                                        <i class="fas fa-user"></i>
                                    </button>
                                    <button class="text-green-600 hover:text-green-900 mr-3" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-purple-600 hover:text-purple-900 mr-3" title="Nómina">
                                        <i class="fas fa-money-bill"></i>
                                    </button>
                                    <button class="text-yellow-600 hover:text-yellow-900 mr-3" title="Reportes">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>Todos</option>
                                <option>Enero 2024</option>
                                <option>Febrero 2024</option>
                                <option>Marzo 2024</option>
                                <option>Abril 2024</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>Todos</option>
                                <option>Borrador</option>
                                <option>Aprobada</option>
                                <option>Pagada</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>Todos</option>
                                <option>Semanal</option>
                                <option>Quincenal</option>
                                <option>Mensual</option>
                                <option>Extraordinaria</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('payroll.process') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-calculator mr-2"></i>Nueva Nómina
                        </a>
<<<<<<< Updated upstream
                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-download mr-2"></i>Exportar
=======
                        <button id="btn-export-payrolls" type="button" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-download mr-2"></i>{{ __('payroll.reports.actions.export') }}
>>>>>>> Stashed changes
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Historial de Nóminas</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleados</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Pago</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
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
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Pagada</span>
                                    @elseif($payroll['status'] === 'approved')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Aprobada</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Borrador</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payroll['payment_date'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-green-600 hover:text-green-900" title="Descargar PDF">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        @if($payroll['status'] === 'draft')
                                        <button class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Eliminar">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Reporte</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>Seleccionar tipo</option>
                                <option>Resumen de Nómina</option>
                                <option>Detalle por Empleado</option>
                                <option>Comparativo Mensual</option>
                                <option>Deducciones e Impuestos</option>
                                <option>Costos por Departamento</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>Último mes</option>
                                <option>Últimos 3 meses</option>
                                <option>Último año</option>
                                <option>Personalizado</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Formato</label>
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option>PDF</option>
                                <option>Excel</option>
                                <option>CSV</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-chart-bar mr-2"></i>Generar Reporte
                        </button>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-download mr-2"></i>Descargar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reports Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">Resumen Mensual</h4>
                        <i class="fas fa-chart-line text-blue-500 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Resumen de nómina del mes actual con comparativo</p>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Pagado:</span>
                            <span class="text-sm font-medium">$416,000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Empleados:</span>
                            <span class="text-sm font-medium">5</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Promedio:</span>
                            <span class="text-sm font-medium">$83,200</span>
                        </div>
                    </div>
                    <button class="w-full mt-4 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded text-sm">
                        Ver Reporte Completo
                    </button>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">Por Departamento</h4>
                        <i class="fas fa-building text-green-500 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Distribución de costos por departamento</p>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Contabilidad:</span>
                            <span class="text-sm font-medium">$146,000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Ventas:</span>
                            <span class="text-sm font-medium">$110,000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Administración:</span>
                            <span class="text-sm font-medium">$76,000</span>
                        </div>
                    </div>
                    <button class="w-full mt-4 bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded text-sm">
                        Ver Desglose
                    </button>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">Deducciones</h4>
                        <i class="fas fa-calculator text-red-500 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Resumen de impuestos y deducciones</p>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">ISR:</span>
                            <span class="text-sm font-medium">$62,400</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">IMSS:</span>
                            <span class="text-sm font-medium">$41,600</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total:</span>
                            <span class="text-sm font-medium">$104,000</span>
                        </div>
                    </div>
                    <button class="w-full mt-4 bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded text-sm">
                        Ver Detalle
                    </button>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Reportes Recientes</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reporte</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formato</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $reports = [
                                    ['name' => 'Resumen de Nómina Abril 2024', 'period' => 'Abril 2024', 'generated' => '15/04/2024 10:30', 'format' => 'PDF'],
                                    ['name' => 'Detalle por Empleado Q1 2024', 'period' => 'Q1 2024', 'generated' => '31/03/2024 16:45', 'format' => 'Excel'],
                                    ['name' => 'Comparativo Mensual', 'period' => 'Ene-Mar 2024', 'generated' => '28/03/2024 09:15', 'format' => 'PDF'],
                                    ['name' => 'Costos por Departamento', 'period' => 'Marzo 2024', 'generated' => '25/03/2024 14:20', 'format' => 'CSV'],
                                ];
                            @endphp
                            @foreach($reports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $report['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report['period'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report['generated'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $report['format'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-green-600 hover:text-green-900" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Eliminar">
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
                        <p class="text-sm font-medium text-gray-600">Total Empleados</p>
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
                        <p class="text-sm font-medium text-gray-600">Empleados Activos</p>
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
                        <p class="text-sm font-medium text-gray-600">En Vacaciones</p>
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
                        <p class="text-sm font-medium text-gray-600">Nómina Mensual</p>
                        <p class="text-2xl font-semibold text-purple-600">${{ number_format(array_sum(array_column($employees, 'salary')), 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll Process Section -->
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Proceso de Nómina</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 border border-gray-200 rounded-lg">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Período Actual</h4>
                    <p class="text-sm text-gray-600">{{ date('F Y') }}</p>
                    <p class="text-xs text-gray-500 mt-1">Del 1 al {{ date('t') }}</p>
                </div>
                
                <div class="text-center p-4 border border-gray-200 rounded-lg">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Estado</h4>
                    <p class="text-sm text-green-600 font-medium">Listo para procesar</p>
                    <p class="text-xs text-gray-500 mt-1">Todos los datos verificados</p>
                </div>
                
                <div class="text-center p-4 border border-gray-200 rounded-lg">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-calculator text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Total a Pagar</h4>
                    <p class="text-sm text-purple-600 font-medium">${{ number_format(array_sum(array_column($employees, 'salary')), 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Incluye deducciones</p>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        // Initialize the first tab as active on page load
        document.addEventListener('DOMContentLoaded', function() {
            showTab('empleados');
<<<<<<< Updated upstream
=======

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
            const btnExportPayrolls = document.getElementById('btn-export-payrolls');

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

            // Exportar desde pestaña "Nóminas" con valores por defecto
            if (btnExportPayrolls) {
                btnExportPayrolls.addEventListener('click', () => {
                    const prd = 'month';
                    const fmt = 'csv';
                    const type = 'summary';
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
>>>>>>> Stashed changes
        });
    </script>
</body>
</html>