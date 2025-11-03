<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DatabaseController as AdminDatabaseController;
use App\Http\Controllers\Admin\SystemController as AdminSystemController;
use App\Http\Controllers\Admin\FeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;

// Ruta principal - muestra panel de bienvenida y redirige a /login en 30s
Route::get('/', function () {
    return view('welcome');
});

// Cambio de idioma accesible para cualquier usuario (invitado o autenticado)
Route::get('/locale/{lang}', [\App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');

// Rutas de autenticación
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

// Desafío 2FA tras credenciales válidas
Route::get('/two-factor', [App\Http\Controllers\Auth\TwoFactorLoginController::class, 'show'])
    ->name('auth.twofa.show')
    ->middleware('guest');
Route::post('/two-factor', [App\Http\Controllers\Auth\TwoFactorLoginController::class, 'verify'])
    ->name('auth.twofa.submit')
    ->middleware('guest');

// Envío de enlace de recuperación para el administrador
Route::post('/password/forgot-admin', [LoginController::class, 'sendAdminResetLink'])
    ->name('password.forgot_admin')
    ->middleware('guest');

// Envío de enlace de recuperación para cualquier email (usuario/admin)
Route::post('/password/email', [LoginController::class, 'sendResetLink'])
    ->name('password.email')
    ->middleware('guest');

// Restablecimiento de contraseña (formulario y acción)
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset')
    ->middleware('guest');
Route::post('/password/reset', [PasswordResetController::class, 'reset'])
    ->name('password.update')
    ->middleware('guest');

// Registro de administradores (acceso invitado)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

// Verificación por código (usuario autenticado, sin exigir verificación previa)
Route::middleware('auth')->group(function () {
    Route::get('/verify', [\App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('auth.verify.show');
    Route::post('/verify', [\App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('auth.verify.submit');
    Route::post('/verify/resend', [\App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('auth.verify.resend');
    Route::get('/verify/status', [\App\Http\Controllers\Auth\VerificationController::class, 'status'])->name('auth.verify.status');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

// Rutas protegidas
Route::middleware(['auth', 'inactive', 'verified_code'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user && method_exists($user, 'isSupport') && $user->isSupport()) {
            return redirect()->route('admin.database');
        }
        return view('dashboard');
    })->name('dashboard');

    // Cambio de idioma: usa la ruta global 'locale.switch'
    
    // Rutas de administración (solo admin)
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Cambio de idioma en sección admin: utiliza la ruta global 'locale.switch'

        // Gestión de usuarios (controlador)
        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');
        Route::post('/users/{user}/resend-code', [AdminUserController::class, 'resendVerificationCode'])->name('users.resend_code');
        // 2FA para usuarios (admin)
        Route::get('/users/{user}/2fa', [\App\Http\Controllers\Admin\TwoFactorController::class, 'show'])->name('users.2fa');
        Route::post('/users/{user}/2fa/verify', [\App\Http\Controllers\Admin\TwoFactorController::class, 'verify'])->name('users.2fa.verify');
        Route::post('/users/{user}/2fa/disable', [\App\Http\Controllers\Admin\TwoFactorController::class, 'disable'])->name('users.2fa.disable');
        
        // Rutas para gestión de roles
        Route::get('/roles', function () {
            return view('admin.roles');
        })->name('roles');
        
        Route::get('/roles/create', function () {
            return view('admin.roles-create');
        })->name('roles.create');
        
        Route::post('/roles', function () {
            // Lógica para crear rol
            return redirect()->route('admin.roles')->with('success', 'Rol creado exitosamente');
        })->name('roles.store');
        
        // Rutas para configuración
        Route::get('/config', function () {
            return view('admin.config');
        })->name('config');
        Route::post('/config/save', [\App\Http\Controllers\Admin\SettingsController::class, 'save'])->name('config.save');
        Route::post('/config/unsubscribe', [\App\Http\Controllers\Admin\SettingsController::class, 'unsubscribe'])->name('config.unsubscribe');

        // Base de datos: movido fuera del grupo admin para permitir acceso a soporte

        // Facturación Electrónica (DIAN) - Configuración (solo admin)
        Route::get('/fe/config', [FeController::class, 'index'])->name('fe.config');
        Route::post('/fe/config/save', [FeController::class, 'save'])->name('fe.config.save');
        // Envío de facturas a DIAN (solo admin)
        Route::post('/fe/send/{invoice}', [FeController::class, 'sendInvoice'])->name('fe.invoice.send');
        // Prueba de habilitación DIAN (sandbox)
        Route::post('/fe/test', [FeController::class, 'test'])->name('fe.test');
        
        // Rutas para reportes
        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('reports');
        Route::get('/reports/export/{format}', [\App\Http\Controllers\Admin\ReportsController::class, 'export'])
            ->name('reports.export');

        // Logs de seguridad (admin)
        Route::get('/security-logs', [\App\Http\Controllers\Admin\SecurityLogsController::class, 'index'])
            ->name('security.logs');
        Route::get('/security-logs/export/csv', [\App\Http\Controllers\Admin\SecurityLogsController::class, 'exportCsv'])
            ->name('security.logs.export.csv');
    });

    // Base de datos (compartido admin o soporte)
    Route::middleware(['admin_or_support'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/database', [AdminDatabaseController::class, 'index'])->name('database');
        Route::get('/system/verify', [AdminSystemController::class, 'verify'])->name('system.verify');
    });

    // Exportación de reportes accesible para cualquier usuario autenticado (no admin)
    Route::get('/reports/export/{format}', [\App\Http\Controllers\Admin\ReportsController::class, 'export'])
        ->name('reports.export');

    // Eliminación de reportes recientes (almacenados en sesión)
    Route::post('/reports/delete/{id}', [\App\Http\Controllers\Admin\ReportsController::class, 'delete'])
        ->name('reports.delete');

    // Acciones de BD solo para soporte interno
    Route::middleware(['support'])->prefix('admin')->name('admin.')->group(function () {
        Route::post('/database/test', [AdminDatabaseController::class, 'testConnection'])->name('database.test');
        Route::post('/database/save', [AdminDatabaseController::class, 'saveConnection'])->name('database.save');
        // Respaldos
        Route::post('/database/backups/create', [AdminDatabaseController::class, 'createBackup'])->name('database.backups.create');
        Route::get('/database/backups/download/{file}', [AdminDatabaseController::class, 'downloadBackup'])->name('database.backups.download');
        Route::post('/database/backups/delete/{file}', [AdminDatabaseController::class, 'deleteBackup'])->name('database.backups.delete');
        Route::post('/database/backups/toggle', [AdminDatabaseController::class, 'toggleAutoBackup'])->name('database.backups.toggle');
        // Migraciones y mantenimiento
        Route::post('/database/migrate', [AdminDatabaseController::class, 'runMigrations'])->name('database.migrate');
        Route::post('/database/rollback', [AdminDatabaseController::class, 'rollbackMigrations'])->name('database.rollback');
        Route::post('/database/optimize', [AdminDatabaseController::class, 'optimizeDatabase'])->name('database.optimize');
        Route::post('/database/cache/clear', [AdminDatabaseController::class, 'clearCache'])->name('database.cache.clear');
    });
    
    // Rutas de contabilidad
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/accounts', function () {
            $domain = \Illuminate\Support\Facades\Auth::user()->emailDomain();
            $accounts = \App\Models\Account::forDomain($domain)->get();
            return view('accounting.accounts', compact('accounts'));
        })->name('accounts');
        
        Route::get('/accounts/create', function () {
            return view('accounting.accounts-create');
        })->name('accounts.create');
        
        Route::post('/accounts', function (\Illuminate\Http\Request $request) {
            // Validación básica
            $validated = $request->validate([
                'account_code' => ['required', 'string', 'max:10', \Illuminate\Validation\Rule::unique('accounts', 'code')],
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

            // Confirmación de dominio (si se envía desde el formulario)
            $userDomain = \Illuminate\Support\Facades\Auth::user()->emailDomain();
            if (!empty($validated['confirm_domain']) && $validated['confirm_domain'] !== $userDomain) {
                return back()->withErrors(['confirm_domain' => 'El dominio confirmado no coincide con su dominio de servicio.'])->withInput();
            }

            // Mapear tipos del formulario a los enumerados de la BD
            $typeInput = $validated['account_type'];
            $typeMap = [
                'capital' => 'patrimonio',
                'ingresos' => 'ingreso',
                'gastos' => 'gasto',
                'costos' => 'costo',
            ];
            $type = $typeMap[$typeInput] ?? $typeInput; // activo/pasivo pasan directo

            // Mapear saldo normal
            $nature = $validated['normal_balance'] === 'deudor' ? 'debito' : 'credito';

            // Resolver cuenta padre por código si existe
            $parentId = null;
            if (!empty($validated['parent_account'])) {
                $parent = \App\Models\Account::where('code', $validated['parent_account'])->first();
                $parentId = $parent?->id;
            }

            // Verificar unicidad de nombre dentro del dominio activo
            $existingByName = \App\Models\Account::where('service_domain', $userDomain)
                ->where('name', $validated['account_name'])
                ->where('active', true)
                ->exists();

            if ($existingByName) {
                return back()
                    ->withErrors(['account_name' => 'Ya existe una cuenta activa con este nombre en su servicio.'])
                    ->withInput();
            }

            // Crear cuenta con dominio del usuario autenticado
            $account = \App\Models\Account::create([
                'code' => $validated['account_code'],
                'name' => $validated['account_name'],
                'type' => $type,
                'nature' => $nature,
                'parent_id' => $parentId,
                'level' => (int) $validated['level'],
                'balance' => (float) ($validated['initial_balance'] ?? 0),
                'active' => $request->boolean('is_active'),
                'accepts_movements' => true,
                'description' => $validated['description'] ?? null,
                'service_domain' => $userDomain,
                'created_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

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
