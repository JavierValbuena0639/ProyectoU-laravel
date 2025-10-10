<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Factura - SumAxia</title>
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
                    <h2 class="ml-4 text-lg font-semibold text-gray-700">Nueva Factura</h2>
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
                        <a href="{{ route('invoicing.invoices') }}" class="text-gray-700 hover:text-blue-600">Facturas</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-500">Nueva Factura</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Invoice Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-file-invoice text-green-600 mr-2"></i>
                    Crear Nueva Factura
                </h3>
            </div>
            
            <form method="POST" action="{{ route('invoicing.invoices.store') }}" class="p-6">
                @csrf
                
                <!-- Client Information -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user mr-2"></i>Información del Cliente
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="client_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Cliente
                            </label>
                            <input type="text" id="client_name" name="client_name" placeholder="Nombre completo o empresa" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="client_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email del Cliente
                            </label>
                            <input type="email" id="client_email" name="client_email" placeholder="cliente@ejemplo.com" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="client_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono
                            </label>
                            <input type="tel" id="client_phone" name="client_phone" placeholder="+1 234 567 8900" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="client_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Dirección
                            </label>
                            <input type="text" id="client_address" name="client_address" placeholder="Dirección completa" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-file-alt mr-2"></i>Detalles de la Factura
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Factura
                            </label>
                            <input type="text" id="invoice_number" name="invoice_number" value="FAC-{{ date('Y') }}-{{ str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Emisión
                            </label>
                            <input type="date" id="issue_date" name="issue_date" value="{{ date('Y-m-d') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Vencimiento
                            </label>
                            <input type="date" id="due_date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="mb-8">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">
                        <i class="fas fa-list mr-2"></i>Productos/Servicios
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Descripción</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Cantidad</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Precio Unit.</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Total</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="invoice-items">
                                <tr>
                                    <td class="px-4 py-2">
                                        <input type="text" name="items[0][description]" placeholder="Descripción del producto/servicio" 
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="items[0][quantity]" value="1" min="1" 
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm quantity-input" required>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="items[0][price]" step="0.01" min="0" placeholder="0.00" 
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm price-input" required>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="item-total font-medium">$0.00</span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <button type="button" class="text-red-600 hover:text-red-800 remove-item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="add-item" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                        <i class="fas fa-plus mr-2"></i>Agregar Producto/Servicio
                    </button>
                </div>

                <!-- Totals -->
                <div class="mb-8">
                    <div class="flex justify-end">
                        <div class="w-full md:w-1/3">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-600">Subtotal:</span>
                                    <span id="subtotal" class="font-medium">$0.00</span>
                                </div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-600">IVA (16%):</span>
                                    <span id="tax" class="font-medium">$0.00</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t pt-2">
                                    <span>Total:</span>
                                    <span id="total">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-8">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-1"></i>Notas Adicionales
                    </label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Términos y condiciones, notas de pago, etc." 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('invoicing.invoices') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class="fas fa-save mr-2"></i>Crear Factura
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemCount = 1;

        // Add new item row
        document.getElementById('add-item').addEventListener('click', function() {
            const tbody = document.getElementById('invoice-items');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="px-4 py-2">
                    <input type="text" name="items[${itemCount}][description]" placeholder="Descripción del producto/servicio" 
                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemCount}][quantity]" value="1" min="1" 
                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm quantity-input" required>
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemCount}][price]" step="0.01" min="0" placeholder="0.00" 
                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm price-input" required>
                </td>
                <td class="px-4 py-2">
                    <span class="item-total font-medium">$0.00</span>
                </td>
                <td class="px-4 py-2">
                    <button type="button" class="text-red-600 hover:text-red-800 remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            itemCount++;
            attachEventListeners();
        });

        // Remove item row
        function attachEventListeners() {
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    if (document.querySelectorAll('#invoice-items tr').length > 1) {
                        this.closest('tr').remove();
                        calculateTotals();
                    }
                });
            });

            document.querySelectorAll('.quantity-input, .price-input').forEach(input => {
                input.addEventListener('input', calculateTotals);
            });
        }

        // Calculate totals
        function calculateTotals() {
            let subtotal = 0;
            
            document.querySelectorAll('#invoice-items tr').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                const total = quantity * price;
                
                row.querySelector('.item-total').textContent = '$' + total.toFixed(2);
                subtotal += total;
            });

            const tax = subtotal * 0.16;
            const total = subtotal + tax;

            document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('tax').textContent = '$' + tax.toFixed(2);
            document.getElementById('total').textContent = '$' + total.toFixed(2);
        }

        // Initialize event listeners
        attachEventListeners();
    </script>
</body>
</html>