<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Transacción - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
    @php
        $type = $transaction->isDebit() ? 'debit' : 'credit';
        $amount = $transaction->isDebit() ? $transaction->debit_amount : $transaction->credit_amount;
        $statusMap = [
            'draft' => 'pending',
            'posted' => 'completed',
            'cancelled' => 'cancelled',
        ];
        $statusForm = $statusMap[$transaction->status] ?? 'pending';
        $domain = auth()->user()->emailDomain();
        $accounts = \App\Models\Account::forDomain($domain)->active()->acceptsMovements()->orderBy('code')->get();
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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-edit text-green-600 mr-2"></i>
                    Editar Transacción
                </h3>
            </div>
            <form method="POST" action="{{ route('accounting.transactions.update', $transaction) }}" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-1"></i>Fecha
                        </label>
                        <input type="date" id="date" name="date" value="{{ $transaction->transaction_date->toDateString() }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-hashtag mr-1"></i>Referencia
                        </label>
                        <input type="text" id="reference" name="reference" value="{{ $transaction->reference }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-1"></i>Descripción
                        </label>
                        <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>{{ $transaction->description }}</textarea>
                    </div>
                    <div>
                        <label for="account" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-list mr-1"></i>Cuenta
                        </label>
                        <select id="account" name="account" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->code }}" {{ $transaction->account && $transaction->account->code === $acc->code ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-exchange-alt mr-1"></i>Tipo
                        </label>
                        <select id="type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="debit" {{ $type === 'debit' ? 'selected' : '' }}>Débito</option>
                            <option value="credit" {{ $type === 'credit' ? 'selected' : '' }}>Crédito</option>
                        </select>
                    </div>
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-dollar-sign mr-1"></i>Monto
                        </label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0" value="{{ $amount }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-check-circle mr-1"></i>Estado
                        </label>
                        <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="pending" {{ $statusForm === 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="completed" {{ $statusForm === 'completed' ? 'selected' : '' }}>Completada</option>
                            <option value="cancelled" {{ $statusForm === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('accounting.transactions') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"><i class="fas fa-times mr-2"></i>Cancelar</a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"><i class="fas fa-save mr-2"></i>Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>