<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Support\CurrentCompany;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::withCount('items')
            ->latest()
            ->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $company = CurrentCompany::get();
        $customers = Customer::orderBy('name')->get();
        $services = Product::where('type', 'service')->orderBy('name')->get();
        $goods = Product::where('type', 'good')->orderBy('name')->get();

        return view('invoices.create', compact('services', 'goods', 'company', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', Rule::exists('customers', 'id')->where('company_id', CurrentCompany::id())],
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->where('company_id', CurrentCompany::id())],
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string',
        ]);

        $company = CurrentCompany::get();

        $invoice = DB::transaction(function () use ($validated, $company) {
            $subtotal = 0;
            $taxRate = $validated['tax_rate'] ?? 0;

            $invoice = Invoice::create([
                'company_id' => $company->id,
                'customer_id' => $validated['customer_id'] ?? null,
                'invoice_number' => Invoice::generateInvoiceNumber($company->id),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'] ?? null,
                'tax_rate' => $taxRate,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
            ]);

            foreach ($validated['items'] as $item) {
                $lineTotal = round($item['quantity'] * $item['unit_price'], 2);
                $subtotal += $lineTotal;

                $invoice->items()->create([
                    'product_id' => $item['product_id'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $lineTotal,
                ]);
            }

            $invoice->subtotal = $subtotal;
            $invoice->tax_amount = round($subtotal * ($taxRate / 100), 2);
            $invoice->total = $invoice->subtotal + $invoice->tax_amount;
            $invoice->save();

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['items.product', 'company', 'customer', 'payments']);

        return view('invoices.show', ['invoice' => $invoice, 'company' => $invoice->company]);
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items.product');
        $customers = Customer::orderBy('name')->get();
        $services = Product::where('type', 'service')->orderBy('name')->get();
        $goods = Product::where('type', 'good')->orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'services', 'goods', 'customers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', Rule::exists('customers', 'id')->where('company_id', CurrentCompany::id())],
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,sent,paid,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => ['required', Rule::exists('products', 'id')->where('company_id', $invoice->company_id)],
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $invoice) {
            $subtotal = 0;
            $taxRate = $validated['tax_rate'] ?? 0;

            $invoice->update([
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_address' => $validated['customer_address'] ?? null,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'] ?? null,
                'tax_rate' => $taxRate,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
            ]);

            $invoice->items()->delete();

            foreach ($validated['items'] as $item) {
                $lineTotal = round($item['quantity'] * $item['unit_price'], 2);
                $subtotal += $lineTotal;

                $invoice->items()->create([
                    'product_id' => $item['product_id'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $lineTotal,
                ]);
            }

            $invoice->subtotal = $subtotal;
            $invoice->tax_amount = round($subtotal * ($taxRate / 100), 2);
            $invoice->total = $invoice->subtotal + $invoice->tax_amount;
            $invoice->save();
            $invoice->syncPaymentStatus();
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function pdfPreview(Invoice $invoice)
    {
        $data = $this->getPdfViewData($invoice);
        $html = view('invoices.pdf', $data)->render();

        return view('invoices.pdf-preview', array_merge($data, compact('html')));
    }

    public function pdfPreviewHtml(Invoice $invoice)
    {
        return response(view('invoices.pdf', $this->getPdfViewData($invoice))->render())
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function pdf(Request $request, Invoice $invoice)
    {
        if ($request->filled('html')) {
            $pdf = Pdf::loadHTML($request->input('html'))
                ->setPaper('a4', 'portrait');
        } else {
            $pdf = Pdf::loadView('invoices.pdf', $this->getPdfViewData($invoice))
                ->setPaper('a4', 'portrait');
        }

        return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
    }

    private function getPdfViewData(Invoice $invoice): array
    {
        $invoice->load(['items.product', 'company', 'customer', 'payments']);
        $invoice->refresh();

        $company = $invoice->company;
        $amountPaid = round((float) $invoice->amount_paid, 2);
        $balanceDue = $invoice->getBalanceDue();
        $paymentStatus = $invoice->payment_status ?? 'unpaid';

        return compact('invoice', 'company', 'amountPaid', 'balanceDue', 'paymentStatus');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
