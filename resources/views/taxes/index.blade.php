<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Impuestos - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
</head>
<body class="bg-gray-50">
    @include('partials.alerts')
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">SumAxia</a>
                    <span class="ml-2 text-sm text-gray-500">/ Impuestos</span>
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
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestión de Impuestos</h1>
            <p class="text-gray-600">Administra obligaciones fiscales, declaraciones y reportes tributarios</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6">
            <nav class="flex space-x-8">
                <a href="#" class="border-b-2 border-blue-500 text-blue-600 py-2 px-1 text-sm font-medium">
                    Declaraciones
                </a>
                <a href="#" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-2 px-1 text-sm font-medium">
                    Calendario Fiscal
                </a>
                <a href="#" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-2 px-1 text-sm font-medium">
                    Reportes
                </a>
            </nav>
        </div>

        <!-- Tax Calendar Alert -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Próximas Obligaciones Fiscales</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>IVA Mensual - Vence el {{ date('d/m/Y', strtotime('last day of this month')) }}</li>
                            <li>Retenciones en la Fuente - Vence el 15 de {{ date('F') }}</li>
                            <li>Declaración de Renta - Vence el 31 de Octubre</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions and Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Impuesto</label>
                        <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option>Todos</option>
                            <option>IVA</option>
                            <option>Retención en la Fuente</option>
                            <option>Industria y Comercio</option>
                            <option>Renta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                        <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option>{{ date('Y') }}</option>
                            <option>{{ date('Y') - 1 }}</option>
                            <option>{{ date('Y') - 2 }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option>Todos</option>
                            <option>Pendiente</option>
                            <option>Presentado</option>
                            <option>Pagado</option>
                            <option>Vencido</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-plus mr-2"></i>Nueva Declaración
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-download mr-2"></i>Exportar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tax Declarations Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Declaraciones Tributarias</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Período</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Límite</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Gravable</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Impuesto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $declarations = [
                                ['type' => 'IVA', 'period' => date('m/Y'), 'due_date' => date('d/m/Y', strtotime('last day of this month')), 'base' => 125000, 'tax' => 23750, 'status' => 'pending'],
                                ['type' => 'Retención Fuente', 'period' => date('m/Y'), 'due_date' => '15/'.date('m/Y'), 'base' => 85000, 'tax' => 8500, 'status' => 'pending'],
                                ['type' => 'ICA', 'period' => date('m/Y'), 'due_date' => '15/'.date('m/Y', strtotime('+1 month')), 'base' => 200000, 'tax' => 1400, 'status' => 'draft'],
                                ['type' => 'IVA', 'period' => date('m/Y', strtotime('-1 month')), 'due_date' => date('d/m/Y', strtotime('last day of last month')), 'base' => 98000, 'tax' => 18620, 'status' => 'paid'],
                                ['type' => 'Retención Fuente', 'period' => date('m/Y', strtotime('-1 month')), 'due_date' => '15/'.date('m/Y', strtotime('-1 month')), 'base' => 75000, 'tax' => 7500, 'status' => 'filed'],
                                ['type' => 'Renta', 'period' => (date('Y')-1), 'due_date' => '31/10/'.date('Y'), 'base' => 450000, 'tax' => 67500, 'status' => 'pending'],
                            ];
                        @endphp
                        @foreach($declarations as $declaration)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-full 
                                        @switch($declaration['type'])
                                            @case('IVA') bg-blue-100 text-blue-600 @break
                                            @case('Retención Fuente') bg-green-100 text-green-600 @break
                                            @case('ICA') bg-purple-100 text-purple-600 @break
                                            @case('Renta') bg-red-100 text-red-600 @break
                                            @default bg-gray-100 text-gray-600
                                        @endswitch">
                                        <i class="fas fa-file-invoice text-sm"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $declaration['type'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $declaration['period'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $declaration['due_date'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($declaration['base'], 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${{ number_format($declaration['tax'], 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($declaration['status'])
                                        @case('paid') bg-green-100 text-green-800 @break
                                        @case('filed') bg-blue-100 text-blue-800 @break
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('draft') bg-gray-100 text-gray-800 @break
                                        @case('overdue') bg-red-100 text-red-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    @switch($declaration['status'])
                                        @case('paid') Pagado @break
                                        @case('filed') Presentado @break
                                        @case('pending') Pendiente @break
                                        @case('draft') Borrador @break
                                        @case('overdue') Vencido @break
                                        @default {{ ucfirst($declaration['status']) }}
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900 mr-3" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900 mr-3" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-purple-600 hover:text-purple-900 mr-3" title="Generar PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-900 mr-3" title="Presentar">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Declaraciones</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ count($declarations) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pendientes</p>
                        <p class="text-2xl font-semibold text-yellow-600">{{ count(array_filter($declarations, fn($d) => $d['status'] === 'pending')) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pagados</p>
                        <p class="text-2xl font-semibold text-green-600">{{ count(array_filter($declarations, fn($d) => $d['status'] === 'paid')) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-dollar-sign text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Impuestos</p>
                        <p class="text-2xl font-semibold text-red-600">@money(array_sum(array_column($declarations, 'tax')))</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Calendar -->
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Calendario Fiscal {{ date('Y') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Mensual</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex justify-between">
                            <span>IVA</span>
                            <span class="text-gray-500">Último día del mes</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Retención Fuente</span>
                            <span class="text-gray-500">15 de cada mes</span>
                        </li>
                        <li class="flex justify-between">
                            <span>ICA</span>
                            <span class="text-gray-500">15 del mes siguiente</span>
                        </li>
                    </ul>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Bimestral</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex justify-between">
                            <span>ICA Grandes Contribuyentes</span>
                            <span class="text-gray-500">Cada 2 meses</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Información Exógena</span>
                            <span class="text-gray-500">Marzo y Julio</span>
                        </li>
                    </ul>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Anual</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex justify-between">
                            <span>Declaración de Renta</span>
                            <span class="text-gray-500">Octubre</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Medios Magnéticos</span>
                            <span class="text-gray-500">Abril</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Certificados Tributarios</span>
                            <span class="text-gray-500">Marzo</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>