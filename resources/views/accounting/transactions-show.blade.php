<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Transacción - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
    @php
        $typeLabel = $transaction->isDebit() ? 'Débito' : 'Crédito';
        $amount = $transaction->isDebit() ? $transaction->debit_amount : $transaction->credit_amount;
        $statusLabel = [
            'draft' => 'Pendiente',
            'posted' => 'Completada',
            'cancelled' => 'Cancelada',
        ][$transaction->status] ?? $transaction->status;
    @endphp
    </head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">SumAxia</a>
                    <span class="ml-2 text-sm text-gray-500">/ Transacciones</span>
                </div>
                <a href="{{ route('accounting.transactions') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-list mr-1"></i>Listado
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-semibold mb-4">Detalle de Transacción</h1>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-600">Comprobante:</span><span class="font-mono">{{ $transaction->voucher_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Fecha:</span><span>{{ $transaction->transaction_date->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Referencia:</span><span class="font-mono">{{ $transaction->reference }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Descripción:</span><span>{{ $transaction->description }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Cuenta:</span><span>{{ $transaction->account->code ?? 'N/A' }} - {{ $transaction->account->name ?? 'N/A' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Tipo:</span><span>{{ $typeLabel }}</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Monto:</span><span class="{{ $transaction->isDebit() ? 'text-red-600' : 'text-green-600' }}">@money($amount)</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Estado:</span><span>{{ $statusLabel }}</span></div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('accounting.transactions.edit', $transaction) }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"><i class="fas fa-edit mr-2"></i>Editar</a>
                <form action="{{ route('accounting.transactions.cancel', $transaction) }}" method="POST" onsubmit="return confirm('¿Cancelar esta transacción?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"><i class="fas fa-ban mr-2"></i>Cancelar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>