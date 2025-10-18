<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Transacción - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">SumAxia</h1>
                    <span class="ml-4 text-gray-500">|</span>
                    <h2 class="ml-4 text-lg font-semibold text-gray-700">Nueva Transacción</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Bienvenido, {{ Auth::user()->name ?? 'Usuario' }}</span>
                    @php $currentLocale = app()->getLocale(); @endphp
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('locale.switch', ['lang' => 'es']) }}" aria-label="Español" title="Español"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'es' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">🇪🇸</a>
                        <a href="{{ route('locale.switch', ['lang' => 'en']) }}" aria-label="English" title="English"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'en' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">🇺🇸</a>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                            <i class="fas fa-sign-out-alt mr-1"></i>Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('accounting.transactions') }}" class="text-gray-700 hover:text-blue-600">Transacciones</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Nueva Transacción</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Transaction Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                    Crear Nueva Transacción
                </h3>
            </div>
            
            <form method="POST" action="{{ route('accounting.transactions.store') }}" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fecha -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-1"></i>Fecha
                        </label>
                        <input type="date" id="date" name="date" value="{{ date('Y-m-d') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>

                    <!-- Referencia -->
                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-hashtag mr-1"></i>Referencia
                        </label>
                        <input type="text" id="reference" name="reference" placeholder="Ej: TRX-001" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-1"></i>Descripción
                        </label>
                        <textarea id="description" name="description" rows="3" placeholder="Descripción de la transacción" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required></textarea>
                    </div>

                    <!-- Cuenta -->
                    <div>
                        <label for="account" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-list mr-1"></i>Cuenta
                        </label>
                        <select id="account" name="account" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Seleccionar cuenta...</option>
                            <option value="1100">1100 - Caja</option>
                            <option value="1200">1200 - Bancos</option>
                            <option value="1300">1300 - Cuentas por Cobrar</option>
                            <option value="2100">2100 - Cuentas por Pagar</option>
                            <option value="3100">3100 - Capital</option>
                            <option value="4100">4100 - Ingresos por Ventas</option>
                            <option value="5100">5100 - Gastos Operativos</option>
                        </select>
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-exchange-alt mr-1"></i>Tipo
                        </label>
                        <select id="type" name="type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="debit">Débito</option>
                            <option value="credit">Crédito</option>
                        </select>
                    </div>

                    <!-- Monto -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-dollar-sign mr-1"></i>Monto
                        </label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0" placeholder="0.00" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-check-circle mr-1"></i>Estado
                        </label>
                        <select id="status" name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="pending">Pendiente</option>
                            <option value="completed" selected>Completada</option>
                            <option value="cancelled">Cancelada</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('accounting.transactions') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class="fas fa-save mr-2"></i>Guardar Transacción
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Ayuda</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Débito:</strong> Aumenta activos o disminuye pasivos/patrimonio</li>
                            <li><strong>Crédito:</strong> Disminuye activos o aumenta pasivos/patrimonio</li>
                            <li><strong>Referencia:</strong> Código único para identificar la transacción</li>
                            <li>Todos los campos son obligatorios para crear la transacción</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>