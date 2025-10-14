<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SumAxia - {{ __('auth.title_register') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>body{background:linear-gradient(135deg,#f0f7ff,#e9ecff)}</style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-green-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-user-shield text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">SumAxia</h2>
            <p class="text-gray-600">{{ __('auth.title_register') }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-xl p-8">
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>{{ __('auth.name') }}
                    </label>
                    <input id="name" name="name" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="{{ old('name') }}">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>{{ __('auth.email') }}
                    </label>
                    <input id="email" name="email" type="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="{{ old('email') }}">
                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded text-xs text-blue-700">
                        Usa tu correo con el nombre del dominio de tu empresa para que podamos enviar un código de verificación por email.
                    </div>
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>{{ __('auth.password') }}
                    </label>
                    <input id="password" name="password" type="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Mínimo 8 caracteres, mayúsculas, minúsculas, número y símbolo">
                    <div class="mt-2 text-xs text-gray-600" id="pw-requirements">
                        <p class="font-medium">Requisitos:</p>
                        <div class="grid grid-cols-2 gap-x-3 gap-y-2">
                            <div><span id="req-length" class="inline-block px-3 py-1.5 rounded bg-gray-100">8+ caracteres</span></div>
                            <div><span id="req-lower" class="inline-block px-3 py-1.5 rounded bg-gray-100">minúscula</span></div>
                            <div><span id="req-upper" class="inline-block px-3 py-1.5 rounded bg-gray-100">mayúscula</span></div>
                            <div><span id="req-number" class="inline-block px-3 py-1.5 rounded bg-gray-100">número</span></div>
                            <div><span id="req-symbol" class="inline-block px-3 py-1.5 rounded bg-gray-100">símbolo</span></div>
                        </div>
                    </div>
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>{{ __('auth.confirm_password') }}
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg">
                    {{ __('auth.register') }}
                </button>
            </form>

            <script>
                (function(){
                    const pw = document.getElementById('password');
                    const pwc = document.getElementById('password_confirmation');
                    const reqs = {
                        length: document.getElementById('req-length'),
                        lower: document.getElementById('req-lower'),
                        upper: document.getElementById('req-upper'),
                        number: document.getElementById('req-number'),
                        symbol: document.getElementById('req-symbol')
                    };

                    function setBadge(el, ok){
                        if (!el) return;
                        const label = el.dataset.label || el.textContent;
                        el.dataset.label = label;
                        el.textContent = (ok ? '✔ ' : '✖ ') + label;
                        el.className = 'inline-block px-3 py-1.5 rounded ' + (ok ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');
                    }

                    function checkPassword(p){
                        const length = p.length >= 8;
                        const lower = /[a-z]/.test(p);
                        const upper = /[A-Z]/.test(p);
                        const number = /[0-9]/.test(p);
                        const symbol = /[^A-Za-z0-9]/.test(p);
                        setBadge(reqs.length, length);
                        setBadge(reqs.lower, lower);
                        setBadge(reqs.upper, upper);
                        setBadge(reqs.number, number);
                        setBadge(reqs.symbol, symbol);
                        const ok = length && lower && upper && number && symbol;
                        pw.setCustomValidity(ok ? '' : 'La contraseña no cumple los requisitos');
                    }

                    pw.addEventListener('input', function(){
                        checkPassword(pw.value);
                        if (pwc.value && pw.value !== pwc.value) {
                            pwc.setCustomValidity('Las contraseñas no coinciden');
                        } else {
                            pwc.setCustomValidity('');
                        }
                    });

                    pwc.addEventListener('input', function(){
                        if (pw.value !== pwc.value) {
                            this.setCustomValidity('Las contraseñas no coinciden');
                        } else {
                            this.setCustomValidity('');
                        }
                    });

                    checkPassword(pw.value || '');
                })();
            </script>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500">{{ __('auth.back_login') }}</a>
            </div>
        </div>

        <div class="text-center text-sm text-gray-500">
            <p>&copy; 2024 SumAxia</p>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('locale.switch', ['lang' => 'es']) }}" class="px-3 py-1 bg-gray-200 rounded">ES</a>
            <a href="{{ route('locale.switch', ['lang' => 'en']) }}" class="px-3 py-1 bg-gray-200 rounded">US</a>
        </div>
    </div>
</body>
</html>