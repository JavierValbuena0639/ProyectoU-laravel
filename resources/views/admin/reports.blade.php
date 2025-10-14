<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin.reports_page_title') }} - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">SumAxia</a>
                    <span class="ml-2 text-sm text-gray-500">/ {{ __('common.admin_panel') }} / {{ __('admin.reports_breadcrumb') }}</span>
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
                        <i class="fas fa-home mr-1"></i>{{ __('common.admin_panel') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">{{ __('admin.reports_breadcrumb') }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('admin.reports_title') }}</h1>
            <p class="text-gray-600">{{ __('admin.reports_subtitle') }}</p>
        </div>

        <!-- Report Filters -->
        @php
            use Illuminate\Support\Carbon;
            $admin = auth()->user();
            $domain = $admin ? $admin->emailDomain() : '';
            $period = request('period', 'month');
            $startDateReq = request('start_date');
            $endDateReq = request('end_date');

            // Calcular rango según período
            $now = Carbon::now();
            if ($period === 'today') {
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
            } elseif ($period === 'week') {
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
            } elseif ($period === 'month') {
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
            } elseif ($period === 'quarter') {
                $start = $now->copy()->startOfQuarter();
                $end = $now->copy()->endOfQuarter();
            } elseif ($period === 'year') {
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
            } else { // custom
                $start = $startDateReq ? Carbon::parse($startDateReq)->startOfDay() : $now->copy()->startOfMonth();
                $end = $endDateReq ? Carbon::parse($endDateReq)->endOfDay() : $now->copy()->endOfMonth();
            }

            // Período anterior para cálculo de crecimiento
            $diffDays = $start->diffInDays($end) + 1;
            $prevEnd = $start->copy()->subDay();
            $prevStart = $prevEnd->copy()->subDays($diffDays - 1);

            // Métricas por dominio y período
            $invoicesQuery = \App\Models\Invoice::whereHas('user', function($q) use ($domain){
                $q->where('email', 'like', "%@{$domain}");
            });
            $invoicesCount = (clone $invoicesQuery)->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])->count();
            $revenueSum = (clone $invoicesQuery)->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])->sum('total_amount');
            $prevRevenueSum = (clone $invoicesQuery)->whereBetween('invoice_date', [$prevStart->toDateString(), $prevEnd->toDateString()])->sum('total_amount');
            $growthPct = $prevRevenueSum > 0 ? round((($revenueSum - $prevRevenueSum) / $prevRevenueSum) * 100, 1) : ($revenueSum > 0 ? 100 : 0);

            // Serie de ingresos últimos 6 meses
            $labels = [];
            $series = [];
            for ($i = 5; $i >= 0; $i--) {
                $monthEnd = $end->copy()->subMonths($i)->endOfMonth();
                $monthStart = $monthEnd->copy()->startOfMonth();
                $labels[] = $monthStart->isoFormat('MMM');
                $series[] = (clone $invoicesQuery)->whereBetween('invoice_date', [$monthStart->toDateString(), $monthEnd->toDateString()])->sum('total_amount');
            }

            // Actividad de usuario por módulo (conteos por período)
            $accountingCount = \App\Models\Account::forDomain($domain)->count();
            $invoicingCount = $invoicesCount;
            $payrollCount = \App\Models\Payroll::whereHas('user', function($q) use ($domain){
                    $q->where('email', 'like', "%@{$domain}");
                })
                ->whereBetween('payroll_period_start', [$start->toDateString(), $end->toDateString()])
                ->count();
            $quotesCount = 0; // No hay modelo de cotizaciones en el proyecto
        @endphp

        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-filter mr-2 text-blue-600"></i>{{ __('admin.reports_filters_title') }}
                </h3>
            </div>
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.reports_period') }}</label>
                        @php $selected = request('period', 'month'); @endphp
                        <select name="period" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="today" {{ $selected==='today'?'selected':'' }}>{{ __('admin.reports_period_today') }}</option>
                            <option value="week" {{ $selected==='week'?'selected':'' }}>{{ __('admin.reports_period_week') }}</option>
                            <option value="month" {{ $selected==='month'?'selected':'' }}>{{ __('admin.reports_period_month') }}</option>
                            <option value="quarter" {{ $selected==='quarter'?'selected':'' }}>{{ __('admin.reports_period_quarter') }}</option>
                            <option value="year" {{ $selected==='year'?'selected':'' }}>{{ __('admin.reports_period_year') }}</option>
                            <option value="custom" {{ $selected==='custom'?'selected':'' }}>{{ __('admin.reports_period_custom') }}</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.reports_start_date') }}</label>
                        <input type="date" name="start_date" value="{{ request('start_date', $start->toDateString()) }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.reports_end_date') }}</label>
                        <input type="date" name="end_date" value="{{ request('end_date', $end->toDateString()) }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>{{ __('admin.reports_generate') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-blue-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('admin.reports_active_users') }}</p>
                        @php
                            $activeUsers = \App\Models\User::where('active', true)
                                ->where('email', 'like', "%@{$domain}")
                                ->count();
                        @endphp
                        <p class="text-2xl font-bold text-gray-900">{{ $activeUsers }}</p>
                        <p class="text-sm text-green-600">+12% {{ __('admin.reports_vs_last_month') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-invoice text-green-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('admin.reports_invoices_generated') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $invoicesCount }}</p>
                        <p class="text-sm text-green-600">+8% {{ __('admin.reports_vs_last_month') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-yellow-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('admin.reports_total_revenue') }}</p>
                        <p class="text-2xl font-bold text-gray-900">@money($revenueSum)</p>
                        <p class="text-sm text-green-600">{{ $growthPct }}% {{ __('admin.reports_vs_last_month') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chart-line text-purple-600 text-3xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">{{ __('admin.reports_growth') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $growthPct }}%</p>
                        <p class="text-sm text-green-600">{{ $growthPct >= 0 ? '+' : '' }}{{ $growthPct }}% {{ __('admin.reports_vs_last_month') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-area mr-2 text-blue-600"></i>{{ __('admin.reports_monthly_revenue') }}
                    </h3>
                </div>
                <div class="p-6">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- User Activity Chart -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-pie mr-2 text-green-600"></i>{{ __('admin.reports_user_activity') }}
                    </h3>
                </div>
                <div class="p-6">
                    <canvas id="userActivityChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Reports -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- System Usage Report -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-server mr-2 text-indigo-600"></i>{{ __('admin.reports_system_usage') }}
                    </h3>
                </div>
                <div class="p-6">
                    @php
                        $totalActivity = max(1, $invoicingCount + $accountingCount + $payrollCount + $quotesCount);
                        $invoicingPct = round(($invoicingCount / $totalActivity) * 100);
                        $accountingPct = round(($accountingCount / $totalActivity) * 100);
                        $payrollPct = round(($payrollCount / $totalActivity) * 100);
                        $quotesPct = round(($quotesCount / $totalActivity) * 100);
                    @endphp
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ __('admin.reports_module_invoicing') }}</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $invoicingPct }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $invoicingPct }}%</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ __('admin.reports_module_accounting') }}</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $accountingPct }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $accountingPct }}%</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ __('admin.reports_module_payroll') }}</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $payrollPct }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $payrollPct }}%</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">{{ __('admin.reports_module_quotes') }}</span>
                            <div class="flex items-center">
                                <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $quotesPct }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $quotesPct }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-clock mr-2 text-orange-600"></i>{{ __('admin.reports_recent_activity') }}
                    </h3>
                </div>
                <div class="p-6">
                    @php
                        $admin = Auth::user();
                        $domain = $admin ? $admin->emailDomain() : '';
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
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600 text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $audit->description ?? $audit->event }}</p>
                                        <p class="text-sm text-gray-500">{{ $audit->user->name ?? 'Sistema' }}</p>
                                        <p class="text-xs text-gray-400">{{ $audit->created_at->diffForHumans() }}</p>
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

        <!-- Export Options -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-download mr-2 text-red-600"></i>{{ __('admin.reports_export_title') }}
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php
                        $query = http_build_query([
                            'period' => request('period', 'month'),
                            'start_date' => request('start_date', $start->toDateString()),
                            'end_date' => request('end_date', $end->toDateString())
                        ]);
                    @endphp
                    <a href="{{ route('admin.reports.export', ['format' => 'pdf']) }}?{{ $query }}" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">{{ __('admin.reports_export_pdf') }}</span>
                    </a>
                    
                    <a href="{{ route('admin.reports.export', ['format' => 'excel']) }}?{{ $query }}" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-file-excel text-green-600 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">{{ __('admin.reports_export_excel') }}</span>
                    </a>
                    
                    <a href="{{ route('admin.reports.export', ['format' => 'csv']) }}?{{ $query }}" class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-file-csv text-blue-600 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">{{ __('admin.reports_export_csv') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: '{{ __('admin.chart_revenue') }}',
                    data: @json($series),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // User Activity Chart
        const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
        new Chart(userActivityCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    '{{ __('admin.chart_invoicing') }}',
                    '{{ __('admin.chart_accounting') }}',
                    '{{ __('admin.chart_payroll') }}',
                    '{{ __('admin.chart_quotes') }}'
                ],
                datasets: [{
                    data: [{{ $invoicingCount }}, {{ $accountingCount }}, {{ $payrollCount }}, {{ $quotesCount }}],
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(234, 179, 8)',
                        'rgb(168, 85, 247)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>