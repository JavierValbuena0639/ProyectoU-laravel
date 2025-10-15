<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('config.title') }} - SumAxia</title>
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
                    <span class="ml-2 text-sm text-gray-500">/ {{ __('config.breadcrumb_admin') }} / {{ __('config.breadcrumb_config') }}</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-home mr-1"></i>{{ __('config.admin_dashboard') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>{{ __('config.logout') }}
                        </button>
                    </form>
                    <!-- Language Switcher -->
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

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                        <span class="text-gray-500">{{ __('config.breadcrumb_config') }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('config.header') }}</h1>
            <p class="text-gray-600">{{ __('config.subheader') }}</p>
        </div>

        <!-- Configuration Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- General Settings -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-cog mr-2 text-blue-600"></i>{{ __('config.general') }}
                    </h3>
                </div>
                <div class="p-6">
                    <form class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Empresa
                            </label>
                            <input type="text" value="SumAxia" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                RFC de la Empresa
                            </label>
                            <input type="text" placeholder="ABC123456789" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Dirección
                            </label>
                            <textarea rows="3" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Dirección completa de la empresa"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Moneda por Defecto
                            </label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="COP">Peso Colombiano (COP)</option>
                                <option value="USD">Dólar Americano (USD)</option>
                                <option value="EUR">Euro (EUR)</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>{{ __('config.save') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- System Settings -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-server mr-2 text-green-600"></i>{{ __('config.system') }}
                    </h3>
                </div>
                <div class="p-6">
                    <form class="space-y-6" method="POST" action="{{ route('admin.config.save') }}">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('config.region') }}
                            </label>
                            <select id="region" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="CO" selected>Colombia (CO)</option>
                                <option value="USA">Estados Unidos (USA)</option>
                                <option value="Europe">Europa</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Selecciona una región para listar zonas/ubicaciones.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('config.timezone') }}
                            </label>
                            <select id="timezone" name="timezone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></select>
                            <p class="text-xs text-gray-500 mt-1">Lista dinámica por estados/países/departamentos según la región.</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('config.date_format') }}
                            </label>
                            <select id="date_format" name="date_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="d/m/Y">DD/MM/YYYY</option>
                                <option value="m/d/Y">MM/DD/YYYY</option>
                                <option value="Y-m-d">YYYY-MM-DD</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('config.language') }}
                            </label>
                            <select id="language" name="language" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="es" selected>Español</option>
                                <option value="en">English</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">{{ __('config.note_language_global') }}</p>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="maintenance_mode" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="maintenance_mode" class="ml-2 block text-sm text-gray-900">
                                {{ __('config.maintenance') }}
                            </label>
                        </div>
                        
                        <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>{{ __('config.apply') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Email Settings -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-envelope mr-2 text-purple-600"></i>{{ __('config.email') }}
                    </h3>
                </div>
                <div class="p-6">
                    <form class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Servidor SMTP
                            </label>
                            <input type="text" placeholder="smtp.gmail.com" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Puerto
                                </label>
                                <input type="number" value="587" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Encriptación
                                </label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Usuario SMTP
                            </label>
                            <input type="email" placeholder="usuario@empresa.com" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Contraseña SMTP
                            </label>
                            <input type="password" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="flex space-x-4">
                            <button type="button" class="flex-1 bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>Probar Conexión
                            </button>
                            <button type="submit" class="flex-1 bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Scripts para lógica dinámica de región/zonas/formato/idioma -->
            <script>
                // Datos de zonas por región: optgroups y opciones {label, tz}
                const zonesData = {
                    'USA': {
                        'Nueva York (EST/EDT)': [
                            { label: 'Nueva York - NYC', tz: 'America/New_York' },
                            { label: 'Florida - Miami', tz: 'America/New_York' }
                        ],
                        'Centro (CST/CDT)': [
                            { label: 'Texas - Dallas', tz: 'America/Chicago' },
                            { label: 'Illinois - Chicago', tz: 'America/Chicago' }
                        ],
                        'Montaña (MST/MDT)': [
                            { label: 'Colorado - Denver', tz: 'America/Denver' },
                            { label: 'Arizona - Phoenix', tz: 'America/Phoenix' }
                        ],
                        'Pacífico (PST/PDT)': [
                            { label: 'California - Los Ángeles', tz: 'America/Los_Angeles' },
                            { label: 'Washington - Seattle', tz: 'America/Los_Angeles' }
                        ],
                        'Otros': [
                            { label: 'Alaska - Anchorage', tz: 'America/Anchorage' },
                            { label: 'Hawái - Honolulu', tz: 'Pacific/Honolulu' }
                        ]
                    },
                    'Europe': {
                        'España (CET/CEST)': [ { label: 'Madrid', tz: 'Europe/Madrid' }, { label: 'Barcelona', tz: 'Europe/Madrid' } ],
                        'Francia (CET/CEST)': [ { label: 'París', tz: 'Europe/Paris' } ],
                        'Alemania (CET/CEST)': [ { label: 'Berlín', tz: 'Europe/Berlin' } ],
                        'Italia (CET/CEST)': [ { label: 'Roma', tz: 'Europe/Rome' } ],
                        'Reino Unido (GMT/BST)': [ { label: 'Londres', tz: 'Europe/London' } ]
                    },
                    'CO': {
                        'Cundinamarca': [ { label: 'Bogotá', tz: 'America/Bogota' } ],
                        'Antioquia': [ { label: 'Medellín', tz: 'America/Bogota' } ],
                        'Valle del Cauca': [ { label: 'Cali', tz: 'America/Bogota' } ],
                        'Atlántico': [ { label: 'Barranquilla', tz: 'America/Bogota' } ],
                        'Santander': [ { label: 'Bucaramanga', tz: 'America/Bogota' } ]
                    }
                };

                const regionEl = document.getElementById('region');
                const tzEl = document.getElementById('timezone');
                const dateEl = document.getElementById('date_format');
                const langEl = document.getElementById('language');

                function renderTimezones(region) {
                    tzEl.innerHTML = '';
                    const groups = zonesData[region] || {};
                    for (const groupLabel in groups) {
                        const optGroup = document.createElement('optgroup');
                        optGroup.label = groupLabel;
                        groups[groupLabel].forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.tz;
                            opt.textContent = `${item.label} (${item.tz})`;
                            optGroup.appendChild(opt);
                        });
                        tzEl.appendChild(optGroup);
                    }
                    // Selección por defecto
                    if (region === 'USA') {
                        dateEl.value = 'm/d/Y';
                        tzEl.value = 'America/New_York';
                    } else if (region === 'Europe') {
                        dateEl.value = 'd/m/Y';
                        tzEl.value = 'Europe/Madrid';
                    } else {
                        dateEl.value = 'd/m/Y';
                        tzEl.value = 'America/Bogota';
                    }
                }

                // Cambio de idioma: demo de etiquetas (efecto visual inmediato)
                function applyLanguageLabels(lang) {
                    const header = document.querySelector('h3 i.fa-server').parentElement;
                    if (!header) return;
                    if (lang === 'en') {
                        header.innerHTML = '<i class="fas fa-server mr-2 text-green-600"></i>System Configuration';
                    } else {
                        header.innerHTML = '<i class="fas fa-server mr-2 text-green-600"></i>Configuración del Sistema';
                    }
                }

                // Inicializar con región CO
                renderTimezones(regionEl.value);
                applyLanguageLabels(langEl.value);

                regionEl.addEventListener('change', (e) => {
                    renderTimezones(e.target.value);
                });
                langEl.addEventListener('change', (e) => {
                    applyLanguageLabels(e.target.value);
                });
            </script>

            <!-- Security Settings -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-shield-alt mr-2 text-red-600"></i>Configuración de Seguridad
                    </h3>
                </div>
                <div class="p-6">
                    <form class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tiempo de Sesión (minutos)
                            </label>
                            <input type="number" value="120" min="30" max="480"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Intentos de Login Máximos
                            </label>
                            <input type="number" value="5" min="3" max="10"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" id="force_https" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="force_https" class="ml-2 block text-sm text-gray-900">
                                    Forzar HTTPS
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="two_factor" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="two_factor" class="ml-2 block text-sm text-gray-900">
                                    Autenticación de Dos Factores
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="audit_log" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="audit_log" class="ml-2 block text-sm text-gray-900">
                                    Registro de Auditoría
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-shield-alt mr-2"></i>Actualizar Seguridad
                        </button>
                    </form>
                </div>
            </div>

            <!-- Danger Zone: Unsubscribe Service -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-ban mr-2 text-red-600"></i>Baja del Servicio
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Esta acción desactivará todas las cuentas y usuarios asociados al dominio de su servicio
                        (<span class="font-semibold">{{ Auth::user()->emailDomain() }}</span>) y cerrará su sesión.
                    </p>
                    <form id="unsubscribeForm" method="POST" action="{{ route('admin.config.unsubscribe') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="confirm_domain" id="unsubscribe_confirm_domain" value="">
                        <input type="hidden" name="confirm_word" id="unsubscribe_confirm_word" value="">
                        <button type="button" id="unsubscribeBtn" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-power-off mr-2"></i>Dar de baja el servicio
                        </button>
                        <p class="text-xs text-red-700 bg-red-50 border border-red-200 rounded p-3">
                            Advertencia: Esta acción es irreversible desde la interfaz. Para reactivar, contacte al soporte.
                        </p>
                        <!-- Fallback inline si el navegador bloquea prompts -->
                        <div id="unsubscribeInlineConfirm" class="mt-3 hidden">
                            <label for="unsubscribe_inline_input" class="block text-sm text-gray-700 mb-2">Para confirmar, escribe exactamente: <span class="font-semibold">DELETED</span></label>
                            <input type="text" id="unsubscribe_inline_input" class="w-full border border-gray-300 rounded-md p-2" placeholder="DELETED">
                            <button type="button" id="unsubscribeInlineConfirmBtn" class="mt-3 w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                                Confirmar baja ahora
                            </button>
                            <p class="text-xs text-gray-600 mt-2">Si tu navegador bloquea ventanas emergentes, utiliza este campo para confirmar.</p>
                        </div>
                    </form>
                    <script>
                        (function(){
                            const btn = document.getElementById('unsubscribeBtn');
                            const form = document.getElementById('unsubscribeForm');
                            const hiddenDomain = document.getElementById('unsubscribe_confirm_domain');
                            const hiddenWord = document.getElementById('unsubscribe_confirm_word');
                            const inlineConfirm = document.getElementById('unsubscribeInlineConfirm');
                            const inlineInput = document.getElementById('unsubscribe_inline_input');
                            const inlineBtn = document.getElementById('unsubscribeInlineConfirmBtn');
                            btn.addEventListener('click', function(){
                                const domain = '{{ Auth::user()->emailDomain() }}';
                                const msg = `Confirmar baja del servicio para el dominio: ${domain}.`;
                                if (!window.confirm(msg)) return;
                                // Intento con prompt; si está bloqueado o es incorrecto, mostrar fallback inline
                                let typed = null;
                                try {
                                    typed = window.prompt('Para confirmar la baja escribe la palabra: DELETED');
                                } catch (e) {
                                    typed = null;
                                }
                                if (typed === 'DELETED') {
                                    hiddenDomain.value = domain;
                                    hiddenWord.value = typed;
                                    form.submit();
                                    return;
                                }

                                // Mostrar flujo inline como alternativa
                                inlineConfirm.classList.remove('hidden');
                                inlineInput && inlineInput.focus();
                            });

                            inlineBtn && inlineBtn.addEventListener('click', function(){
                                const domain = '{{ Auth::user()->emailDomain() }}';
                                const typed = (inlineInput?.value || '').trim();
                                if (typed !== 'DELETED') {
                                    alert('Debes escribir exactamente DELETED para continuar.');
                                    inlineInput && inlineInput.focus();
                                    return;
                                }
                                hiddenDomain.value = domain;
                                hiddenWord.value = typed;
                                form.submit();
                            });
                        })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</body>
</html>