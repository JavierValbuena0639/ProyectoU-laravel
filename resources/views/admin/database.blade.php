<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Base de Datos - SumAxia</title>
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
                    <span class="ml-2 text-sm text-gray-500">/ Administración / Base de Datos</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-home mr-1"></i>Admin Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>Cerrar Sesión
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
                        <span class="text-gray-500">Base de Datos</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestión de Base de Datos</h1>
            <p class="text-gray-600">Administra respaldos, migraciones y mantenimiento de la base de datos</p>
        </div>

        <!-- Database Status -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>Estado de la Base de Datos
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-green-800">Estado de Conexión</h4>
                                <p class="text-sm text-green-600">Conectado</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-database text-blue-600 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800">Motor de BD</h4>
                                <p class="text-sm text-blue-600">{{ $engine ?? strtoupper(env('DB_CONNECTION')) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-hdd text-yellow-600 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-yellow-800">Tamaño de BD</h4>
                                <p class="text-sm text-yellow-600">2.4 MB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Operations -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @php($support = Auth::user()->isSupport())
            <!-- Backup Operations -->
            @if($support)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-download mr-2 text-green-600"></i>Respaldos de Base de Datos
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">Crear Respaldo Manual</h4>
                                <p class="text-sm text-gray-600">Genera un respaldo completo de la base de datos</p>
                            </div>
                            <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-download mr-1"></i>Crear
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">Respaldo Automático</h4>
                                <p class="text-sm text-gray-600">Configurar respaldos automáticos diarios</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="font-medium text-gray-900 mb-3">Respaldos Recientes</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-file-archive text-gray-400 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">backup_2024_01_15.sql</p>
                                        <p class="text-xs text-gray-500">15 Enero 2024, 10:30 AM</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-file-archive text-gray-400 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">backup_2024_01_14.sql</p>
                                        <p class="text-xs text-gray-500">14 Enero 2024, 10:30 AM</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Migration Operations -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-sync-alt mr-2 text-blue-600"></i>Migraciones y Mantenimiento
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">Ejecutar Migraciones</h4>
                                <p class="text-sm text-gray-600">Aplicar migraciones pendientes</p>
                            </div>
                            <form method="POST" action="{{ route('admin.database.test') }}">
                                @csrf
                                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-play mr-1"></i>Ejecutar
                                </button>
                            </form>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">Rollback Migraciones</h4>
                                <p class="text-sm text-gray-600">Revertir última migración</p>
                            </div>
                            <button class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-undo mr-1"></i>Revertir
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">Optimizar Base de Datos</h4>
                                <p class="text-sm text-gray-600">Optimizar tablas y índices</p>
                            </div>
                            <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-magic mr-1"></i>Optimizar
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                            <div>
                                <h4 class="font-medium text-red-900">Limpiar Cache</h4>
                                <p class="text-sm text-red-600">Limpiar cache de consultas y configuración</p>
                            </div>
                            <form method="POST" action="{{ route('admin.database.test') }}">
                                @csrf
                                <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-broom mr-1"></i>Limpiar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-shield-alt mr-2 text-gray-600"></i>Acceso limitado
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-700">Tu rol actual no permite ejecutar operaciones de respaldo, migraciones ni mantenimiento. Puedes consultar el estado y las tablas de la base de datos.</p>
                        </div>
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800"><i class="fas fa-info-circle mr-2"></i>Si necesitas realizar estas operaciones, inicia sesión con un usuario de <strong>soporte interno</strong> o solicita permisos.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Database Tables -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-table mr-2 text-indigo-600"></i>Tablas de la Base de Datos
                    </h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tabla
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Registros
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tamaño
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">users</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">5</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">12 KB</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">invoices</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">23</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">45 KB</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">quotes</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">18</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">32 KB</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">accounts</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">15</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">8 KB</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">employees</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">12</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">28 KB</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Database Configuration -->
            @if($support)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-cogs mr-2 text-orange-600"></i>Configuración de Conexión
                    </h3>
                </div>
                <div class="p-6">
                    <form class="space-y-4" method="POST" action="{{ route('admin.database.save') }}">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Motor de Base de Datos
                            </label>
                            <select name="DB_CONNECTION" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="mysql" {{ ($config['DB_CONNECTION'] ?? '') === 'mysql' ? 'selected' : '' }}>MySQL</option>
                                <option value="sqlite" {{ ($config['DB_CONNECTION'] ?? '') === 'sqlite' ? 'selected' : '' }}>SQLite</option>
                                <option value="pgsql" {{ ($config['DB_CONNECTION'] ?? '') === 'pgsql' ? 'selected' : '' }}>PostgreSQL</option>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Host
                                </label>
                                <input type="text" name="DB_HOST" value="{{ $config['DB_HOST'] ?? 'localhost' }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Puerto
                                </label>
                                <input type="number" name="DB_PORT" value="{{ $config['DB_PORT'] ?? '3306' }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Base de Datos
                            </label>
                            <input type="text" name="DB_DATABASE" value="{{ $config['DB_DATABASE'] ?? '' }}" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Usuario
                                </label>
                                <input type="text" name="DB_USERNAME" value="{{ $config['DB_USERNAME'] ?? '' }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Contraseña
                                </label>
                                <input type="password" name="DB_PASSWORD" value="" placeholder="••••••••" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex space-x-4">
                            <button type="submit" formaction="{{ route('admin.database.test') }}" class="flex-1 bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-plug mr-2"></i>Probar Conexión
                            </button>
                            <button type="submit" class="flex-1 bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>