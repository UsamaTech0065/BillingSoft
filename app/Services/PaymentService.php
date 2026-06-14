<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

use function money;

class PaymentService
{
    public function recordInvoicePayment(Invoice $invoice, array $data): Payment
    {
        if ($invoice->payment_status === 'paid') {
            throw new InvalidArgumentException('Invoice is already fully paid.');
        }

        $amount = round((float) $data['amount'], 2);
        $balanceDue = $invoice->getBalanceDue();

        if ($amount <= 0) {
            throw new InvalidArgumentException('Payment amount must be greater than zero.');
        }

        if ($amount > $balanceDue) {
            throw new InvalidArgumentException('Payment cannot exceed balance due ('.$this->formatForInvoice($invoice, $balanceDue).').');
        }

        if (! $invoice->customer_id) {
            throw new InvalidArgumentException('Invoice must be linked to a customer to record payments.');
        }

        return DB::transaction(function () use ($invoice, $data, $amount) {
            $payment = Payment::create([
                'company_id' => $invoice->company_id,
                'customer_id' => $invoice->customer_id,
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'method' => $data['method'],
                'payment_date' => $data['payment_date'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $invoice->recordPayment($amount);

            return $payment;
        });
    }

    public function recordAdvancePayment(Customer $customer, array $data): Payment
    {
        $amount = round((float) $data['amount'], 2);

        if ($amount <= 0) {
            throw new InvalidArgumentException('Advance amount must be greater than zero.');
        }

        return DB::transaction(function () use ($customer, $data, $amount) {
            $payment = Payment::create([
                'company_id' => $customer->company_id,
                'customer_id' => $customer->id,
                'invoice_id' => null,
                'amount' => $amount,
                'method' => $data['method'],
                'payment_date' => $data['payment_date'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? 'Advance payment',
            ]);

            $customer->addAdvance($amount);

            return $payment;
        });
    }

    public function applyAdvanceToInvoice(Invoice $invoice, float $amount): Payment
    {
        if ($invoice->payment_status === 'paid') {
            throw new InvalidArgumentException('Invoice is already fully paid.');
        }

        if (! $invoice->customer_id) {
            throw new InvalidArgumentException('Invoice must be linked to a customer.');
        }

        $amount = round($amount, 2);
        $balanceDue = $invoice->getBalanceDue();
        $customer = $invoice->customer;

        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be greater than zero.');
        }

        if ($amount > $balanceDue) {
            throw new InvalidArgumentException('Amount cannot exceed balance due.');
        }

        if ($amount > (float) $customer->advance_balance) {
            throw new InvalidArgumentException('Insufficient advance balance ('.$this->formatForCustomer($customer, $customer->advance_balance).' available).');
        }

        return DB::transaction(function () use ($invoice, $customer, $amount) {
            $payment = Payment::create([
                'company_id' => $invoice->company_id,
                'customer_id' => $customer->id,
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'method' => 'advance',
                'payment_date' => now()->toDateString(),
                'notes' => 'Applied from customer advance balance',
            ]);

            $customer->useAdvance($amount);
            $invoice->recordPayment($amount);

            return $payment;
        });
    }

    private function formatForInvoice(Invoice $invoice, float $amount): string
    {
        $invoice->loadMissing('company');

        return ($invoice->company ?? Company::find($invoice->company_id))?->formatMoney($amount) ?? money($amount);
    }

    private function formatForCustomer(Customer $customer, float $amount): string
    {
        return Company::find($customer->company_id)?->formatMoney($amount) ?? money($amount);
    }
}
