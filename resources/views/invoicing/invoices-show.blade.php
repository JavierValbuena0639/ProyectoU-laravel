<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $invoice->invoice_number }} - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
</head>
<body class="bg-gray-50">
    <div class="max-w-3xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">SumAxia</a>
                <p class="text-sm text-gray-500">Factura</p>
            </div>
            <div class="space-x-2">
                <a href="{{ route('invoicing.invoices') }}" class="px-3 py-2 text-sm bg-gray-100 rounded hover:bg-gray-200">Volver</a>
                <button onclick="window.print()" class="px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-print mr-1"></i>Imprimir / PDF
                </button>
            </div>
        </div>

        <div class="bg-white shadow rounded p-6">
            <div class="flex justify-between mb-4">
                <div>
                    <h1 class="text-xl font-semibold">Factura {{ $invoice->invoice_number }}</h1>
                    <p class="text-sm text-gray-600">Estado: <span class="font-medium">{{ ucfirst($invoice->status) }}</span></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Fecha: {{ $invoice->invoice_date->format('d/m/Y') }}</p>
                    <p class="text-sm text-gray-600">Vence: {{ $invoice->due_date->format('d/m/Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 mb-2">Cliente</h2>
                    <p class="text-gray-900">{{ $invoice->client_name }}</p>
                    <p class="text-gray-700">Documento: {{ $invoice->client_document }}</p>
                    @if($invoice->client_email)
                        <p class="text-gray-700">Email: {{ $invoice->client_email }}</p>
                    @endif
                    @if($invoice->client_address)
                        <p class="text-gray-700">Dirección: {{ $invoice->client_address }}</p>
                    @endif
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 mb-2">Totales</h2>
                    <p class="text-gray-700">Subtotal: <span class="font-medium">@money($invoice->subtotal)</span></p>
                    <p class="text-gray-700">IVA (16%): <span class="font-medium">@money($invoice->tax_amount)</span></p>
                    <p class="text-gray-900 text-lg">Total: <span class="font-bold">@money($invoice->total_amount)</span></p>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-semibold text-gray-700 mb-2">Productos/Servicios</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Descripción</th>
                                <th class="px-4 py-2 text-right text-sm font-medium text-gray-700">Cantidad</th>
                                <th class="px-4 py-2 text-right text-sm font-medium text-gray-700">Precio</th>
                                <th class="px-4 py-2 text-right text-sm font-medium text-gray-700">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($invoice->items ?? []) as $item)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $item['description'] ?? '' }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format((float)($item['quantity'] ?? 0), 0) }}</td>
                                    <td class="px-4 py-2 text-right">@money((float)($item['price'] ?? 0))</td>
                                    <td class="px-4 py-2 text-right">@money(((float)($item['quantity'] ?? 0)) * ((float)($item['price'] ?? 0)))</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($invoice->notes)
                <div class="mt-6">
                    <h2 class="text-sm font-semibold text-gray-700 mb-2">Notas</h2>
                    <p class="text-gray-700">{{ $invoice->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>