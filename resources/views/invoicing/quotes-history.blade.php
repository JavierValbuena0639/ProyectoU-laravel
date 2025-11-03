<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Cotización - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    @include('partials.alerts')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Historial: {{ $quote->quote_number }}</h1>
            <a href="{{ route('invoicing.quotes.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="text-sm text-gray-500">Cliente</div>
                    <div class="text-lg font-semibold">{{ $quote->client_name }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Emitida</div>
                    <div class="text-lg font-semibold">{{ $quote->issue_date->format('Y-m-d') }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Total</div>
                    <div class="text-lg font-semibold">{{ number_format($quote->total_amount, 2) }}</div>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                Estado actual: <span class="px-2 py-1 rounded text-white {{ $quote->status === 'converted' ? 'bg-green-600' : 'bg-gray-600' }}">{{ $quote->status }}</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Versión</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($versions as $v)
                        <tr>
                            <td class="px-6 py-4 text-sm">{{ $v->version }}</td>
                            <td class="px-6 py-4 text-sm">{{ $v->change_reason }}</td>
                            <td class="px-6 py-4 text-sm">{{ $v->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 text-sm">
                                <details>
                                    <summary class="cursor-pointer text-blue-600">Ver snapshot</summary>
                                    <pre class="mt-2 bg-gray-100 p-3 rounded text-xs overflow-auto">{{ json_encode($v->snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($quote->status !== 'converted')
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Registrar nueva versión</h3>
            <form method="POST" action="{{ route('invoicing.quotes.version', $quote->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo del cambio</label>
                    <input type="text" name="change_reason" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Actualización de precios, cambio de cantidad, etc.">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Actualizar items (opcional)</label>
                    <textarea name="items" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="4" placeholder='JSON de items e.g. [{"description":"Servicio","quantity":2,"price":100}]'></textarea>
                    <p class="text-xs text-gray-500 mt-1">Si envías items como JSON, se recalcularán los totales automáticamente.</p>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar nueva versión</button>
            </form>
        </div>
        @endif
    </div>
</body>
</html>