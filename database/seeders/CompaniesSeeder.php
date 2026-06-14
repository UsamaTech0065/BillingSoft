<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    public function run(): void
    {
        Company::firstOrCreate(
            ['company_name' => 'BillingSoft'],
            [
                'tagline' => 'Professional Billing Solutions',
                'email' => 'billing@billingsoft.com',
                'phone' => '+1 (555) 123-4567',
                'website' => 'www.billingsoft.com',
                'address' => '123 Business Avenue, Suite 100',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
                'country' => 'USA',
                'tax_id' => 'TAX-123456789',
                'footer_message' => 'Thank you for your business! Payment is due within 30 days.',
            ]
        );

        Company::firstOrCreate(
            ['company_name' => 'Protect Phone'],
            [
                'tagline' => 'Mobile Protection Services',
                'email' => 'info@protectphone.com',
                'phone' => '+1 (555) 987-6543',
                'website' => 'www.protectphone.com',
                'address' => '456 Tech Park Drive',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90001',
                'country' => 'USA',
                'tax_id' => 'TAX-987654321',
                'footer_message' => 'Thank you for choosing Protect Phone!',
            ]
        );
    }
}
