<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar correo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-inter min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <a href="/" class="text-xl font-semibold text-blue-700">SumAxia</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="text-sm text-gray-600 hover:text-gray-900">Cerrar sesión</button>
            </form>
        </div>
        <h1 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
            <i class="fas fa-shield-alt text-blue-600 text-xl mr-2" aria-hidden="true"></i>
            Verificación de correo
        </h1>
        <p class="text-sm text-gray-600 mb-4">Ingresa el código de 6 dígitos que te enviamos por correo para activar tu cuenta.</p>

        @if(session('status'))
            <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('auth.verify.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Código de verificación
                </label>
                <input id="code" name="code" type="text" maxlength="6" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="000000" value="{{ old('code') }}">
                @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-between items-center">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Verificar</button>
                <span class="text-xs text-gray-500">¿No recibiste el código? Revisa spam o solicita reenvío.</span>
            </div>
        </form>
    </div>
</body>
</html>