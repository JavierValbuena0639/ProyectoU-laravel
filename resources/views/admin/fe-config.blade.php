<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin.fe_config_title') }} - SumAxia</title>
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
                    <span class="ml-2 text-sm text-gray-500">/ Admin / FE</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-home mr-1"></i>{{ __('config.admin_dashboard') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>{{ __('common.logout') }}
                        </button>
                    </form>
                    <div class="flex space-x-2">
                        <a href="{{ route('locale.switch', ['lang' => 'es']) }}" class="px-3 py-1 border rounded">ES</a>
                        <a href="{{ route('locale.switch', ['lang' => 'en']) }}" class="px-3 py-1 border rounded">US</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-800 rounded">
                {{ $errors->first() }}
            </div>
        @endif
        @if(session('warning'))
            <div class="mb-6 p-4 bg-yellow-100 text-yellow-800 rounded">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Software credentials (read-only from config) -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-key text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.fe_software_section') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('admin.fe_config_desc') }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600">{{ __('admin.fe_software_id') }}</label>
                    <input type="text" class="mt-1 w-full border rounded px-3 py-2 bg-gray-100" value="{{ $feConfig['software_id'] }}" readonly>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">{{ __('admin.fe_software_pin') }}</label>
                    <input type="text" class="mt-1 w-full border rounded px-3 py-2 bg-gray-100" value="{{ $feConfig['software_pin'] ? '********' : '' }}" readonly>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">{{ __('admin.fe_cert_path') }}</label>
                    <input type="text" class="mt-1 w-full border rounded px-3 py-2 bg-gray-100" value="{{ $feConfig['cert_path'] }}" readonly>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">{{ __('admin.fe_cert_password') }}</label>
                    <input type="text" class="mt-1 w-full border rounded px-3 py-2 bg-gray-100" value="{{ $feConfig['cert_password'] ? '********' : '' }}" readonly>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">{{ __('admin.fe_environment') }}</label>
                    <input type="text" class="mt-1 w-full border rounded px-3 py-2 bg-gray-100" value="{{ $feConfig['environment'] }}" readonly>
                </div>
            </div>
        </div>

        <!-- Resolution form -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-file-signature text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.fe_resolution_section') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('admin.fe_resolution_desc') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.fe.config.save') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600">{{ __('admin.fe_prefix') }}</label>
                        <input type="text" name="prefix" class="mt-1 w-full border rounded px-3 py-2" value="{{ old('prefix', optional($resolution)->prefix) }}">
                        @error('prefix')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">{{ __('admin.fe_number_from') }}</label>
                        <input type="number" name="number_from" class="mt-1 w-full border rounded px-3 py-2" value="{{ old('number_from', optional($resolution)->number_from) }}">
                        @error('number_from')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">{{ __('admin.fe_number_to') }}</label>
                        <input type="number" name="number_to" class="mt-1 w-full border rounded px-3 py-2" value="{{ old('number_to', optional($resolution)->number_to) }}">
                        @error('number_to')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">{{ __('admin.fe_start_date') }}</label>
                        <input type="date" name="start_date" class="mt-1 w-full border rounded px-3 py-2" value="{{ old('start_date', optional($resolution)->start_date ? optional($resolution)->start_date->format('Y-m-d') : '') }}">
                        @error('start_date')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600">{{ __('admin.fe_end_date') }}</label>
                        <input type="date" name="end_date" class="mt-1 w-full border rounded px-3 py-2" value="{{ old('end_date', optional($resolution)->end_date ? optional($resolution)->end_date->format('Y-m-d') : '') }}">
                        @error('end_date')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>{{ __('admin.fe_save_button') }}
                    </button>
                    <form method="POST" action="{{ route('admin.fe.test') }}" class="inline ml-3">
                        @csrf
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                            <i class="fas fa-vial mr-2"></i> Probar habilitación (sandbox)
                        </button>
                    </form>
                </div>
            </form>
        </div>
    </div>
</body>
</html>