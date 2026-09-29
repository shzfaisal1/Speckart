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
        if (Schema::hasTable('lens_packages') && !Schema::hasColumn('lens_packages', 'product_code')) {
            Schema::table('lens_packages', function (Blueprint $table) {
                $table->string('product_code', 100)->nullable()->after('slug')
                      ->comment('Physical Lens SKU from tbl_product_code for inventory & purchase tracking');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lens_packages') && Schema::hasColumn('lens_packages', 'product_code')) {
            Schema::table('lens_packages', function (Blueprint $table) {
                $table->dropColumn('product_code');
            });
        }
    }
};
