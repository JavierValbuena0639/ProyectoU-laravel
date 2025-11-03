<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan de Cuentas - SumAxia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('icons/calculator.svg') }}" type="image/svg+xml">
</head>
<body class="bg-gray-50">
    @include('partials.alerts')
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">SumAxia</a>
                    <span class="ml-2 text-sm text-gray-500">/ Contabilidad</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    @php $currentLocale = app()->getLocale(); @endphp
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('locale.switch', ['lang' => 'es']) }}" aria-label="Español" title="Español"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'es' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">🇪🇸</a>
                        <a href="{{ route('locale.switch', ['lang' => 'en']) }}" aria-label="English" title="English"
                           class="px-2 py-1 rounded border {{ $currentLocale === 'en' ? 'border-blue-500 text-blue-600' : 'border-gray-300 text-gray-700' }} hover:border-blue-500 hover:text-blue-600">🇺🇸</a>
                    </div>
                    <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-home mr-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Plan de Cuentas</h1>
            <p class="text-gray-600">Gestiona el catálogo de cuentas contables</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6">
            <nav class="flex space-x-8">
                <a href="#" class="border-b-2 border-blue-500 text-blue-600 py-2 px-1 text-sm font-medium">
                    Plan de Cuentas
                </a>
                <a href="{{ route('accounting.transactions') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 py-2 px-1 text-sm font-medium">
                    Transacciones
                </a>
            </nav>
        </div>

        <!-- Action Buttons -->
        <div class="mb-6">
            <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                <a href="{{ route('accounting.accounts.create') }}" class="text-white">
                    <i class="fas fa-plus mr-2"></i>Nueva Cuenta
                </a>
            </button>
            <a href="{{ route('accounting.accounts.export.csv') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg ml-2 inline-block" title="Exportar CSV">
                <i class="fas fa-download mr-2"></i>Exportar CSV
            </a>
        </div>

        <!-- Accounts Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Catálogo de Cuentas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $domain = Auth::user()->emailDomain();
                            $accounts = isset($accounts) ? $accounts : \App\Models\Account::forDomain($domain)->get();
                        @endphp
                        @foreach($accounts as $account)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $account->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $account->name }}</div>
                                @if($account->description)
                                <div class="text-sm text-gray-500">{{ $account->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($account->type)
                                        @case('asset') bg-green-100 text-green-800 @break
                                        @case('liability') bg-red-100 text-red-800 @break
                                        @case('equity') bg-blue-100 text-blue-800 @break
                                        @case('income') bg-yellow-100 text-yellow-800 @break
                                        @case('expense') bg-purple-100 text-purple-800 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ ucfirst($account->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($account->balance, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ ($account->active) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ($account->active) ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('accounting.accounts.edit', $account->id) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="text-green-600 hover:text-green-900 mr-3 js-account-view" title="Ver"
                                        data-code="{{ $account->code }}"
                                        data-name="{{ $account->name }}"
                                        data-type="{{ $account->type }}"
                                        data-nature="{{ $account->nature ?? '' }}"
                                        data-level="{{ $account->level ?? '' }}"
                                        data-balance="{{ number_format((float)$account->balance, 2) }}"
                                        data-active="{{ ($account->active) ? 'Sí' : 'No' }}"
                                        data-accepts="{{ $account->accepts_movements ? 'Sí' : 'No' }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <form method="POST" action="{{ route('accounting.accounts.deactivate', $account->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Desactivar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mt-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-coins text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Activos</p>
                        @php($domain = Auth::user()->emailDomain())
                        <p class="text-lg font-semibold text-gray-900">{{ \App\Models\Account::forDomain($domain)->where('type', 'activo')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-credit-card text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pasivos</p>
                        @php($domain = Auth::user()->emailDomain())
                        <p class="text-lg font-semibold text-gray-900">{{ \App\Models\Account::forDomain($domain)->where('type', 'pasivo')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-balance-scale text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Patrimonio</p>
                        @php($domain = Auth::user()->emailDomain())
                        <p class="text-lg font-semibold text-gray-900">{{ \App\Models\Account::forDomain($domain)->where('type', 'patrimonio')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-arrow-up text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ingresos</p>
                        @php($domain = Auth::user()->emailDomain())
                        <p class="text-lg font-semibold text-gray-900">{{ \App\Models\Account::forDomain($domain)->where('type', 'ingreso')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-arrow-down text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Gastos</p>
                        @php($domain = Auth::user()->emailDomain())
                        <p class="text-lg font-semibold text-gray-900">{{ \App\Models\Account::forDomain($domain)->where('type', 'gasto')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Ver Cuenta -->
        <div id="account-view-modal" class="fixed inset-0 bg-black bg-opacity-30 hidden items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Detalle de Cuenta</h3>
                    <button id="account-view-close" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
                </div>
                <div class="space-y-2 text-sm" id="account-view-body"></div>
                <div class="mt-4 text-right">
                    <button id="account-view-close-bottom" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<script>
    function openAccountView(data) {
        const body = document.getElementById('account-view-body');
        body.innerHTML = `
            <div class="flex justify-between"><span class="text-gray-600">Código:</span><span class="font-medium">${data.code}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Nombre:</span><span class="font-medium">${data.name}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Tipo:</span><span class="font-medium">${data.type}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Naturaleza:</span><span class="font-medium">${data.nature}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Nivel:</span><span class="font-medium">${data.level}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Balance:</span><span class="font-medium">$${data.balance}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Activa:</span><span class="font-medium">${data.active}</span></div>
            <div class="flex justify-between"><span class="text-gray-600">Acepta Movimientos:</span><span class="font-medium">${data.accepts}</span></div>
        `;
        const modal = document.getElementById('account-view-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeAccountView() {
        const modal = document.getElementById('account-view-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.js-account-view').forEach(function(btn){
            btn.addEventListener('click', function(){
                openAccountView({
                    code: btn.dataset.code,
                    name: btn.dataset.name,
                    type: btn.dataset.type,
                    nature: btn.dataset.nature,
                    level: btn.dataset.level,
                    balance: btn.dataset.balance,
                    active: btn.dataset.active,
                    accepts: btn.dataset.accepts,
                });
            });
        });
        document.getElementById('account-view-close')?.addEventListener('click', closeAccountView);
        document.getElementById('account-view-close-bottom')?.addEventListener('click', closeAccountView);
        document.getElementById('account-view-modal')?.addEventListener('click', function(e){
            if (e.target.id === 'account-view-modal') closeAccountView();
        });
    });
</script>
</body>
</html>