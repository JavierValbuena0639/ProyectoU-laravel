<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - SumAxia</title>
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
                    <span class="ml-2 text-sm text-gray-500">/ Administración</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('admin.users') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-users mr-1"></i>Usuarios
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-1"></i>Admin
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('admin.users') }}" class="text-gray-700 hover:text-blue-600">Usuarios</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Editar Usuario</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Editar Usuario</h1>
            <p class="text-gray-600">Actualiza la información del usuario</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <!-- Nombre -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Contraseña (opcional)</label>
                    <input type="password" id="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Dejar en blanco para no cambiar">
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
                    <input type="password" id="password_confirmation" name="password_confirmation" class="mt-2 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Confirmar contraseña">
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rol -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Rol</label>
                    @if($user->isServiceFounder())
                        <div class="mt-1 p-3 bg-gray-100 border border-gray-300 rounded-md">
                            <span class="text-gray-700 font-medium">{{ $user->getRoleName() }}</span>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-lock mr-1"></i>
                                El rol del fundador del servicio no puede ser modificado
                            </p>
                        </div>
                        <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                    @else
                        <select name="role_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('role_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    @if($user->isServiceFounder())
                        <div class="mt-1 p-3 bg-gray-100 border border-gray-300 rounded-md">
                            <span class="text-gray-700 font-medium">{{ $user->active ? 'Activo' : 'Inactivo' }}</span>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-lock mr-1"></i>
                                El estado del fundador del servicio no puede ser modificado
                            </p>
                        </div>
                        <input type="hidden" name="status" value="{{ $user->active ? 'active' : 'inactive' }}">
                    @else
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="active" {{ old('status', $user->active ? 'active' : 'inactive') === 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('status', $user->active ? 'active' : 'inactive') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    @endif
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.users') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function(){
            const pw = document.getElementById('password');
            const pwc = document.getElementById('password_confirmation');
            if (!pw) return;
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
                const hasValue = p && p.length > 0;
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
                pw.setCustomValidity(!hasValue || ok ? '' : 'La contraseña no cumple los requisitos');
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
</body>
</html>