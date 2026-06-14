<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['company_id', 'name', 'email', 'phone', 'address', 'notes', 'advance_balance'])]
class Customer extends Model
{
    use BelongsToCompany;

    protected function casts(): array
    {
        return [
            'advance_balance' => 'decimal:2',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function addAdvance(float $amount): void
    {
        $this->advance_balance = $this->advance_balance + $amount;
        $this->save();
    }

    public function useAdvance(float $amount): void
    {
        $this->advance_balance = max(0, $this->advance_balance - $amount);
        $this->save();
    }
}
