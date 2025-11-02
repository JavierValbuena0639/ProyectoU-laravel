<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <!-- Logo y título (igual que /login) -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-calculator text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">SumAxia</h2>
            <p class="text-gray-600">{{ __('auth.subtitle_login') }}</p>
        </div>

        <!-- Tarjeta de bienvenida, siguiendo el estilo de /login -->
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="space-y-4">
                <p class="text-gray-700">
                    {{ __('welcome.intro') }}
                </p>

                <!-- Acciones principales -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="w-full inline-flex justify-center items-center gap-2 py-2 px-4 rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow">
                            <i class="fas fa-user-plus"></i>
                            {{ __('welcome.create_account') }}
                        </a>
                    @endif
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="w-full inline-flex justify-center items-center gap-2 py-2 px-4 rounded-md text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200">
                            <i class="fas fa-sign-in-alt"></i>
                            {{ __('auth.login') }}
                        </a>
                    @endif
                </div>

                <!-- Características resumidas -->
                <ul class="mt-6 space-y-2 text-sm text-gray-700">
                    <li><i class="fas fa-users text-blue-600 mr-2"></i>{{ __('welcome.feature_users') }}</li>
                    <li><i class="fas fa-file-invoice-dollar text-blue-600 mr-2"></i>{{ __('welcome.feature_billing') }}</li>
                    <li><i class="fas fa-book text-blue-600 mr-2"></i>{{ __('welcome.feature_accounting') }}</li>
                    <li><i class="fas fa-briefcase text-blue-600 mr-2"></i>{{ __('welcome.feature_payroll') }}</li>
                    <li><i class="fas fa-percentage text-blue-600 mr-2"></i>{{ __('welcome.feature_taxes') }}</li>
                    <li><i class="fas fa-chart-line text-blue-600 mr-2"></i>{{ __('welcome.feature_reports') }}</li>
                    <li><i class="fas fa-shield-alt text-blue-600 mr-2"></i>{{ __('welcome.feature_security') }}</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500">
            <p>{{ __('common.copyright', ['year' => date('Y'), 'company' => 'SumAxia']) }}</p>
        </div>

        <!-- Idioma (igual que /login) -->
        <div class="text-center mt-4">
            <a href="{{ route('locale.switch', ['lang' => 'es']) }}" class="px-3 py-1 bg-gray-200 rounded">ES</a>
            <a href="{{ route('locale.switch', ['lang' => 'en']) }}" class="px-3 py-1 bg-gray-200 rounded">US</a>
        </div>
    </div>
</body>
</html>