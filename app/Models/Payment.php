<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'company_id', 'customer_id', 'invoice_id', 'amount', 'method',
    'payment_date', 'reference', 'notes',
])]
class Payment extends Model
{
    use BelongsToCompany;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function isAdvance(): bool
    {
        return is_null($this->invoice_id);
    }

    public function getMethodLabel(): string
    {
        return match ($this->method) {
            'cash' => 'Cash',
            'online' => 'Online',
            'advance' => 'Advance Credit',
            default => ucfirst($this->method),
        };
    }
}
