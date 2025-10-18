<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación del Sistema - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
    <style>
        .status-ok { color: #16a34a; }
        .status-warn { color: #f59e0b; }
        .status-err { color: #dc2626; }
    </style>
}</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">SumAxia</a>
                    <span class="ml-2 text-sm text-gray-500">/ Administración / Verificación</span>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Verificación del Sistema</h1>
            <p class="text-gray-600">Resumen de estado de componentes y configuración crítica</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-sm font-medium text-gray-700">Laravel</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $checks['app_version'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-sm font-medium text-gray-700">PHP</h4>
                <p class="text-lg font-semibold text-gray-900">{{ $checks['php_version'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-sm font-medium text-gray-700">Entorno</h4>
                <p class="text-lg font-semibold text-gray-900">{{ ucfirst($checks['environment']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-puzzle-piece mr-2 text-indigo-600"></i>Extensiones de PHP
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($extensions as $ext => $loaded)
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-{{ $loaded ? 'check-circle status-ok' : 'times-circle status-err' }} text-xl mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ strtoupper($ext) }}</p>
                            <p class="text-xs text-gray-500">{{ $loaded ? 'Cargada' : 'Falta' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-database mr-2 text-blue-600"></i>Base de Datos & Cache
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-{{ $dbOk ? 'plug status-ok' : 'exclamation-triangle status-err' }} text-xl mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Conexión a BD</p>
                        <p class="text-xs text-gray-500">{{ $dbOk ? 'OK' : ('Error: ' . $dbError) }}</p>
                    </div>
                </div>
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <i class="fas fa-{{ $cacheOk ? 'hourglass-half status-ok' : 'exclamation-triangle status-err' }} text-xl mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Cache</p>
                        <p class="text-xs text-gray-500">{{ $cacheOk ? 'OK' : 'No accesible' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-hdd mr-2 text-yellow-600"></i>Almacenamiento y Respaldos
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-{{ $checks['storage_writable'] ? 'check-circle status-ok' : 'times-circle status-err' }} text-xl mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Storage writable</p>
                            <p class="text-xs text-gray-500">{{ $checks['storage_writable'] ? 'OK' : 'Permisos insuficientes' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-{{ $checks['backup_dir_exists'] ? 'check-circle status-ok' : 'times-circle status-err' }} text-xl mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Directorio de respaldos</p>
                            <p class="text-xs text-gray-500">{{ $checks['backup_dir_exists'] ? 'Existe' : 'No existe' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-{{ $checks['backup_dir_writable'] ? 'check-circle status-ok' : 'times-circle status-err' }} text-xl mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Respaldos writable</p>
                            <p class="text-xs text-gray-500">{{ $checks['backup_dir_writable'] ? 'OK' : 'Permisos insuficientes' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-{{ $firstInitCompleted ? 'check-circle status-ok' : 'exclamation-triangle status-warn' }} text-xl mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Primer arranque completado</p>
                            <p class="text-xs text-gray-500">{{ $firstInitCompleted ? 'Sí (marcador encontrado)' : 'No (se ejecutará en el próximo arranque si DB_AUTO_BACKUP=true)' }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Respaldos disponibles</h4>
                    <div class="space-y-2">
                        @forelse($backupFiles as $file)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-file-archive text-gray-400 mr-3"></i>
                                    <p class="text-sm font-medium text-gray-900">{{ $file }}</p>
                                </div>
                                <a href="{{ route('admin.database.backups.download', ['file' => $file]) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No hay respaldos generados aún.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-cogs mr-2 text-orange-600"></i>Configuración relevante
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($config as $k => $v)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm font-medium text-gray-900">{{ $k }}</p>
                        <p class="text-xs text-gray-600 break-all">{{ is_bool($v) ? ($v ? 'true' : 'false') : ($v ?: '—') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow mt-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-clipboard-list mr-2 text-green-600"></i>Eventos de Auditoría Recientes
                </h3>
            </div>
            <div class="p-6 space-y-2">
                @forelse($auditEntries as $line)
                    <div class="p-3 bg-gray-50 rounded border border-gray-200 text-xs text-gray-700 break-all">{{ $line }}</div>
                @empty
                    <p class="text-sm text-gray-500">No hay entradas de auditoría disponibles.</p>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>