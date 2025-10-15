<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('common.home') }} - SumAxia</title>
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
                    <h1 class="text-2xl font-bold text-blue-600">SumAxia</h1>
                    <span class="ml-2 text-sm text-gray-500">{{ __('common.home') }}</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">
                        {{ __('messages.welcome') }}, {{ Auth::user()->name }}
                        <span class="text-xs text-gray-500">({{ Auth::user()->getRoleName() }})</span>
                    </span>
                    @php $currentLocale = app()->getLocale(); @endphp
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('locale.switch', ['lang' => 'es']) }}" aria-label="Español" title="Español"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'es' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">🇪🇸</a>
                        <a href="{{ route('locale.switch', ['lang' => 'en']) }}" aria-label="English" title="English"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'en' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">🇺🇸</a>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            {{ __('common.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ __('common.home') }}</h2>
            <p class="text-gray-600">Resumen general de tu sistema contable</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Cuentas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-list-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Cuentas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Account::count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Transacciones -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-exchange-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Transacciones</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Transaction::count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Facturas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-file-invoice text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Facturas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Invoice::count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Proveedores -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-truck text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Proveedores</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Supplier::count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Acciones Rápidas</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('accounting.transactions.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fas fa-plus-circle text-blue-600 text-xl mr-3"></i>
                            <span class="text-sm font-medium text-blue-700">Nueva Transacción</span>
                        </a>
                        
                        <a href="{{ route('invoicing.invoices') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <i class="fas fa-file-invoice text-green-600 text-xl mr-3"></i>
                            <span class="text-sm font-medium text-green-700">Nueva Factura</span>
                        </a>
                        
                        <a href="{{ route('accounting.accounts') }}" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                            <i class="fas fa-list-alt text-yellow-600 text-xl mr-3"></i>
                            <span class="text-sm font-medium text-yellow-700">Plan de Cuentas</span>
                        </a>
                        
                        <a href="{{ route('payroll.index') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <i class="fas fa-users text-purple-600 text-xl mr-3"></i>
                            <span class="text-sm font-medium text-purple-700">Nómina</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Actividad Reciente</h3>
                </div>
                <div class="p-6">
                    @php
                        $domain = Auth::user()->emailDomain();
                        $recentAudits = \App\Models\Audit::with('user')
                            ->whereHas('user', function($q) use ($domain) {
                                $q->where('email', 'like', '%@' . $domain);
                            })
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($recentAudits->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentAudits as $audit)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-900">
                                            <span class="font-medium">{{ $audit->user->name ?? 'Sistema' }}</span>
                                            {{ $audit->description ?? $audit->event }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $audit->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No hay actividad reciente</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>