<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">SumAxia</a>
                    <span class="ml-2 text-sm text-gray-500">/ Administración / Crear Usuario</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-home mr-1"></i>Panel de Administración
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>{{ __('common.logout') }}
                        </button>
                    </form>
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
                        <span class="text-gray-500">Crear Usuario</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Crear Nuevo Usuario</h1>
            <p class="text-gray-600">Completa la información para crear un nuevo usuario del sistema</p>
            <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-700">
                Dominio esperado: <strong id="expected-domain">{{ $expectedDomain ?? 'dominio' }}</strong>
                <div class="mt-2 text-xs">El correo del usuario debe pertenecer a este dominio para habilitar el envío del código de verificación por email.</div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('admin.users.store') }}" method="POST" class="p-6">
                @csrf
                
                <!-- Información Personal -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user mr-2 text-blue-600"></i>Información Personal
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre Completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Ingrese el nombre completo">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Correo Electrónico <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="usuario@ejemplo.com">
                            <p class="mt-2 text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded p-2">Se enviará un código de verificación al correo ingresado si el dominio coincide.</p>
                        </div>
                    </div>
                </div>

                <!-- Credenciales -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-key mr-2 text-blue-600"></i>Credenciales de Acceso
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Mínimo 8 caracteres, mayúsculas, minúsculas, número y símbolo">
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
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Repita la contraseña">
                        </div>
                    </div>
                </div>

                <!-- Rol y Permisos -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user-shield mr-2 text-blue-600"></i>Rol y Permisos
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Rol del Usuario <span class="text-red-500">*</span>
                            </label>
                            <select id="role_id" name="role_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione un rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Estado del Usuario
                            </label>
                            <select id="status" name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>Información Adicional
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono
                            </label>
                            <input type="tel" id="phone" name="phone"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="+52 123 456 7890">
                        </div>
                        
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                Departamento
                            </label>
                            <select id="department" name="department"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccione un departamento</option>
                                <option value="contabilidad">Contabilidad</option>
                                <option value="administracion">Administración</option>
                                <option value="recursos_humanos">Recursos Humanos</option>
                                <option value="sistemas">Sistemas</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.users') }}" 
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validación de dominio con popup al enviar el formulario
        (function() {
            const form = document.querySelector('form[action="{{ route('admin.users.store') }}"]');
            const emailInput = document.getElementById('email');
            const expectedDomain = document.getElementById('expected-domain').textContent.trim();

            form.addEventListener('submit', function(e) {
                const emailVal = (emailInput.value || '').trim();
                const parts = emailVal.split('@');
                const domain = parts.length > 1 ? parts[1] : '';

                if (domain && expectedDomain && domain.toLowerCase() !== expectedDomain.toLowerCase()) {
                    e.preventDefault();
                    alert('El dominio del correo (' + domain + ') no coincide con el esperado (' + expectedDomain + '). Por favor, corrige el email.');
                    emailInput.focus();
                }
            });
        })();

        // Validación de contraseñas
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
                // también verificar confirmación al cambiar
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

            // inicializar estado
            checkPassword(pw.value || '');
        })();
    </script>
</body>
</html>