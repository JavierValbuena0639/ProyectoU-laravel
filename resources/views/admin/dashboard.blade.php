<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.admin_panel') }} - SumAxia</title>
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
                    <span class="ml-2 text-sm text-gray-500">/ {{ __('common.admin_panel') }}</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-home mr-1"></i>{{ __('common.home') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>{{ __('common.logout') }}
                        </button>
                    </form>
                    <!-- Language Switcher -->
                    @php
                        $currentLocale = app()->getLocale();
                    @endphp
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('locale.switch', ['lang' => 'es']) }}" aria-label="Español" title="Español"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'es' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">
                            🇪🇸
                        </a>
                        <a href="{{ route('locale.switch', ['lang' => 'en']) }}" aria-label="English" title="English"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'en' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">
                            🇺🇸
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('common.admin_panel') }}</h1>
            <p class="text-gray-600">{{ __('config.subheader') }}</p>
        </div>

        <!-- Admin Alert -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-shield-alt text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">{{ __('admin.admin_access_title') }}</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>{{ __('admin.admin_access_message') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('admin.users') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.quick_actions_users_title') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('admin.quick_actions_users_desc') }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.config') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-cog text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.quick_actions_config_title') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('admin.quick_actions_config_desc') }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.database') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-database text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.quick_actions_database_title') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('admin.quick_actions_database_desc') }}</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.reports') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.quick_actions_reports_title') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('admin.quick_actions_reports_desc') }}</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- System Statistics -->
        @php $domain = Auth::user()->emailDomain(); @endphp
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin.stats_total_users') }}</p>
                        @php
                            // Usuarios del mismo dominio
                            $totalUsers = \App\Models\User::where('email', 'like', "%@{$domain}")->count();
                        @endphp
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-file-invoice text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin.stats_invoices') }}</p>
                        
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Invoice::whereHas('user', function($q) use ($domain){ $q->where('email', 'like', "%@{$domain}"); })->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-building text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin.stats_accounts') }}</p>
                        
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Account::forDomain($domain)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-history text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin.stats_audits') }}</p>
                        
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Audit::whereHas('user', function($q) use ($domain){ $q->where('email', 'like', "%@{$domain}"); })->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.recent_activity_title') }}</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @php
                        $admin = Auth::user();
                        $domain = $admin ? $admin->emailDomain() : '';
                        $recentAudits = \App\Models\Audit::with('user')
                            ->whereHas('user', function($q) use ($domain) {
                                $q->where('email', 'like', '%@' . $domain);
                            })
                            ->latest()
                            ->take(10)
                            ->get();
                    @endphp
                    @foreach($recentAudits as $audit)
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $audit->user->name ?? 'Sistema' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $audit->description }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-sm text-gray-500">
                            {{ $audit->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin.system_health_title') }}</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ __('admin.system_health_db') }}</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>Conectado
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ __('admin.system_health_web') }}</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>Activo
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ __('admin.system_health_auth') }}</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-check mr-1"></i>Funcionando
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin.system_info_title') }}</h4>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">{{ __('admin.system_info_laravel') }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ app()->version() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">{{ __('admin.system_info_php') }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">{{ __('admin.system_info_env') }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ app()->environment() }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin.quick_actions_title') }}</h4>
                <div class="space-y-3">
                    <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-sync mr-2"></i>{{ __('admin.quick_actions_clear_cache') }}
                    </button>
                    <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-download mr-2"></i>{{ __('admin.quick_actions_export_data') }}
                    </button>
                    <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-shield-alt mr-2"></i>{{ __('admin.quick_actions_security_logs') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>