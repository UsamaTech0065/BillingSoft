<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Web Development', 'description' => 'Custom website development and implementation', 'type' => 'service', 'price' => 75.00, 'unit' => 'hr'],
            ['name' => 'Logo Design', 'description' => 'Professional logo design with revisions', 'type' => 'service', 'price' => 500.00, 'unit' => 'project'],
            ['name' => 'IT Consulting', 'description' => 'Technology strategy and advisory services', 'type' => 'service', 'price' => 120.00, 'unit' => 'hr'],
            ['name' => 'SEO Optimization', 'description' => 'Search engine optimization monthly package', 'type' => 'service', 'price' => 350.00, 'unit' => 'month'],
            ['name' => 'Laptop Computer', 'description' => 'Business laptop - 15 inch, 16GB RAM', 'type' => 'good', 'price' => 899.00, 'unit' => 'pcs'],
            ['name' => 'Wireless Mouse', 'description' => 'Ergonomic wireless mouse', 'type' => 'good', 'price' => 29.99, 'unit' => 'pcs'],
            ['name' => 'USB-C Hub', 'description' => '7-in-1 USB-C multiport adapter', 'type' => 'good', 'price' => 45.00, 'unit' => 'pcs'],
        ];

        $protectPhoneProducts = [
            ['name' => 'Screen Protector Install', 'description' => 'Professional screen protector installation', 'type' => 'service', 'price' => 15.00, 'unit' => 'pcs'],
            ['name' => 'Phone Case', 'description' => 'Premium protective phone case', 'type' => 'good', 'price' => 24.99, 'unit' => 'pcs'],
            ['name' => 'Device Repair', 'description' => 'Screen and hardware repair service', 'type' => 'service', 'price' => 89.00, 'unit' => 'job'],
        ];

        $billingSoft = Company::where('company_name', 'BillingSoft')->first();
        $protectPhone = Company::where('company_name', 'Protect Phone')->first();

        if ($billingSoft) {
            foreach ($products as $product) {
                Product::withoutGlobalScope('company')->firstOrCreate(
                    ['company_id' => $billingSoft->id, 'name' => $product['name']],
                    array_merge($product, ['company_id' => $billingSoft->id])
                );
            }
        }

        if ($protectPhone) {
            foreach ($protectPhoneProducts as $product) {
                Product::withoutGlobalScope('company')->firstOrCreate(
                    ['company_id' => $protectPhone->id, 'name' => $product['name']],
                    array_merge($product, ['company_id' => $protectPhone->id])
                );
            }
        }
    }
}
