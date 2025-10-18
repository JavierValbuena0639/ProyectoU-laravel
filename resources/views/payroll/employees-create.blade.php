<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Empleado - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">SumAxia</h1>
                    <span class="ml-4 text-gray-500">|</span>
                    <h2 class="ml-4 text-lg font-semibold text-gray-700">Nuevo Empleado</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Bienvenido, {{ Auth::user()->name ?? 'Usuario' }}</span>
                    <div class="flex items-center space-x-2 text-sm">
            <a href="{{ route('locale.switch', ['lang' => 'es']) }}" class="{{ app()->getLocale() === 'es' ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">ES</a>
                        <span class="text-gray-300">|</span>
            <a href="{{ route('locale.switch', ['lang' => 'en']) }}" class="{{ app()->getLocale() === 'en' ? 'font-semibold text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">EN</a>
                    </div>
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

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                        <a href="{{ route('payroll.index') }}" class="text-gray-700 hover:text-blue-600">Nómina</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Nuevo Empleado</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Employee Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                    Registrar Nuevo Empleado
                </h3>
                <p class="text-sm text-gray-600 mt-1">Complete la información del empleado para agregarlo al sistema de nómina</p>
            </div>
            
            <form method="POST" action="{{ route('payroll.employees.store') }}" class="p-6">
                @csrf
                
                <!-- Personal Information -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user mr-2"></i>Información Personal
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre(s) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name" placeholder="Nombre completo" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Apellido Paterno <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name" placeholder="Apellido paterno" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="mother_last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Apellido Materno
                            </label>
                            <input type="text" id="mother_last_name" name="mother_last_name" placeholder="Apellido materno" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Empleado <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="employee_id" name="employee_id" placeholder="Ej: EMP001" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Nacimiento <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="birth_date" name="birth_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                Género <span class="text-red-500">*</span>
                            </label>
                            <select id="gender" name="gender" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Seleccionar</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-address-book mr-2"></i>Información de Contacto
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" placeholder="empleado@empresa.com" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" placeholder="+52 55 1234 5678" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Dirección <span class="text-red-500">*</span>
                            </label>
                            <textarea id="address" name="address" rows="2" 
                                      placeholder="Calle, número, colonia, ciudad, estado, código postal" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Job Information -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-briefcase mr-2"></i>Información Laboral
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                                Puesto <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="position" name="position" placeholder="Ej: Desarrollador, Contador, Gerente" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                                Departamento <span class="text-red-500">*</span>
                            </label>
                            <select id="department" name="department" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Seleccionar departamento</option>
                                <option value="administracion">Administración</option>
                                <option value="contabilidad">Contabilidad</option>
                                <option value="ventas">Ventas</option>
                                <option value="marketing">Marketing</option>
                                <option value="sistemas">Sistemas</option>
                                <option value="recursos_humanos">Recursos Humanos</option>
                                <option value="operaciones">Operaciones</option>
                                <option value="produccion">Producción</option>
                            </select>
                        </div>
                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Contratación <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="hire_date" name="hire_date" value="{{ date('Y-m-d') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Contrato <span class="text-red-500">*</span>
                            </label>
                            <select id="employment_type" name="employment_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="tiempo_completo">Tiempo Completo</option>
                                <option value="medio_tiempo">Medio Tiempo</option>
                                <option value="temporal">Temporal</option>
                                <option value="por_proyecto">Por Proyecto</option>
                                <option value="practicante">Practicante</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Estado <span class="text-red-500">*</span>
                            </label>
                            <select id="status" name="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="suspendido">Suspendido</option>
                                <option value="vacaciones">En Vacaciones</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Salary Information -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-dollar-sign mr-2"></i>Información Salarial
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="base_salary" class="block text-sm font-medium text-gray-700 mb-2">
                                Salario Base <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="base_salary" name="base_salary" step="0.01" min="0" placeholder="0.00" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="salary_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Salario <span class="text-red-500">*</span>
                            </label>
                            <select id="salary_type" name="salary_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Seleccionar</option>
                                <option value="mensual">Mensual</option>
                                <option value="quincenal">Quincenal</option>
                                <option value="semanal">Semanal</option>
                                <option value="por_hora">Por Hora</option>
                                <option value="por_dia">Por Día</option>
                            </select>
                        </div>
                        <div>
                            <label for="bank_account" class="block text-sm font-medium text-gray-700 mb-2">
                                Cuenta Bancaria
                            </label>
                            <input type="text" id="bank_account" name="bank_account" placeholder="Número de cuenta" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Legal Information -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-file-contract mr-2"></i>Información Legal
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="rfc" class="block text-sm font-medium text-gray-700 mb-2">
                                RFC <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="rfc" name="rfc" placeholder="ABCD123456EF7" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="curp" class="block text-sm font-medium text-gray-700 mb-2">
                                CURP <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="curp" name="curp" placeholder="ABCD123456HDFGHI01" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="nss" class="block text-sm font-medium text-gray-700 mb-2">
                                NSS (IMSS)
                            </label>
                            <input type="text" id="nss" name="nss" placeholder="12345678901" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-phone mr-2"></i>Contacto de Emergencia
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Contacto
                            </label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" placeholder="Nombre completo" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono de Emergencia
                            </label>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" placeholder="+52 55 1234 5678" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-2">
                                Parentesco
                            </label>
                            <select id="emergency_contact_relationship" name="emergency_contact_relationship" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Seleccionar</option>
                                <option value="padre">Padre</option>
                                <option value="madre">Madre</option>
                                <option value="esposo">Esposo(a)</option>
                                <option value="hermano">Hermano(a)</option>
                                <option value="hijo">Hijo(a)</option>
                                <option value="amigo">Amigo(a)</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('payroll.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>Registrar Empleado
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Format salary input
        document.getElementById('base_salary').addEventListener('blur', function() {
            const value = parseFloat(this.value) || 0;
            this.value = value.toFixed(2);
        });

        // Auto-format RFC input
        document.getElementById('rfc').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Auto-format CURP input
        document.getElementById('curp').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Validate age (must be at least 18)
        document.getElementById('birth_date').addEventListener('change', function() {
            const birthDate = new Date(this.value);
            const today = new Date();
            const age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            if (age < 18) {
                alert('El empleado debe ser mayor de 18 años');
                this.value = '';
            }
        });
    </script>
</body>
</html>