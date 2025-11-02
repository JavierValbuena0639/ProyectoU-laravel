<?php

namespace App\Http\Controllers\Invoicing;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\QuoteVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotesController extends Controller
{
    public function index(Request $request)
    {
        $domain = Auth::user()->email_domain ?? null;
        $quotes = Quote::where('email_domain', $domain)
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->paginate(15);

        return view('invoicing.quotes-index', compact('quotes'));
    }

    public function store(Request $request)
    {
        $domain = Auth::user()->email_domain ?? null;
        $userId = Auth::id();

        $data = $request->validate([
            'quote_number' => 'required|string|max:50|unique:quotes,quote_number',
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email',
            'client_company' => 'nullable|string|max:255',
            'client_address' => 'nullable|string',
            'client_document' => 'nullable|string|max:100',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:issue_date',
            'project_description' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'terms' => 'nullable|string',
        ]);

        $subtotal = 0;
        foreach ($data['items'] as $it) {
            $subtotal += ((float)$it['quantity']) * ((float)$it['price']);
        }
        $taxAmount = round($subtotal * 0.16, 2); // IVA 16%
        $totalAmount = round($subtotal + $taxAmount, 2);

        $quote = Quote::create([
            'quote_number' => $data['quote_number'],
            'user_id' => $userId,
            'email_domain' => $domain,
            'client_name' => $data['client_name'],
            'client_document' => $data['client_document'] ?? 'N/A',
            'client_email' => $data['client_email'] ?? null,
            'client_company' => $data['client_company'] ?? null,
            'client_address' => $data['client_address'] ?? null,
            'issue_date' => $data['issue_date'],
            'valid_until' => $data['valid_until'],
            'project_description' => $data['project_description'],
            'items' => $data['items'],
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'status' => 'draft',
        ]);

        QuoteVersion::create([
            'quote_id' => $quote->id,
            'version' => 1,
            'user_id' => $userId,
            'email_domain' => $domain,
            'change_reason' => 'Creación inicial',
            'snapshot' => $quote->toArray(),
        ]);

        return redirect()->route('invoicing.quotes.history', $quote->id)
            ->with('status', 'Cotización creada y versión 1 registrada.');
    }

    public function history(Request $request, Quote $quote)
    {
        $this->authorizeDomain($quote);
        $versions = $quote->versions()->orderByDesc('version')->get();
        return view('invoicing.quotes-history', compact('quote', 'versions'));
    }

    public function version(Request $request, Quote $quote)
    {
        $this->authorizeDomain($quote);
        $data = $request->validate([
            'change_reason' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'client_email' => 'nullable|email',
            'client_company' => 'nullable|string|max:255',
            'client_address' => 'nullable|string',
            'client_document' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date',
            'valid_until' => 'nullable|date',
            'project_description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.description' => 'required_with:items|string',
            'items.*.quantity' => 'required_with:items|numeric|min:1',
            'items.*.price' => 'required_with:items|numeric|min:0',
        ]);

        DB::transaction(function () use ($quote, $data) {
            $quote->fill(array_filter($data, fn($v) => !is_null($v)));

            if (!empty($data['items'])) {
                $subtotal = 0;
                foreach ($data['items'] as $it) {
                    $subtotal += ((float)$it['quantity']) * ((float)$it['price']);
                }
                $taxAmount = round($subtotal * 0.16, 2);
                $totalAmount = round($subtotal + $taxAmount, 2);
                $quote->subtotal = $subtotal;
                $quote->tax_amount = $taxAmount;
                $quote->total_amount = $totalAmount;
            }

            $quote->save();

            $nextVersion = (int)($quote->versions()->max('version')) + 1;
            QuoteVersion::create([
                'quote_id' => $quote->id,
                'version' => $nextVersion,
                'user_id' => Auth::id(),
                'email_domain' => Auth::user()->email_domain ?? null,
                'change_reason' => $data['change_reason'] ?? 'Actualización',
                'snapshot' => $quote->toArray(),
            ]);
        });

        return redirect()->route('invoicing.quotes.history', $quote->id)
            ->with('status', 'Nueva versión registrada.');
    }

    public function convert(Request $request, Quote $quote)
    {
        $this->authorizeDomain($quote);

        $invoice = Invoice::create([
            'quote_id' => $quote->id,
            'invoice_number' => 'FAC-' . date('Y') . '-' . str_pad((string)rand(1, 99999), 5, '0', STR_PAD_LEFT),
            'user_id' => Auth::id(),
            'client_name' => $quote->client_name,
            'client_document' => $quote->client_document ?: 'N/A',
            'client_email' => $quote->client_email,
            'client_address' => $quote->client_address,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => $quote->subtotal,
            'tax_amount' => $quote->tax_amount,
            'retention_amount' => 0,
            'total_amount' => $quote->total_amount,
            'paid_amount' => 0,
            'status' => 'draft',
            'notes' => 'Convertida desde cotización ' . $quote->quote_number,
            'items' => $quote->items,
        ]);

        $quote->status = 'converted';
        $quote->save();

        return redirect()->route('invoicing.invoices')
            ->with('status', 'Cotización convertida a factura: ' . $invoice->invoice_number);
    }

    private function authorizeDomain(Quote $quote): void
    {
        $domain = Auth::user()->email_domain ?? null;
        if ($quote->email_domain !== $domain) {
            abort(403, 'No autorizado para este dominio');
        }
    }
}