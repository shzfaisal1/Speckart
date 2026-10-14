<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Enforce Allow_Negative_Inventory = 0 for physical barcode goods (Contact Lenses, Solutions).
     */
    public function up(): void
    {
        DB::table('tbl_product_code')
            ->whereIn('product_type', ['Lens', 'Solution'])
            ->orWhere('category_id', 7)
            ->update([
                'Allow_Negative_Inventory' => 0,
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tbl_product_code')
            ->whereIn('product_type', ['Lens', 'Solution'])
            ->orWhere('category_id', 7)
            ->update([
                'Allow_Negative_Inventory' => 1,
                'updated_at' => now(),
            ]);
    }
};
