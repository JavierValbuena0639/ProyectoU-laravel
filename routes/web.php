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
        
        Route::get('/transactions', function () {
            return view('accounting.transactions');
        })->name('transactions');
        
        Route::get('/transactions/create', function () {
            return view('accounting.transactions-create');
        })->name('transactions.create');
        
        Route::post('/transactions', function () {
            // Aquí iría la lógica para guardar la transacción
            return redirect()->route('accounting.transactions')->with('success', 'Transacción creada exitosamente');
        })->name('transactions.store');
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
        
        Route::post('/invoices', function () {
            return redirect()->route('invoicing.invoices')->with('success', 'Factura creada exitosamente');
        })->name('invoices.store');
        
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
    });
 });
