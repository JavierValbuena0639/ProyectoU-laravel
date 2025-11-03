<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionsController extends Controller
{
    /**
     * Store a new transaction.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'reference' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'account' => ['required', 'string'], // account code from select
            'type' => ['required', 'in:debit,credit'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,completed,cancelled'],
        ]);

        $domain = method_exists($user, 'emailDomain') ? $user->emailDomain() : ($user->email_domain ?? null);

        // Map account code to account_id within domain
        $account = Account::forDomain($domain)->where('code', $validated['account'])->first();
        if (!$account) {
            return redirect()->back()->withErrors(['account' => 'La cuenta seleccionada no existe en su dominio'])->withInput();
        }

        // Generate unique voucher number
        $voucherNumber = $this->generateVoucherNumber();

        // Map status to DB enum
        $statusMap = [
            'pending' => 'draft',
            'completed' => 'posted',
            'cancelled' => 'cancelled',
        ];

        $debit = $validated['type'] === 'debit' ? (float) $validated['amount'] : 0.0;
        $credit = $validated['type'] === 'credit' ? (float) $validated['amount'] : 0.0;

        $transaction = new Transaction([
            'voucher_number' => $voucherNumber,
            'voucher_type' => 'diario', // asiento general
            'transaction_date' => $validated['date'],
            'description' => $validated['description'],
            'account_id' => $account->id,
            'user_id' => $user->id,
            'debit_amount' => $debit,
            'credit_amount' => $credit,
            'reference' => $validated['reference'],
            'status' => $statusMap[$validated['status']] ?? 'draft',
        ]);
        $transaction->save();

        return redirect()->route('accounting.transactions')->with('success', 'Transacción creada exitosamente');
    }

    /**
     * Show transaction details.
     */
    public function show(Transaction $transaction)
    {
        return view('accounting.transactions-show', compact('transaction'));
    }

    /**
     * Edit transaction form.
     */
    public function edit(Transaction $transaction)
    {
        return view('accounting.transactions-edit', compact('transaction'));
    }

    /**
     * Update transaction.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'reference' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'account' => ['required', 'string'],
            'type' => ['required', 'in:debit,credit'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,completed,cancelled'],
        ]);

        $domain = method_exists($user, 'emailDomain') ? $user->emailDomain() : ($user->email_domain ?? null);
        $account = Account::forDomain($domain)->where('code', $validated['account'])->first();
        if (!$account) {
            return redirect()->back()->withErrors(['account' => 'La cuenta seleccionada no existe en su dominio'])->withInput();
        }

        $statusMap = [
            'pending' => 'draft',
            'completed' => 'posted',
            'cancelled' => 'cancelled',
        ];

        $transaction->transaction_date = $validated['date'];
        $transaction->reference = $validated['reference'];
        $transaction->description = $validated['description'];
        $transaction->account_id = $account->id;
        if ($validated['type'] === 'debit') {
            $transaction->debit_amount = (float) $validated['amount'];
            $transaction->credit_amount = 0.0;
        } else {
            $transaction->credit_amount = (float) $validated['amount'];
            $transaction->debit_amount = 0.0;
        }
        $transaction->status = $statusMap[$validated['status']] ?? 'draft';
        $transaction->save();

        return redirect()->route('accounting.transactions')->with('success', 'Transacción actualizada');
    }

    /**
     * Cancel transaction (soft).
     */
    public function cancel(Transaction $transaction)
    {
        $transaction->status = 'cancelled';
        $transaction->save();
        return redirect()->route('accounting.transactions')->with('success', 'Transacción cancelada');
    }

    private function generateVoucherNumber(): string
    {
        $base = 'TRX-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(4));
        // Ensure uniqueness
        while (Transaction::where('voucher_number', $base)->exists()) {
            $base = 'TRX-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(5));
        }
        return $base;
    }
}