<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'company_name', 'tagline', 'email', 'phone', 'website',
    'address', 'city', 'state', 'zip', 'country', 'tax_id',
    'currency_code', 'currency_symbol', 'decimal_places', 'currency_position',
    'logo_path', 'footer_message',
])]
class Company extends Model
{
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getLogoUrl(): ?string
    {
        if (! $this->logo_path || ! Storage::disk('public')->exists($this->logo_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    public function getLogoBase64(): ?string
    {
        if (! $this->logo_path || ! Storage::disk('public')->exists($this->logo_path)) {
            return null;
        }

        $path = Storage::disk('public')->path($this->logo_path);
        $mime = mime_content_type($path) ?: 'image/png';
        $data = base64_encode(file_get_contents($path));

        return "data:{$mime};base64,{$data}";
    }

    public function getAddressLine(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip,
            $this->country,
        ]));
    }

    public function formatMoney(float|int|string|null $amount): string
    {
        $decimals = (int) ($this->decimal_places ?? 2);
        $value = number_format(round((float) $amount, $decimals), $decimals);
        $symbol = $this->currency_symbol ?: '$';

        if (($this->currency_position ?? 'before') === 'after') {
            return $value.' '.$symbol;
        }

        return $symbol.$value;
    }

    public function getCurrencyLabel(): string
    {
        $code = $this->currency_code ?? 'USD';
        $symbol = $this->currency_symbol ?? '$';

        return "{$code} ({$symbol})";
    }
}
