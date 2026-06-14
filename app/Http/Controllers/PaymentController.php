<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Support\CurrentCompany;

use function money;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['customer', 'invoice'])->latest();

        if ($request->filled('type')) {
            if ($request->type === 'advance') {
                $query->whereNull('invoice_id');
            } elseif ($request->type === 'invoice') {
                $query->whereNotNull('invoice_id');
            }
        }

        $payments = $query->paginate(20);
        $totalReceived = Payment::whereNotNull('invoice_id')->sum('amount');
        $totalAdvance = Payment::whereNull('invoice_id')->sum('amount');

        return view('payments.index', compact('payments', 'totalReceived', 'totalAdvance'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $invoices = Invoice::with('customer')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderByDesc('issue_date')
            ->get();

        return view('payments.create', compact('customers', 'invoices'));
    }

    public function store(Request $request, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'payment_type' => 'required|in:invoice,advance',
            'customer_id' => ['required', Rule::exists('customers', 'id')->where('company_id', CurrentCompany::id())],
            'invoice_id' => 'nullable|required_if:payment_type,invoice|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,online',
            'payment_date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            if ($validated['payment_type'] === 'advance') {
                $customer = Customer::findOrFail($validated['customer_id']);
                $paymentService->recordAdvancePayment($customer, $validated);

                return redirect()->route('payments.index')
                    ->with('success', 'Advance payment of '.money($validated['amount']).' recorded.');
            }

            $invoice = Invoice::findOrFail($validated['invoice_id']);
            $paymentService->recordInvoicePayment($invoice, $validated);

            return redirect()->route('payments.index')
                ->with('success', 'Payment of '.money($validated['amount']).' recorded for '.$invoice->invoice_number.'.');
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function storeForInvoice(Request $request, Invoice $invoice, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,online',
            'payment_date' => 'required|date',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $paymentService->recordInvoicePayment($invoice, $validated);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Payment of '.money($validated['amount'], $invoice->company).' recorded successfully.');
    }

    public function applyAdvance(Request $request, Invoice $invoice, PaymentService $paymentService)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $paymentService->applyAdvanceToInvoice($invoice, (float) $validated['amount']);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Advance of '.money($validated['amount'], $invoice->company).' applied to invoice.');
    }
}
