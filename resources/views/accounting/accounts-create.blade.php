<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Cuenta - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
</head>
<body class="bg-gray-50">
    @include('partials.alerts')
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">SumAxia</h1>
                    <span class="ml-4 text-gray-500">|</span>
                    <h2 class="ml-4 text-lg font-semibold text-gray-700">Nueva Cuenta</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Bienvenido, {{ Auth::user()->name ?? 'Usuario' }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                            <i class="fas fa-sign-out-alt mr-1"></i>Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('accounting.accounts') }}" class="text-gray-700 hover:text-blue-600">Cuentas</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Nueva Cuenta</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Account Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                    Crear Nueva Cuenta Contable
                </h3>
                <p class="text-sm text-gray-600 mt-1">Complete la información para crear una nueva cuenta en el catálogo contable</p>
            </div>
            
            <form id="accountForm" method="POST" action="{{ route('accounting.accounts.store') }}" class="p-6">
                @csrf
                <input type="hidden" name="confirm_domain" id="confirm_domain" value="">
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>Información Básica
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="account_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Código de Cuenta <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="account_code" name="account_code" placeholder="Ej: 1001, 2001, 3001" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <p class="text-xs text-gray-500 mt-1">Código único para identificar la cuenta</p>
                        </div>
                        <div>
                            <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Cuenta <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="account_name" name="account_name" placeholder="Ej: Caja, Bancos, Proveedores" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                    </div>
                </div>

                <!-- Account Classification -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-tags mr-2"></i>Clasificación Contable
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="account_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Cuenta <span class="text-red-500">*</span>
                            </label>
                            <select id="account_type" name="account_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="activo">Activo</option>
                                <option value="pasivo">Pasivo</option>
                                <option value="capital">Capital</option>
                                <option value="ingresos">Ingresos</option>
                                <option value="gastos">Gastos</option>
                                <option value="costos">Costos</option>
                            </select>
                        </div>
                        <div>
                            <label for="account_subtype" class="block text-sm font-medium text-gray-700 mb-2">
                                Subtipo
                            </label>
                            <select id="account_subtype" name="account_subtype" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Seleccionar subtipo</option>
                                <!-- Activo -->
                                <option value="circulante" data-parent="activo">Circulante</option>
                                <option value="fijo" data-parent="activo">Fijo</option>
                                <option value="diferido" data-parent="activo">Diferido</option>
                                <!-- Pasivo -->
                                <option value="corto_plazo" data-parent="pasivo">Corto Plazo</option>
                                <option value="largo_plazo" data-parent="pasivo">Largo Plazo</option>
                                <!-- Capital -->
                                <option value="contribuido" data-parent="capital">Contribuido</option>
                                <option value="ganado" data-parent="capital">Ganado</option>
                                <!-- Ingresos -->
                                <option value="operacionales" data-parent="ingresos">Operacionales</option>
                                <option value="no_operacionales" data-parent="ingresos">No Operacionales</option>
                                <!-- Gastos -->
                                <option value="operacion" data-parent="gastos">Operación</option>
                                <option value="administracion" data-parent="gastos">Administración</option>
                                <option value="ventas" data-parent="gastos">Ventas</option>
                                <option value="financieros" data-parent="gastos">Financieros</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Account Details -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-cog mr-2"></i>Configuración de la Cuenta
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="normal_balance" class="block text-sm font-medium text-gray-700 mb-2">
                                Saldo Normal <span class="text-red-500">*</span>
                            </label>
                            <select id="normal_balance" name="normal_balance" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Seleccionar</option>
                                <option value="deudor">Deudor</option>
                                <option value="acreedor">Acreedor</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Indica si la cuenta normalmente tiene saldo deudor o acreedor</p>
                        </div>
                        <div>
                            <label for="initial_balance" class="block text-sm font-medium text-gray-700 mb-2">
                                Saldo Inicial
                            </label>
                            <input type="number" id="initial_balance" name="initial_balance" step="0.01" value="0.00" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Saldo inicial de la cuenta (opcional)</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-file-alt mr-2"></i>Información Adicional
                    </h4>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción
                            </label>
                            <textarea id="description" name="description" rows="3" 
                                      placeholder="Descripción detallada del propósito y uso de esta cuenta..." 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                Cuenta activa (disponible para transacciones)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Account Hierarchy (Optional) -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sitemap mr-2"></i>Jerarquía (Opcional)
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="parent_account" class="block text-sm font-medium text-gray-700 mb-2">
                                Cuenta Padre
                            </label>
                            <select id="parent_account" name="parent_account" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sin cuenta padre (cuenta principal)</option>
                                <option value="1000">1000 - Activos</option>
                                <option value="2000">2000 - Pasivos</option>
                                <option value="3000">3000 - Capital</option>
                                <option value="4000">4000 - Ingresos</option>
                                <option value="5000">5000 - Gastos</option>
                                <option value="6000">6000 - Costos</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Seleccione si esta cuenta es subcuenta de otra</p>
                        </div>
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                                Nivel
                            </label>
                            <select id="level" name="level" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="1">1 - Cuenta Principal</option>
                                <option value="2">2 - Subcuenta</option>
                                <option value="3">3 - Sub-subcuenta</option>
                                <option value="4" selected>4 - Cuenta de Detalle</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('accounting.accounts') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="button" id="submitBtn"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>Crear Cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Confirmación de dominio antes de enviar
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('accountForm');
        const confirmDomainInput = document.getElementById('confirm_domain');
        submitBtn.addEventListener('click', function() {
            const domain = '{{ Auth::user()->emailDomain() }}';
            const msg = `Confirmar dominio de servicio: ${domain}?`;
            if (window.confirm(msg)) {
                confirmDomainInput.value = domain;
                form.submit();
            }
        });

        // Filter subtypes based on account type selection
        document.getElementById('account_type').addEventListener('change', function() {
            const selectedType = this.value;
            const subtypeSelect = document.getElementById('account_subtype');
            const subtypeOptions = subtypeSelect.querySelectorAll('option[data-parent]');
            
            // Reset subtype selection
            subtypeSelect.value = '';
            
            // Show/hide options based on selected type
            subtypeOptions.forEach(option => {
                if (option.dataset.parent === selectedType) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });

        // Auto-suggest normal balance based on account type
        document.getElementById('account_type').addEventListener('change', function() {
            const selectedType = this.value;
            const normalBalanceSelect = document.getElementById('normal_balance');
            
            switch(selectedType) {
                case 'activo':
                case 'gastos':
                case 'costos':
                    normalBalanceSelect.value = 'deudor';
                    break;
                case 'pasivo':
                case 'capital':
                case 'ingresos':
                    normalBalanceSelect.value = 'acreedor';
                    break;
                default:
                    normalBalanceSelect.value = '';
            }
        });

        // Format initial balance input
        document.getElementById('initial_balance').addEventListener('blur', function() {
            const value = parseFloat(this.value) || 0;
            this.value = value.toFixed(2);
        });
    </script>
</body>
</html>