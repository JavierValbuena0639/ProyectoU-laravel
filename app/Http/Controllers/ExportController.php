<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function invoicesCsv(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }
        $domain = method_exists($user, 'emailDomain') ? $user->emailDomain() : ($user->email_domain ?? null);

        $query = Invoice::query()
            ->whereHas('user', function ($q) use ($domain) {
                $q->where('email_domain', $domain);
            });

        // Optional filters
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($client = $request->string('client')->toString()) {
            $query->where(function ($q) use ($client) {
                $q->where('client_name', 'like', "%$client%")
                  ->orWhere('client_email', 'like', "%$client%");
            });
        }
        if ($request->filled(['from', 'to'])) {
            $query->whereBetween('invoice_date', [$request->date('from')->toDateString(), $request->date('to')->toDateString()]);
        } elseif ($request->filled('from')) {
            $query->where('invoice_date', '>=', $request->date('from')->toDateString());
        } elseif ($request->filled('to')) {
            $query->where('invoice_date', '<=', $request->date('to')->toDateString());
        }

        $filename = 'invoices-' . now()->format('Ymd-His') . '.csv';
        $response = new StreamedResponse(function () use ($query) {
            $out = fopen('php://output', 'w');
            // Header
            fputcsv($out, ['Invoice Number', 'Client', 'Email', 'Invoice Date', 'Due Date', 'Status', 'Total Amount']);
            $query->orderBy('invoice_date', 'desc')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $invoice) {
                    fputcsv($out, [
                        $invoice->invoice_number,
                        $invoice->client_name,
                        $invoice->client_email,
                        optional($invoice->invoice_date)->toDateString(),
                        optional($invoice->due_date)->toDateString(),
                        $invoice->status,
                        $invoice->total_amount,
                    ]);
                }
            });
            fclose($out);
        });

        return $response
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function transactionsCsv(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }
        $domain = method_exists($user, 'emailDomain') ? $user->emailDomain() : ($user->email_domain ?? null);

        $query = Transaction::query()
            ->with('account')
            ->whereHas('account', function ($q) use ($domain) {
                $q->where('service_domain', $domain);
            });

        // Optional filters
        if ($type = $request->string('type')->toString()) {
            $query->where('voucher_type', $type);
        }
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($request->filled(['from', 'to'])) {
            $query->whereBetween('transaction_date', [$request->date('from')->toDateString(), $request->date('to')->toDateString()]);
        } elseif ($request->filled('from')) {
            $query->where('transaction_date', '>=', $request->date('from')->toDateString());
        } elseif ($request->filled('to')) {
            $query->where('transaction_date', '<=', $request->date('to')->toDateString());
        }

        $filename = 'transactions-' . now()->format('Ymd-His') . '.csv';
        $response = new StreamedResponse(function () use ($query) {
            $out = fopen('php://output', 'w');
            // Header
            fputcsv($out, ['Voucher Number', 'Voucher Type', 'Date', 'Reference', 'Description', 'Account Code', 'Account Name', 'Debit', 'Credit', 'Status']);
            $query->orderBy('transaction_date', 'desc')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $trx) {
                    fputcsv($out, [
                        $trx->voucher_number,
                        $trx->voucher_type,
                        optional($trx->transaction_date)->toDateString(),
                        $trx->reference,
                        $trx->description,
                        optional($trx->account)->code,
                        optional($trx->account)->name,
                        $trx->debit_amount,
                        $trx->credit_amount,
                        $trx->status,
                    ]);
                }
            });
            fclose($out);
        });

        return $response
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}