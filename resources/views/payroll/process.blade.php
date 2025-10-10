<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Nómina - SumAxia</title>
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
                    <h2 class="ml-4 text-lg font-semibold text-gray-700">Procesar Nómina</h2>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                        <a href="{{ route('payroll') }}" class="text-gray-700 hover:text-blue-600">Nómina</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Procesar Nómina</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Payroll Processing Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-calculator text-blue-600 mr-2"></i>
                    Procesamiento de Nómina
                </h3>
                <p class="text-sm text-gray-600 mt-1">Configure los parámetros para procesar la nómina del período seleccionado</p>
            </div>
            
            <form method="POST" action="{{ route('payroll.process.store') }}" class="p-6">
                @csrf
                
                <!-- Period Selection -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-calendar mr-2"></i>Período de Nómina
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="payroll_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Nómina <span class="text-red-500">*</span>
                            </label>
                            <select id="payroll_type" name="payroll_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="semanal">Semanal</option>
                                <option value="quincenal">Quincenal</option>
                                <option value="mensual">Mensual</option>
                                <option value="extraordinaria">Extraordinaria</option>
                                <option value="aguinaldo">Aguinaldo</option>
                                <option value="vacaciones">Vacaciones</option>
                            </select>
                        </div>
                        <div>
                            <label for="period_start" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha Inicio <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="period_start" name="period_start" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="period_end" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha Fin <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="period_end" name="period_end" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Pago <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="payroll_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Nómina
                            </label>
                            <input type="text" id="payroll_number" name="payroll_number" value="NOM-{{ date('Y-m') }}-{{ str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Employee Selection -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-users mr-2"></i>Selección de Empleados
                    </h4>
                    <div class="mb-4">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="employee_selection" value="all" checked 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Todos los empleados activos</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="employee_selection" value="department" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Por departamento</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="employee_selection" value="individual" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Empleados específicos</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Department Selection -->
                    <div id="department-selection" class="hidden mb-4">
                        <label for="selected_departments" class="block text-sm font-medium text-gray-700 mb-2">
                            Departamentos
                        </label>
                        <select id="selected_departments" name="selected_departments[]" multiple 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="administracion">Administración</option>
                            <option value="contabilidad">Contabilidad</option>
                            <option value="ventas">Ventas</option>
                            <option value="marketing">Marketing</option>
                            <option value="sistemas">Sistemas</option>
                            <option value="recursos_humanos">Recursos Humanos</option>
                            <option value="operaciones">Operaciones</option>
                            <option value="produccion">Producción</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Mantenga presionado Ctrl para seleccionar múltiples departamentos</p>
                    </div>

                    <!-- Individual Employee Selection -->
                    <div id="individual-selection" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Empleados
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-60 overflow-y-auto border border-gray-200 rounded-md p-4">
                            <!-- Sample employees - in real app, this would be populated from database -->
                            <label class="flex items-center">
                                <input type="checkbox" name="selected_employees[]" value="1" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Juan Pérez - EMP001</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="selected_employees[]" value="2" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">María García - EMP002</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="selected_employees[]" value="3" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Carlos López - EMP003</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="selected_employees[]" value="4" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-gray-700">Ana Martínez - EMP004</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="selected_employees[]" value="5" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Roberto Silva - EMP005</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Payroll Configuration -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-cogs mr-2"></i>Configuración de Cálculos
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h5 class="text-sm font-medium text-gray-700">Percepciones</h5>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="include_base_salary" value="1" checked 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Sueldo Base</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="include_overtime" value="1" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Horas Extra</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="include_bonuses" value="1" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Bonos y Comisiones</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="include_allowances" value="1" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Prestaciones</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h5 class="text-sm font-medium text-gray-700">Deducciones</h5>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="include_isr" value="1" checked 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">ISR</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="include_imss" value="1" checked 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">IMSS</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="include_infonavit" value="1" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">INFONAVIT</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="include_other_deductions" value="1" 
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Otras Deducciones</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Options -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sliders-h mr-2"></i>Opciones Adicionales
                    </h4>
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="generate_receipts" value="1" checked 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Generar recibos de nómina automáticamente</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="send_notifications" value="1" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Enviar notificaciones por email a empleados</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="create_accounting_entries" value="1" checked 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Crear asientos contables automáticamente</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="backup_before_process" value="1" checked 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Crear respaldo antes de procesar</span>
                        </label>
                    </div>
                </div>

                <!-- Summary -->
                <div class="mb-8 bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>Resumen del Procesamiento
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Empleados a procesar:</span>
                            <span id="employee-count" class="font-medium ml-2">Todos los activos</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Tipo de nómina:</span>
                            <span id="payroll-type-summary" class="font-medium ml-2">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Período:</span>
                            <span id="period-summary" class="font-medium ml-2">-</span>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('payroll') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="button" id="preview-btn" 
                            class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-eye mr-2"></i>Vista Previa
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-play mr-2"></i>Procesar Nómina
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Handle employee selection radio buttons
        document.querySelectorAll('input[name="employee_selection"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const departmentDiv = document.getElementById('department-selection');
                const individualDiv = document.getElementById('individual-selection');
                const employeeCount = document.getElementById('employee-count');
                
                if (this.value === 'all') {
                    departmentDiv.classList.add('hidden');
                    individualDiv.classList.add('hidden');
                    employeeCount.textContent = 'Todos los activos';
                } else if (this.value === 'department') {
                    departmentDiv.classList.remove('hidden');
                    individualDiv.classList.add('hidden');
                    employeeCount.textContent = 'Por departamento';
                } else if (this.value === 'individual') {
                    departmentDiv.classList.add('hidden');
                    individualDiv.classList.remove('hidden');
                    employeeCount.textContent = 'Empleados específicos';
                }
            });
        });

        // Update summary when payroll type changes
        document.getElementById('payroll_type').addEventListener('change', function() {
            document.getElementById('payroll-type-summary').textContent = this.options[this.selectedIndex].text || '-';
        });

        // Update period summary
        function updatePeriodSummary() {
            const startDate = document.getElementById('period_start').value;
            const endDate = document.getElementById('period_end').value;
            const summary = document.getElementById('period-summary');
            
            if (startDate && endDate) {
                summary.textContent = `${startDate} al ${endDate}`;
            } else {
                summary.textContent = '-';
            }
        }

        document.getElementById('period_start').addEventListener('change', updatePeriodSummary);
        document.getElementById('period_end').addEventListener('change', updatePeriodSummary);

        // Preview functionality
        document.getElementById('preview-btn').addEventListener('click', function() {
            alert('Vista previa de nómina - Esta funcionalidad mostraría un resumen detallado de los cálculos antes del procesamiento final.');
        });

        // Auto-set period end based on payroll type and start date
        document.getElementById('payroll_type').addEventListener('change', function() {
            const startDate = document.getElementById('period_start');
            const endDate = document.getElementById('period_end');
            
            if (startDate.value) {
                const start = new Date(startDate.value);
                let end = new Date(start);
                
                switch(this.value) {
                    case 'semanal':
                        end.setDate(start.getDate() + 6);
                        break;
                    case 'quincenal':
                        end.setDate(start.getDate() + 14);
                        break;
                    case 'mensual':
                        end.setMonth(start.getMonth() + 1);
                        end.setDate(start.getDate() - 1);
                        break;
                }
                
                if (this.value !== 'extraordinaria' && this.value !== 'aguinaldo' && this.value !== 'vacaciones') {
                    endDate.value = end.toISOString().split('T')[0];
                    updatePeriodSummary();
                }
            }
        });
    </script>
</body>
</html>