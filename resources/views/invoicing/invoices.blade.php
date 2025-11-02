<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('invoicing.page_title') }}</title>
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
                    <span class="ml-2 text-sm text-gray-500">/ {{ __('invoicing.breadcrumb') }}</span>
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
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('invoicing.title') }}</h1>
            <p class="text-gray-600">{{ __('invoicing.subtitle') }}</p>
        </div>

        <!-- Filters and Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('invoicing.filters.status') }}</label>
                        <select class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option>{{ __('invoicing.filters.all') }}</option>
                            <option>{{ __('invoicing.status_labels.pending') }}</option>
                            <option>{{ __('invoicing.status_labels.paid') }}</option>
                            <option>{{ __('invoicing.status_labels.overdue') }}</option>
                            <option>{{ __('invoicing.status_labels.cancelled') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('invoicing.filters.client') }}</label>
                        <input type="text" placeholder="{{ __('invoicing.filters.search_client_placeholder') }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('invoicing.filters.date') }}</label>
                        <input type="date" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                    </div>
                    <div class="flex items-end">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-search mr-1"></i>{{ __('invoicing.filters.filter') }}
                        </button>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('invoicing.invoices.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-plus mr-2"></i>{{ __('invoicing.actions.new_invoice') }}
                    </a>
                    <a href="{{ route('invoicing.quotes.create') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-file-alt mr-2"></i>{{ __('invoicing.actions.new_quote') }}
                    </a>
                    <a href="{{ route('invoicing.invoices.export.csv') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm" title="Exportar CSV">
                        <i class="fas fa-download mr-2"></i>Exportar CSV
                    </a>
                    <a href="{{ route('invoicing.invoices.export.csv') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm" title="Exportar CSV">
                        <i class="fas fa-download mr-2"></i>Exportar CSV
                    </a>
                    <a href="{{ route('invoicing.invoices.export.csv') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm" title="Exportar CSV">
                        <i class="fas fa-download mr-2"></i>Exportar CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('invoicing.table.list_title') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('invoicing.table.headers.number') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('invoicing.table.headers.client') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('invoicing.table.headers.date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('invoicing.table.headers.due') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('invoicing.table.headers.total') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('invoicing.table.headers.status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('invoicing.table.headers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $domain = auth()->user()->emailDomain();
                            $invoices = \App\Models\Invoice::whereHas('user', function($q) use ($domain) {
                                $q->where('email_domain', $domain);
                            })
                            ->latest()
                            ->take(15)
                            ->get();
                        @endphp
                        @foreach($invoices as $invoice)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                {{ $invoice->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $invoice->client_name }}</div>
                                <div class="text-sm text-gray-500">{{ $invoice->client_email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $invoice->invoice_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $invoice->due_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                @money($invoice->total_amount)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($invoice->status)
                                        @case('paid') bg-green-100 text-green-800 @break
                                        @case('pending') bg-yellow-100 text-yellow-800 @break
                                        @case('overdue') bg-red-100 text-red-800 @break
                                        @case('cancelled') bg-gray-100 text-gray-800 @break
                                        @default bg-blue-100 text-blue-800
                                    @endswitch">
                                    @switch($invoice->status)
                                        @case('paid') {{ __('invoicing.status_labels.paid') }} @break
                                        @case('pending') {{ __('invoicing.status_labels.pending') }} @break
                                        @case('overdue') {{ __('invoicing.status_labels.overdue') }} @break
                                        @case('cancelled') {{ __('invoicing.status_labels.cancelled') }} @break
                                        @default {{ ucfirst($invoice->status) }}
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-blue-600 hover:text-blue-900 mr-3" title="{{ __('invoicing.actions.view') }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900 mr-3" title="{{ __('invoicing.actions.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-purple-600 hover:text-purple-900 mr-3" title="{{ __('invoicing.actions.pdf') }}">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-900 mr-3" title="{{ __('invoicing.actions.send') }}">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                <form action="{{ route('admin.fe.invoice.send', ['invoice' => $invoice->id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-700 hover:text-blue-900 mr-3" title="{{ __('invoicing.actions.send_to_dian') }}">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </button>
                                </form>
                                <button class="text-red-600 hover:text-red-900" title="{{ __('invoicing.actions.delete') }}">
                                    <i class="fas fa-trash"></i>
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
                        <i class="fas fa-file-invoice text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('invoicing.summary.total_invoices') }}</p>
                        @php $domain = $domain ?? auth()->user()->emailDomain(); @endphp
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Invoice::whereHas('user', function($q) use ($domain){ $q->where('email_domain', $domain); })->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('invoicing.summary.paid_invoices') }}</p>
                        <p class="text-2xl font-semibold text-green-600">{{ \App\Models\Invoice::where('status', 'paid')->whereHas('user', function($q) use ($domain){ $q->where('email_domain', $domain); })->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('invoicing.summary.pending') }}</p>
                        <p class="text-2xl font-semibold text-yellow-600">{{ \App\Models\Invoice::where('status', 'pending')->whereHas('user', function($q) use ($domain){ $q->where('email_domain', $domain); })->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('invoicing.summary.overdue') }}</p>
                        <p class="text-2xl font-semibold text-red-600">{{ \App\Models\Invoice::where('status', 'overdue')->whereHas('user', function($q) use ($domain){ $q->where('email_domain', $domain); })->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart Placeholder -->
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('invoicing.chart.revenue_title') }}</h3>
            <div class="h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">{{ __('invoicing.chart.monthly_income_chart') }}</p>
                    <p class="text-sm text-gray-400">{{ __('invoicing.chart.chartjs_pending') }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>