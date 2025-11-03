<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin.database_page_title') }} - SumAxia</title>
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
                    <span class="ml-2 text-sm text-gray-500">/ {{ __('common.admin_panel') }} / {{ __('admin.database_breadcrumb') }}</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-home mr-1"></i>{{ __('config.admin_dashboard') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>{{ __('config.logout') }}
                        </button>
                    </form>
                    @php $currentLocale = app()->getLocale(); @endphp
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('locale.switch', ['lang' => 'es']) }}" aria-label="Español" title="Español"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'es' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">🇪🇸</a>
                        <a href="{{ route('locale.switch', ['lang' => 'en']) }}" aria-label="English" title="English"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'en' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">🇺🇸</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @php $isSupport = Auth::user()->isSupport(); @endphp
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-1"></i>{{ __('common.admin_panel') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">{{ __('admin.database_breadcrumb') }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('admin.database_title') }}</h1>
            <p class="text-gray-600">{{ __('admin.database_subtitle') }}</p>
            
            @if(session('success'))
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif
        </div>

        <!-- Database Status -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>{{ __('admin.database_status_title') }}
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-green-800">{{ __('admin.database_status_connection') }}</h4>
                                <p class="text-sm text-green-600">{{ __('admin.database_status_connected') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-database text-blue-600 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800">{{ __('admin.database_status_engine') }}</h4>
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
                                <h4 class="text-sm font-medium text-yellow-800">{{ $isSupport ? __('admin.database_status_db_size_total') : __('admin.database_status_db_size') }}</h4>
                                <p class="text-sm text-yellow-600">{{ $dbSizeHuman ?? '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-shield text-indigo-600 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-indigo-800">{{ __('admin.database_status_admin_emails') }}</h4>
                                <p class="text-sm text-indigo-600">{{ number_format($adminsCount ?? 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-teal-50 border border-teal-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-users text-teal-600 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-teal-800">{{ __('admin.database_status_total_users') }}</h4>
                                <p class="text-sm text-teal-600">{{ number_format($totalUsers ?? 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Operations -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Backup Operations -->
            @if($isSupport)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-download mr-2 text-green-600"></i>{{ __('admin.database_backups_title') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ __('admin.database_backups_create_title') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('admin.database_backups_create_desc') }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.database.backups.create') }}">
                                @csrf
                                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-download mr-1"></i>{{ __('admin.database_backups_create_button') }}
                                </button>
                            </form>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ __('admin.database_backups_auto_title') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('admin.database_backups_auto_desc') }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.database.backups.toggle') }}">
                                @csrf
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enabled" value="1" class="sr-only peer" {{ $autoBackup ? 'checked' : '' }} onchange="this.form.submit()">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="font-medium text-gray-900 mb-3">{{ __('admin.database_backups_recent') }}</h4>
                        <div class="space-y-2">
                            @forelse(($backups ?? []) as $backup)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-archive text-gray-400 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $backup['name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ date('d M Y, H:i', $backup['mtime']) }} • {{ number_format(($backup['size'] ?? 0)/1024, 1) }} KB</p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.database.backups.download', ['file' => $backup['name']]) }}" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.database.backups.delete', ['file' => $backup['name']]) }}">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('{{ __('admin.database_backups_delete_confirm') }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">{{ __('admin.database_backups_none') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Migration Operations -->
            @if($isSupport)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-sync-alt mr-2 text-blue-600"></i>{{ __('admin.database_migrations_title') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ __('admin.database_migrations_run_title') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('admin.database_migrations_run_desc') }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.database.migrate') }}">
                                @csrf
                                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-play mr-1"></i>{{ __('admin.database_migrations_run_button') }}
                                </button>
                            </form>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ __('admin.database_migrations_rollback_title') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('admin.database_migrations_rollback_desc') }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.database.rollback') }}">
                                @csrf
                                <button class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                    <i class="fas fa-undo mr-1"></i>{{ __('admin.database_migrations_rollback_button') }}
                                </button>
                            </form>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ __('admin.database_migrations_optimize_title') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('admin.database_migrations_optimize_desc') }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.database.optimize') }}">
                                @csrf
                                <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                                    <i class="fas fa-magic mr-1"></i>{{ __('admin.database_migrations_optimize_button') }}
                                </button>
                            </form>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                            <div>
                                <h4 class="font-medium text-red-900">{{ __('admin.database_cache_clear_title') }}</h4>
                                <p class="text-sm text-red-600">{{ __('admin.database_cache_clear_desc') }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.database.cache.clear') }}">
                                @csrf
                                <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-broom mr-1"></i>{{ __('admin.database_cache_clear_button') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Database Tables -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-table mr-2 text-indigo-600"></i>{{ __('admin.database_tables_title') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('admin.database_tables_header_table') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('admin.database_tables_header_records') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('admin.database_tables_header_size') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $tables = $tableStats ?? [];
                                    // Helper: extrae el nombre base de la tabla sin esquema (e.g. "public.accounts" -> "accounts")
                                    $getBaseName = function($name) {
                                        $raw = strtolower((string)($name ?? ''));
                                        if ($raw === '') return '';
                                        $parts = explode('.', $raw);
                                        return end($parts);
                                    };

                                    if (!$isSupport) {
                                        $allowed = ['accounts','admin_user_accounts','invoices','transactions'];
                                        $tables = array_values(array_filter($tables, function($x) use ($allowed, $getBaseName) {
                                            $raw = strtolower($x['name'] ?? '');
                                            $base = $getBaseName($raw);
                                            foreach ($allowed as $a) {
                                                if ($base === $a || strpos($raw, $a) !== false) {
                                                    return true;
                                                }
                                            }
                                            return false;
                                        }));
                                    }
                                    // Etiquetas traducibles para tablas conocidas
                                    $labels = [
                                        'accounts' => __('admin.database.table_labels.accounts'),
                                        'admin_user_accounts' => __('admin.database.table_labels.admin_user_accounts'),
                                        'invoices' => __('admin.database.table_labels.invoices'),
                                        'transactions' => __('admin.database.table_labels.transactions'),
                                        'users' => __('admin.database.table_labels.users'),
                                        'migrations' => __('admin.database.table_labels.migrations'),
                                        'jobs' => __('admin.database.table_labels.jobs'),
                                        'failed_jobs' => __('admin.database.table_labels.failed_jobs'),
                                        'password_resets' => __('admin.database.table_labels.password_resets'),
                                        'password_reset_tokens' => __('admin.database.table_labels.password_reset_tokens'),
                                        'personal_access_tokens' => __('admin.database.table_labels.personal_access_tokens'),
                                        'sessions' => __('admin.database.table_labels.sessions'),
                                        'cache' => __('admin.database.table_labels.cache'),
                                        'notifications' => __('admin.database.table_labels.notifications'),
                                        'roles' => __('admin.database.table_labels.roles'),
                                        'permissions' => __('admin.database.table_labels.permissions'),
                                        'model_has_roles' => __('admin.database.table_labels.model_has_roles'),
                                        'model_has_permissions' => __('admin.database.table_labels.model_has_permissions'),
                                        'role_has_permissions' => __('admin.database.table_labels.role_has_permissions'),
                                        'orders' => __('admin.database.table_labels.orders'),
                                        'order_items' => __('admin.database.table_labels.order_items'),
                                        'products' => __('admin.database.table_labels.products'),
                                        'payments' => __('admin.database.table_labels.payments'),
                                        'taxes' => __('admin.database.table_labels.taxes'),
                                        'logs' => __('admin.database.table_labels.logs'),
                                    ];
                                    // Diccionario/fallback bilingüe para palabras comunes
                                    $locale = app()->getLocale();
                                    $dictEs = [
                                        'users'=>'Usuarios','user'=>'Usuario','accounts'=>'Cuentas','account'=>'Cuenta',
                                        'admin'=>'Admin','admins'=>'Administradores','invoices'=>'Facturas','invoice'=>'Factura',
                                        'transactions'=>'Transacciones','transaction'=>'Transacción','migrations'=>'Migraciones',
                                        'jobs'=>'Trabajos','job'=>'Trabajo','failed'=>'Fallidos','password'=>'Contraseña',
                                        'resets'=>'Restablecimientos','reset'=>'Restablecimiento','tokens'=>'Tokens','token'=>'Token',
                                        'personal'=>'Personal','access'=>'Acceso','notifications'=>'Notificaciones','notification'=>'Notificación',
                                        'roles'=>'Roles','role'=>'Rol','permissions'=>'Permisos','permission'=>'Permiso','model'=>'Modelo',
                                        'has'=>'Tiene','cache'=>'Cache','sessions'=>'Sesiones','session'=>'Sesión','orders'=>'Pedidos',
                                        'order'=>'Pedido','items'=>'Ítems','item'=>'Ítem','products'=>'Productos','product'=>'Producto',
                                        'payments'=>'Pagos','payment'=>'Pago','taxes'=>'Impuestos','tax'=>'Impuesto','logs'=>'Registros','log'=>'Registro'
                                    ];
                                    $ignore = ['public','dbo','mysql','information','schema','prefix','bk','prod','dev','test','staging'];
                                    $makeLabel = function($name) use ($dictEs, $ignore, $locale) {
                                        $name = (string) $name;
                                        if ($name === '') return '';
                                        $parts = preg_split('/[._\s-]+/', strtolower($name));
                                        if ($locale === 'es') {
                                            $translated = array_map(function($p) use ($dictEs) {
                                                return $dictEs[$p] ?? ucfirst($p);
                                            }, $parts);
                                        } else {
                                            $translated = array_map(function($p) {
                                                return ucfirst($p);
                                            }, $parts);
                                        }
                                        $translated = array_values(array_filter($translated, function($p) use ($ignore) {
                                            return !in_array(strtolower($p), $ignore, true);
                                        }));
                                        return implode(' ', $translated);
                                    };
                                @endphp
                                @forelse($tables as $t)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $labels[$getBaseName($t['name'] ?? '')] ?? $makeLabel($getBaseName($t['name'] ?? '')) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($t['rows']) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $t['size_human'] ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ __('admin.database_tables_empty') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            

            <!-- Database Configuration -->
            @if($isSupport)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-cogs mr-2 text-orange-600"></i>{{ __('admin.database_config_title') }}
                    </h3>
                </div>
                <div class="p-6">
                    <form class="space-y-4" method="POST" action="{{ route('admin.database.save') }}">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('admin.database_config_engine') }}
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
                                    {{ __('admin.database_config_host') }}
                                </label>
                                <input type="text" name="DB_HOST" value="{{ $config['DB_HOST'] ?? 'localhost' }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('admin.database_config_port') }}
                                </label>
                                <input type="number" name="DB_PORT" value="{{ $config['DB_PORT'] ?? '3306' }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('admin.database_config_name') }}
                            </label>
                            <input type="text" name="DB_DATABASE" value="{{ $config['DB_DATABASE'] ?? '' }}" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('admin.database_config_user') }}
                                </label>
                                <input type="text" name="DB_USERNAME" value="{{ $config['DB_USERNAME'] ?? '' }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('admin.database_config_password') }}
                                </label>
                                <input type="password" name="DB_PASSWORD" value="" placeholder="••••••••" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex space-x-4">
                            <button type="submit" formaction="{{ route('admin.database.test') }}" class="flex-1 bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-plug mr-2"></i>{{ __('admin.database_config_test_button') }}
                            </button>
                            <button type="submit" class="flex-1 bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>{{ __('admin.database_config_save_button') }}
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