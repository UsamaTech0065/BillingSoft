<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('currency_code', 3)->default('USD')->after('tax_id');
            $table->string('currency_symbol', 10)->default('$')->after('currency_code');
            $table->unsignedTinyInteger('decimal_places')->default(2)->after('currency_symbol');
            $table->string('currency_position', 10)->default('before')->after('decimal_places');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'currency_symbol', 'decimal_places', 'currency_position']);
        });
    }
};
