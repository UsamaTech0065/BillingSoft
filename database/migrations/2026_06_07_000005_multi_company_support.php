<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('company_settings', 'companies');

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        $defaultCompanyId = DB::table('companies')->min('id') ?? 1;

        if (! DB::table('companies')->exists()) {
            DB::table('companies')->insert([
                'company_name' => 'My Company',
                'tagline' => 'Professional Billing Solutions',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $defaultCompanyId = DB::table('companies')->min('id');
        }

        DB::table('products')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
        DB::table('invoices')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::rename('companies', 'company_settings');
    }
};
