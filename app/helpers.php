<?php

use App\Models\Company;
use App\Support\CurrentCompany;

if (! function_exists('money')) {
    function money(float|int|string|null $amount, ?Company $company = null): string
    {
        $company ??= CurrentCompany::get();

        return $company->formatMoney($amount);
    }
}
