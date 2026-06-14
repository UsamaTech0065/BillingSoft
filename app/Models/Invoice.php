<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'company_id', 'customer_id', 'invoice_number', 'customer_name', 'customer_email', 'customer_address',
    'issue_date', 'due_date', 'subtotal', 'tax_rate', 'tax_amount', 'total',
    'amount_paid', 'payment_status', 'notes', 'status',
])]
class Invoice extends Model
{
    use BelongsToCompany;

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceDue(): float
    {
        return max(0, round((float) $this->total - (float) $this->amount_paid, 2));
    }

    public function recordPayment(float $amount): void
    {
        $this->amount_paid = round((float) $this->amount_paid + $amount, 2);
        $this->syncPaymentStatus();
    }

    public function syncPaymentStatus(): void
    {
        if ($this->amount_paid >= $this->total) {
            $this->payment_status = 'paid';
            if ($this->status !== 'cancelled') {
                $this->status = 'paid';
            }
        } elseif ($this->amount_paid > 0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'unpaid';
        }

        $this->save();
    }

    public static function generateInvoiceNumber(?int $companyId = null): string
    {
        $year = now()->format('Y');
        $query = static::withoutGlobalScope('company')
            ->where('invoice_number', 'like', "INV-{$year}-%");

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $last = $query->orderByDesc('id')->first();

        if ($last) {
            $lastNumber = (int) substr($last->invoice_number, -4);
            $next = $lastNumber + 1;
        } else {
            $next = 1;
        }

        return sprintf('INV-%s-%04d', $year, $next);
    }

    public function recalculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total');
        $this->tax_amount = round($this->subtotal * ($this->tax_rate / 100), 2);
        $this->total = $this->subtotal + $this->tax_amount;
        $this->syncPaymentStatus();
    }
}
