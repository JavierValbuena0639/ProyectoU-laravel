<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.admin_panel') }} - SumAxia</title>
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
                    <span class="ml-2 text-sm text-gray-500">/ Administración / Roles</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-home mr-1"></i>{{ __('common.admin_panel') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>{{ __('common.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-1"></i>Admin
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Roles</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestión de Roles</h1>
            <p class="text-gray-600">Administra los roles y permisos del sistema</p>
        </div>

        <!-- Action Buttons -->
        <div class="mb-6">
            <a href="{{ route('admin.roles.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                <i class="fas fa-plus mr-2"></i>Nuevo Rol
            </a>
        </div>

        <!-- Roles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Administrador -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-crown text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Administrador</h3>
                            <p class="text-sm text-gray-600">Acceso completo al sistema</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Gestión de usuarios
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Configuración del sistema
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Acceso a reportes
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Gestión de base de datos
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-users mr-1"></i>
                        {{ \App\Models\User::where('role_id', 1)->count() }} usuarios asignados
                    </p>
                </div>
            </div>

            <!-- Contador -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-calculator text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Contador</h3>
                            <p class="text-sm text-gray-600">Acceso a módulos contables</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Gestión de cuentas
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Facturación
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Reportes contables
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-times text-red-500 mr-2"></i>
                        Gestión de usuarios
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-users mr-1"></i>
                        {{ \App\Models\User::where('role_id', 2)->count() }} usuarios asignados
                    </p>
                </div>
            </div>

            <!-- Usuario -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-user text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Usuario</h3>
                            <p class="text-sm text-gray-600">Acceso básico al sistema</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Ver dashboard
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        Consultar información
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-times text-red-500 mr-2"></i>
                        Crear registros
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-times text-red-500 mr-2"></i>
                        Acceso administrativo
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-users mr-1"></i>
                        {{ \App\Models\User::where('role_id', 3)->count() }} usuarios asignados
                    </p>
                </div>
            </div>
        </div>

        <!-- Permissions Matrix -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Matriz de Permisos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permiso</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Administrador</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Contador</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Gestión de Usuarios</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-times text-red-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-times text-red-500"></i>
                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Configuración del Sistema</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-times text-red-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-times text-red-500"></i>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Gestión Contable</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-times text-red-500"></i>
                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Facturación</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-times text-red-500"></i>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Nómina</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-times text-red-500"></i>
                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Reportes</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-check text-green-500"></i>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <i class="fas fa-eye text-yellow-500"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>