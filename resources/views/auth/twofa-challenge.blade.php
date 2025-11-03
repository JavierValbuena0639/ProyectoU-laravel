<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SumAxia - Desafío 2FA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    @include('partials.alerts')
    <div class="max-w-md w-full space-y-8 p-8">
        <!-- Encabezado -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
            <a href="{{ url('/') }}" class="text-3xl font-bold text-gray-900 mb-2 inline-block">SumAxia</a>
            <p class="text-gray-600">Autenticación de Dos Factores</p>
        </div>

        <!-- Tarjeta -->
        <div class="bg-white rounded-lg shadow-xl p-8">
            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-blue-50 border border-blue-200 text-blue-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <p class="text-sm text-gray-700 mb-4">
                Usuario: <strong>{{ $user->name }}</strong> ({{ $user->email }})
            </p>

            <form method="POST" action="{{ route('auth.twofa.submit') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2"></i>Código de 6 dígitos
                    </label>
                    <input 
                        id="code" 
                        name="code" 
                        type="text" 
                        inputmode="numeric"
                        maxlength="6"
                        pattern="\d{6}"
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="123456"
                        value="{{ old('code') }}"
                    >
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button 
                    type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                >
                    <i class="fas fa-check mr-2"></i>Confirmar acceso
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-500"><i class="fas fa-arrow-left mr-1"></i>Volver al login</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500">
            <p>{{ __('common.copyright', ['year' => date('Y'), 'company' => 'SumAxia']) }}</p>
        </div>
    </div>
</body>
</html>