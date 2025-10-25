<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SumAxia - {{ __('auth.title_login') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <!-- Logo y título -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-calculator text-white text-2xl"></i>
            </div>
            <a href="{{ url('/') }}" class="text-3xl font-bold text-gray-900 mb-2 inline-block">SumAxia</a>
            <p class="text-gray-600">{{ __('auth.subtitle_login') }}</p>
        </div>

        <!-- Formulario de login -->
        <div class="bg-white rounded-lg shadow-xl p-8">
            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-700 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            <form id="login_form" method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>{{ __('auth.email') }}
                    </label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="tu@email.com"
                        value="{{ old('email') }}"
                        inputmode="email"
                        pattern="^\S+$"
                        oninput="this.value = this.value.replace(/\s/g, '')"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>{{ __('auth.password') }}
                    </label>
                    <div class="relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            class="w-full pr-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="••••••••"
                            inputmode="text"
                            pattern="^\S+$"
                            oninput="this.value = this.value.replace(/\s/g, '')"
                        >
                        <button 
                            type="button"
                            id="toggle_pw"
                            aria-label="Mostrar u ocultar contraseña"
                            aria-controls="password"
                            class="absolute inset-y-0 right-2 flex items-center px-1 text-gray-500 hover:text-gray-700"
                        >
                            <i id="toggle_pw_icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember me + Forgot -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            {{ __('auth.remember') }}
                        </label>
                    </div>
                    <button type="button" id="forgot_pw_btn" class="text-sm text-blue-600 hover:text-blue-500">
                        ¿Olvidaste tu contraseña?
                    </button>
                </div>

                <!-- Submit button -->
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    {{ __('auth.login') }}
                </button>
            </form>

            <!-- Registro enlace -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">{{ __('auth.no_account') }} <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500">{{ __('auth.create_admin') }}</a></p>
            </div>
            
            <form id="forgot_pw_form" method="POST" action="{{ route('password.email') }}" class="hidden">
                @csrf
                <input type="hidden" name="email" value="">
            </form>

            <!-- Usuarios demo -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center mb-3">Usuarios de prueba:</p>
                <div class="space-y-2 text-xs text-gray-600">
                    <div class="bg-gray-50 p-2 rounded">
                        <strong>Admin:</strong> admin@sumaxia.com / admin123
                    </div>
                    <div class="bg-gray-50 p-2 rounded">
                        <strong>Usuario:</strong> user@sumaxia.com / user123
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500">
            <p>{{ __('common.copyright', ['year' => date('Y'), 'company' => 'SumAxia']) }}</p>
        </div>

        <!-- Idioma -->
        <div class="text-center mt-4">
            <a href="{{ route('locale.switch', ['lang' => 'es']) }}" class="px-3 py-1 bg-gray-200 rounded">ES</a>
            <a href="{{ route('locale.switch', ['lang' => 'en']) }}" class="px-3 py-1 bg-gray-200 rounded">US</a>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            var btn = document.getElementById('toggle_pw');
            var input = document.getElementById('password');
            var icon = document.getElementById('toggle_pw_icon');
            if(btn && input){
                btn.addEventListener('click', function(){
                    var isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    if(icon){
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                });
            }

            var forgotBtn = document.getElementById('forgot_pw_btn');
            var forgotForm = document.getElementById('forgot_pw_form');
            var emailInput = document.getElementById('email');
            if (forgotBtn && forgotForm && emailInput) {
                forgotBtn.addEventListener('click', function(){
                    var val = (emailInput.value || '').trim();
                    if (!val) {
                        alert('Por favor ingresa tu correo en el campo de email.');
                        return;
                    }
                    // Validación de formato de email en cliente
                    if (!emailInput.checkValidity()) {
                        alert('Por favor ingresa un correo electrónico válido.');
                        return;
                    }
                    forgotForm.querySelector('input[name="email"]').value = val;
                    forgotForm.submit();
                });
            }

            // Persistencia del email con Remember Me
            var loginForm = document.getElementById('login_form');
            var rememberChk = document.getElementById('remember');
            var emailInput = document.getElementById('email');

            try {
                var savedRemember = localStorage.getItem('remember_me_checked') === 'true';
                var savedEmail = localStorage.getItem('remembered_email') || '';
                if (savedRemember && savedEmail && emailInput) {
                    emailInput.value = savedEmail;
                    if (rememberChk) rememberChk.checked = true;
                }
            } catch (e) { /* ignore storage errors */ }

            if (loginForm) {
                loginForm.addEventListener('submit', function(){
                    if (rememberChk && rememberChk.checked && emailInput) {
                        try {
                            localStorage.setItem('remember_me_checked', 'true');
                            localStorage.setItem('remembered_email', (emailInput.value || '').trim());
                        } catch (e) { /* ignore storage errors */ }
                    } else {
                        try {
                            localStorage.removeItem('remember_me_checked');
                            localStorage.removeItem('remembered_email');
                        } catch (e) { /* ignore storage errors */ }
                    }
                    // Extra: sanitizar espacios por si el navegador ignora pattern
                    if (emailInput) emailInput.value = (emailInput.value || '').replace(/\s/g, '');
                    var pwInput = document.getElementById('password');
                    if (pwInput) pwInput.value = (pwInput.value || '').replace(/\s/g, '');
                });
            }
        });
    </script>
</body>
</html>