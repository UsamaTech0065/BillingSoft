<?php

namespace App\Support;

use App\Models\Company;
use Illuminate\Support\Facades\Session;

class CurrentCompany
{
    public const SESSION_KEY = 'active_company_id';

    public static function id(): ?int
    {
        return Session::get(self::SESSION_KEY);
    }

    public static function get(): Company
    {
        if ($id = self::id()) {
            $company = Company::find($id);
            if ($company) {
                return $company;
            }
        }

        $company = Company::query()->orderBy('id')->first();

        if (! $company) {
            $company = Company::create([
                'company_name' => 'My Company',
                'tagline' => 'Professional Billing Solutions',
            ]);
        }

        self::set($company);

        return $company;
    }

    public static function set(Company|int $company): void
    {
        $id = $company instanceof Company ? $company->id : $company;
        Session::put(self::SESSION_KEY, $id);
    }
}
