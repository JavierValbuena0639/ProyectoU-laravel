<?php

namespace App\Http\Controllers\Invoicing;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoicesController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        $validated = $request->validate([
            // Permitimos número opcional y garantizamos unicidad de manera programática
            'invoice_number' => 'nullable|string|max:255',
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_document' => 'required|string|max:100',
            'client_address' => 'nullable|string',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate totals based on items
        $subtotal = 0.0;
        foreach ($validated['items'] as $item) {
            $qty = (float)($item['quantity'] ?? 0);
            $price = (float)($item['price'] ?? 0);
            $subtotal += $qty * $price;
        }
        $taxAmount = round($subtotal * 0.16, 2); // IVA 16% según UI
        $retentionAmount = 0.0; // por ahora 0, se puede ajustar luego
        $totalAmount = round($subtotal + $taxAmount - $retentionAmount, 2);

        // Generar número único si falta o existe ya en la BD
        $inputNumber = (string)($validated['invoice_number'] ?? '');
        $invoiceNumber = trim($inputNumber) !== '' ? $inputNumber : sprintf('FAC-%s-%04d', now()->year, random_int(0, 9999));
        $tries = 0;
        while (Invoice::where('invoice_number', $invoiceNumber)->exists() && $tries < 10) {
            $invoiceNumber = sprintf('FAC-%s-%04d', now()->year, random_int(0, 9999));
            $tries++;
        }
        if (Invoice::where('invoice_number', $invoiceNumber)->exists()) {
            // Fallback ultra-único basado en timestamp
            $invoiceNumber = 'FAC-' . now()->format('YmdHis');
        }

        // Persist invoice
        $invoice = new Invoice();
        $invoice->invoice_number = $invoiceNumber;
        $invoice->user_id = $user->id;
        $invoice->client_name = $validated['client_name'];
        $invoice->client_document = $validated['client_document'];
        $invoice->client_email = $validated['client_email'] ?? null;
        $invoice->client_address = $validated['client_address'] ?? null;
        $invoice->invoice_date = $validated['invoice_date'];
        $invoice->due_date = $validated['due_date'];
        $invoice->subtotal = $subtotal;
        $invoice->tax_amount = $taxAmount;
        $invoice->retention_amount = $retentionAmount;
        $invoice->total_amount = $totalAmount;
        $invoice->paid_amount = 0.0;
        $invoice->status = 'draft';
        $invoice->notes = $validated['notes'] ?? null;
        $invoice->items = $validated['items'];
        $invoice->save();

        return redirect()->route('invoicing.invoices')
            ->with('success', 'Factura creada exitosamente');
    }

    public function update(Request $request, Invoice $invoice)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,' . $invoice->id,
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_document' => 'required|string|max:100',
            'client_address' => 'nullable|string',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        // Recalculate totals
        $subtotal = 0.0;
        foreach ($validated['items'] as $item) {
            $qty = (float)($item['quantity'] ?? 0);
            $price = (float)($item['price'] ?? 0);
            $subtotal += $qty * $price;
        }
        $taxAmount = round($subtotal * 0.16, 2);
        $retentionAmount = 0.0;
        $totalAmount = round($subtotal + $taxAmount - $retentionAmount, 2);

        // Update invoice
        $invoice->invoice_number = $validated['invoice_number'];
        $invoice->client_name = $validated['client_name'];
        $invoice->client_document = $validated['client_document'];
        $invoice->client_email = $validated['client_email'] ?? null;
        $invoice->client_address = $validated['client_address'] ?? null;
        $invoice->invoice_date = $validated['invoice_date'];
        $invoice->due_date = $validated['due_date'];
        $invoice->subtotal = $subtotal;
        $invoice->tax_amount = $taxAmount;
        $invoice->retention_amount = $retentionAmount;
        $invoice->total_amount = $totalAmount;
        $invoice->notes = $validated['notes'] ?? null;
        $invoice->items = $validated['items'];
        if (!empty($validated['status'])) {
            $invoice->status = $validated['status'];
        }
        $invoice->save();

        return redirect()->route('invoicing.invoices')
            ->with('success', 'Factura actualizada exitosamente');
    }
}