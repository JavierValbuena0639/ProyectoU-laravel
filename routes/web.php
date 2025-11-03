<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Ruta principal - redirige al dashboard si está autenticado, sino al login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Rutas de autenticación
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Rutas de administración (solo admin)
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
        
        Route::get('/users', function () {
            return view('admin.users');
        })->name('users');
    });
    
    // Rutas de contabilidad
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/accounts', function () {
            return view('accounting.accounts');
        })->name('accounts');
        
        Route::get('/accounts/create', function () {
            return view('accounting.accounts-create');
        })->name('accounts.create');
        
        Route::post('/accounts', function () {
            return redirect()->route('accounting.accounts')->with('success', 'Cuenta creada exitosamente');
        })->name('accounts.store');

        // Editar cuenta
        Route::get('/accounts/{account}/edit', function (\App\Models\Account $account) {
            return view('accounting.accounts-edit', compact('account'));
        })->name('accounts.edit');

        // Actualizar cuenta
        Route::put('/accounts/{account}', function (\Illuminate\Http\Request $request, \App\Models\Account $account) {
            $validated = $request->validate([
                'account_code' => ['required', 'string', 'max:10', \Illuminate\Validation\Rule::unique('accounts', 'code')->ignore($account->id)],
                'account_name' => ['required', 'string', 'max:255'],
                'account_type' => ['required', 'string'],
                'normal_balance' => ['required', 'string'],
                'initial_balance' => ['nullable', 'numeric'],
                'description' => ['nullable', 'string'],
                'is_active' => ['nullable'],
                'parent_account' => ['nullable', 'string'],
                'level' => ['required', 'integer'],
                'confirm_domain' => ['nullable', 'string']
            ]);

            $userDomain = \Illuminate\Support\Facades\Auth::user()->emailDomain();
            if (!empty($validated['confirm_domain']) && $validated['confirm_domain'] !== $userDomain) {
                return back()->withErrors(['confirm_domain' => 'El dominio confirmado no coincide con su dominio de servicio.'])->withInput();
            }

            $typeInput = $validated['account_type'];
            $typeMap = [
                'capital' => 'patrimonio',
                'ingresos' => 'ingreso',
                'gastos' => 'gasto',
                'costos' => 'costo',
            ];
            $type = $typeMap[$typeInput] ?? $typeInput;

            $nature = $validated['normal_balance'] === 'deudor' ? 'debito' : 'credito';

            $parentId = null;
            if (!empty($validated['parent_account'])) {
                $parent = \App\Models\Account::where('code', $validated['parent_account'])->first();
                $parentId = $parent?->id;
            }

            $account->code = $validated['account_code'];
            $account->name = $validated['account_name'];
            $account->type = $type;
            $account->nature = $nature;
            $account->parent_id = $parentId;
            $account->level = (int) $validated['level'];
            $account->balance = (float) ($validated['initial_balance'] ?? $account->balance ?? 0);
            // Usamos solo la columna 'active' que existe en la tabla
            $account->active = $request->boolean('is_active');
            $account->description = $validated['description'] ?? null;
            $account->service_domain = $userDomain; // mantiene dominio
            $account->save();

            return redirect()->route('accounting.accounts')->with('success', 'Cuenta actualizada exitosamente');
        })->name('accounts.update');

        // Desactivar cuenta
        Route::post('/accounts/{account}/deactivate', function (\App\Models\Account $account) {
            // Desactiva usando la columna válida
            $account->active = false;
            $account->save();
            return redirect()->route('accounting.accounts')->with('success', 'Cuenta desactivada');
        })->name('accounts.deactivate');
        
        Route::get('/transactions', function () {
            return view('accounting.transactions');
        })->name('transactions');
        
        Route::get('/transactions/create', function () {
            return view('accounting.transactions-create');
        })->name('transactions.create');
        
        Route::post('/transactions', [\App\Http\Controllers\Accounting\TransactionsController::class, 'store'])
            ->name('transactions.store');
        Route::get('/transactions/{transaction}', [\App\Http\Controllers\Accounting\TransactionsController::class, 'show'])
            ->name('transactions.show');
        Route::get('/transactions/{transaction}/edit', [\App\Http\Controllers\Accounting\TransactionsController::class, 'edit'])
            ->name('transactions.edit');
        Route::put('/transactions/{transaction}', [\App\Http\Controllers\Accounting\TransactionsController::class, 'update'])
            ->name('transactions.update');
        Route::post('/transactions/{transaction}/cancel', [\App\Http\Controllers\Accounting\TransactionsController::class, 'cancel'])
            ->name('transactions.cancel');
    });
    
    // Rutas de facturación
    Route::prefix('invoicing')->name('invoicing.')->group(function () {
        Route::get('/invoices', function () {
            return view('invoicing.invoices');
        })->name('invoices');

        // Exportación CSV de facturas
        Route::get('/invoices/export/csv', [\App\Http\Controllers\ExportController::class, 'invoicesCsv'])
            ->name('invoices.export.csv');
        
        Route::get('/invoices/create', function () {
            return view('invoicing.invoices-create');
        })->name('invoices.create');
        
        Route::post('/invoices', [\App\Http\Controllers\Invoicing\InvoicesController::class, 'store'])
            ->name('invoices.store');

        // Edición y actualización de facturas
        Route::get('/invoices/{invoice}/edit', function (\App\Models\Invoice $invoice) {
            return view('invoicing.invoices-edit', compact('invoice'));
        })->name('invoices.edit');
        Route::put('/invoices/{invoice}', [\App\Http\Controllers\Invoicing\InvoicesController::class, 'update'])
            ->name('invoices.update');

        // Vista/preview de factura
        Route::get('/invoices/{invoice}', function (\App\Models\Invoice $invoice) {
            return view('invoicing.invoices-show', compact('invoice'));
        })->name('invoices.show');

        // Cancelación (soft delete) de factura
        Route::post('/invoices/{invoice}/cancel', function (\App\Models\Invoice $invoice) {
            $invoice->status = 'cancelled';
            $invoice->save();
            return redirect()->route('invoicing.invoices')->with('success', 'Factura cancelada');
        })->name('invoices.cancel');
        
        // Cotizaciones: listado, creación, historial, versionado y conversión
        Route::get('/quotes', [\App\Http\Controllers\Invoicing\QuotesController::class, 'index'])
            ->name('quotes.index');
        Route::get('/quotes/create', function () {
            return view('invoicing.quotes-create');
        })->name('quotes.create');
        Route::post('/quotes', [\App\Http\Controllers\Invoicing\QuotesController::class, 'store'])
            ->name('quotes.store');
        Route::get('/quotes/{quote}/history', [\App\Http\Controllers\Invoicing\QuotesController::class, 'history'])
            ->name('quotes.history');
        Route::post('/quotes/{quote}/version', [\App\Http\Controllers\Invoicing\QuotesController::class, 'version'])
            ->name('quotes.version');
        Route::post('/quotes/{quote}/convert', [\App\Http\Controllers\Invoicing\QuotesController::class, 'convert'])
            ->name('quotes.convert');
    });
    
    // Rutas de nómina
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', function () {
            return view('payroll.index');
        })->name('index');
        
        Route::get('/employees/create', function () {
            return view('payroll.employees-create');
        })->name('employees.create');
        
        Route::post('/employees', function () {
            return redirect()->route('payroll.index')->with('success', 'Empleado creado exitosamente');
        })->name('employees.store');
        
        Route::get('/process', function () {
            return view('payroll.process');
        })->name('process');
        
        Route::post('/process', function () {
            return redirect()->route('payroll.index')->with('success', 'Nómina procesada exitosamente');
        })->name('process.store');
    });
    
    // Rutas de impuestos
    Route::prefix('taxes')->name('taxes.')->group(function () {
        Route::get('/taxes', function () {
            return view('taxes.taxes');
        })->name('taxes');
    });

    // Rutas de contabilidad
    Route::prefix('accounting')->name('accounting.')->group(function () {
        // Mantener rutas existentes, agregamos export CSV para transacciones si no existe
        Route::get('/transactions/export/csv', [\App\Http\Controllers\ExportController::class, 'transactionsCsv'])
            ->name('transactions.export.csv');
        // Exportación CSV de cuentas
        Route::get('/accounts/export/csv', [\App\Http\Controllers\ExportController::class, 'accountsCsv'])
            ->name('accounts.export.csv');
    });
 });
