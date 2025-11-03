<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin.security_logs_page_title') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body class="bg-gray-100">
    @include('partials.alerts')
    <!-- Header navigation -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold text-blue-600">SumAxia</a>
                    <span class="text-sm text-gray-500">/ Admin / {{ __('admin.security_logs_breadcrumb') }}</span>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-home mr-1"></i>{{ __('admin.security_logs_back_to_panel') }}
                    </a>
                    @php $currentLocale = app()->getLocale(); @endphp
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
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.security_logs_title') }}</h1>
            <p class="text-gray-600">{{ __('admin.security_logs_subtitle') }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.security_logs_filters_event') }}</label>
                    <select name="event" class="mt-1 block w-full rounded border-gray-300">
                        <option value="">{{ __('admin.security_logs_filters_all') }}</option>
                        @foreach($events as $ev)
                            <option value="{{ $ev }}" @selected(request('event')===$ev)>{{ $ev }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.security_logs_filters_user_email') }}</label>
                    <input type="text" name="user" value="{{ request('user') }}" class="mt-1 block w-full rounded border-gray-300" placeholder="usuario@dominio" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.security_logs_filters_ip') }}</label>
                    <input type="text" name="ip" value="{{ request('ip') }}" class="mt-1 block w-full rounded border-gray-300" placeholder="127.0.0.1" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.security_logs_filters_start_date') }}</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('admin.security_logs_filters_end_date') }}</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded border-gray-300" />
                </div>
                <div class="md:col-span-5 flex items-center gap-3 mt-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"><i class="fas fa-filter mr-2"></i>{{ __('admin.security_logs_filter_button') }}</button>
                    <a href="{{ route('admin.security.logs.export.csv', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"><i class="fas fa-file-csv mr-2"></i>{{ __('admin.security_logs_export_csv') }}</a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('admin.security_logs_results') }}</h2>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">{{ __('admin.security_logs_table_date') }}</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">{{ __('admin.security_logs_table_event') }}</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">{{ __('admin.security_logs_table_description') }}</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">{{ __('admin.security_logs_table_user') }}</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">{{ __('admin.security_logs_table_ip') }}</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">{{ __('admin.security_logs_table_url') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                        <tr>
                            <td class="px-4 py-2 text-gray-600">{{ $log->created_at->toDateTimeString() }}</td>
                            <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">{{ $log->event }}</span></td>
                            <td class="px-4 py-2 text-gray-800">{{ $log->description }}</td>
                            <td class="px-4 py-2 text-gray-800">{{ optional($log->user)->email ?? 'Sistema' }}</td>
                            <td class="px-4 py-2 text-gray-800">{{ $log->ip_address }}</td>
                            <td class="px-4 py-2 text-gray-800 break-all">{{ $log->url }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">{{ __('admin.security_logs_no_results') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</body>
</html>