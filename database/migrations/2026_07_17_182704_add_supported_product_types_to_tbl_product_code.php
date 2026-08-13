<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_product_code', function (Blueprint $table) {
            $table->json('supported_product_types')->nullable()->after('product_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_product_code', function (Blueprint $table) {
            $table->dropColumn('supported_product_types');
        });
    }
};
