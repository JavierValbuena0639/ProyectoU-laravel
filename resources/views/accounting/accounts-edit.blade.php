<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cuenta - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">SumAxia</a>
                    <span class="ml-2 text-sm text-gray-500">/ Contabilidad / Editar Cuenta</span>
                </div>
                <a href="{{ route('accounting.accounts') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-list mr-1"></i>Volver a Cuentas
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">Editar Cuenta</h2>
            @php($parentCode = $account->parent_id ? \App\Models\Account::find($account->parent_id)?->code : '')
            <form id="accountEditForm" method="POST" action="{{ route('accounting.accounts.update', $account->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" id="confirm_domain" name="confirm_domain" value="">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="account_code" class="block text-sm font-medium text-gray-700 mb-2">Código</label>
                        <input type="text" id="account_code" name="account_code" value="{{ old('account_code', $account->code) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('account_code')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                        <input type="text" id="account_name" name="account_name" value="{{ old('account_name', $account->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('account_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="account_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        @php($typeVal = old('account_type', $account->type))
                        <select id="account_type" name="account_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @php($types = ['activo','pasivo','capital','ingresos','gastos','costos'])
                            @foreach($types as $t)
                                <option value="{{ $t }}" {{ $typeVal === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="normal_balance" class="block text-sm font-medium text-gray-700 mb-2">Saldo Normal</label>
                        @php($nbal = old('normal_balance', ($account->nature === 'debito' ? 'deudor' : ($account->nature === 'credito' ? 'acreedor' : ''))))
                        <select id="normal_balance" name="normal_balance" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="deudor" {{ $nbal === 'deudor' ? 'selected' : '' }}>Deudor</option>
                            <option value="acreedor" {{ $nbal === 'acreedor' ? 'selected' : '' }}>Acreedor</option>
                        </select>
                    </div>
                    <div>
                        <label for="initial_balance" class="block text-sm font-medium text-gray-700 mb-2">Saldo Inicial</label>
                        <input type="number" step="0.01" id="initial_balance" name="initial_balance" value="{{ old('initial_balance', number_format((float)$account->balance, 2, '.', '')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $account->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', ($account->is_active ?? $account->active) ? 'checked' : '') }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">Cuenta activa (disponible para transacciones)</label>
                        </div>
                    </div>
                    <div>
                        <label for="parent_account" class="block text-sm font-medium text-gray-700 mb-2">Cuenta Padre</label>
                        <input type="text" id="parent_account" name="parent_account" value="{{ old('parent_account', $parentCode) }}" placeholder="Código de cuenta padre" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Nivel</label>
                        <select id="level" name="level" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @for($i=1;$i<=4;$i++)
                                <option value="{{ $i }}" {{ (int) old('level', $account->level) === $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('accounting.accounts') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"><i class="fas fa-times mr-2"></i>Cancelar</a>
                    <button type="button" id="submitEditBtn" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"><i class="fas fa-save mr-2"></i>Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const submitEditBtn = document.getElementById('submitEditBtn');
        const editForm = document.getElementById('accountEditForm');
        const confirmDomainInput = document.getElementById('confirm_domain');
        submitEditBtn.addEventListener('click', function() {
            const domain = '{{ Auth::user()->emailDomain() }}';
            const msg = `Confirmar dominio de servicio: ${domain}?`;
            if (window.confirm(msg)) {
                confirmDomainInput.value = domain;
                editForm.submit();
            }
        });
        document.getElementById('initial_balance').addEventListener('blur', function() {
            const value = parseFloat(this.value) || 0;
            this.value = value.toFixed(2);
        });
        // Sugerir saldo normal según tipo
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
                    normalBalanceSelect.value = 'deudor';
            }
        });
    </script>
</body>
</html>