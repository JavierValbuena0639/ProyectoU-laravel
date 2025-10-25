<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="max-w-3xl mx-auto p-6">
        <nav class="mb-6 text-sm text-gray-600">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600"><i class="fas fa-home mr-1"></i>Admin</a>
            <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
            <a href="{{ route('admin.users') }}" class="hover:text-blue-600">Usuarios</a>
            <i class="fas fa-chevron-right mx-2 text-gray-400"></i>
            <span>2FA</span>
        </nav>

        <div class="bg-white shadow rounded p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-shield-alt text-blue-600 text-2xl mr-3"></i>
                <h1 class="text-2xl font-bold">Autenticación de Dos Factores</h1>
            </div>

            @if(session('success'))
                <div class="p-3 mb-4 bg-green-100 border border-green-200 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @error('code')
                <div class="p-3 mb-4 bg-red-100 border border-red-200 text-red-800 rounded">{{ $message }}</div>
            @enderror

            <p class="text-gray-700 mb-4">Usuario: <strong>{{ $user->name }}</strong> ({{ $user->email }})</p>

            @if(!$user->two_factor_enabled)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div>
                        <p class="text-gray-700 mb-2">Escanea este QR en Google Authenticator o Authy:</p>
                        <img src="{{ $qrUrl }}" alt="QR 2FA" class="border rounded" />
                        <p class="text-xs text-gray-500 mt-2">Si no puedes escanear, usa el código secreto: <code class="bg-gray-100 px-2 py-1 rounded">{{ $user->two_factor_secret }}</code></p>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('admin.users.2fa.verify', $user) }}">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700 mb-1">Código de 6 dígitos</label>
                            <input type="text" name="code" maxlength="6" pattern="\d{6}" required class="w-full border rounded px-3 py-2" placeholder="123456">
                            <button type="submit" class="mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                <i class="fas fa-check mr-2"></i>Activar 2FA
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="p-3 bg-blue-50 border border-blue-200 rounded text-blue-800 mb-4">
                    2FA está <strong>activado</strong> para este usuario desde {{ $user->two_factor_confirmed_at ? $user->two_factor_confirmed_at->format('Y-m-d H:i') : 'desconocido' }}.
                </div>
                <form method="POST" action="{{ route('admin.users.2fa.disable', $user) }}">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        <i class="fas fa-times mr-2"></i>Desactivar 2FA
                    </button>
                </form>
            @endif

            <div class="mt-6">
                <a href="{{ route('admin.users') }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left mr-1"></i>Volver a usuarios</a>
            </div>
        </div>
    </div>
</body>
</html>