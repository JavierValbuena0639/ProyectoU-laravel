<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizaciones - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    @include('partials.alerts')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Cotizaciones</h1>
            <div class="flex gap-2">
                <a href="{{ route('invoicing.quotes.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm">
                    <i class="fas fa-file-alt mr-2"></i>Nueva Cotización
                </a>
                <a href="{{ route('invoicing.invoices') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                    <i class="fas fa-file-invoice mr-2"></i>Facturas
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Emitida</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($quotes as $q)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $q->quote_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $q->client_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $q->issue_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($q->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded text-white {{ $q->status === 'converted' ? 'bg-green-600' : 'bg-gray-600' }}">{{ $q->status }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('invoicing.quotes.history', $q->id) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-history mr-1"></i>Historial
                                </a>
                                @if($q->status !== 'converted')
                                    <form method="POST" action="{{ route('invoicing.quotes.convert', $q->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-exchange-alt mr-1"></i>Convertir a Factura
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay cotizaciones aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $quotes->links() }}
        </div>
    </div>
 </body>
</html>