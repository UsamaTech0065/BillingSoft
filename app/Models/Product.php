<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['company_id', 'name', 'description', 'type', 'price', 'unit'])]
class Product extends Model
{
    use BelongsToCompany;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function isService(): bool
    {
        return $this->type === 'service';
    }

    public function isGood(): bool
    {
        return $this->type === 'good';
    }
}
