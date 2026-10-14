<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('tbl_purchase_return', 'product_id')) {
            Schema::table('tbl_purchase_return', function (Blueprint $table) {
                $table->string('product_id', 100)->nullable()->after('product_code');
            });

            // Backfill existing records from tbl_barcode
            $returns = DB::table('tbl_purchase_return')->get();
            foreach ($returns as $ret) {
                $barcode = DB::table('tbl_barcode')->where('barcode_no', $ret->barcode_no)->first();
                if ($barcode && !empty($barcode->product_id)) {
                    DB::table('tbl_purchase_return')
                        ->where('return_id', $ret->return_id)
                        ->update(['product_id' => $barcode->product_id]);
                } else {
                    $prodCode = DB::table('tbl_product_code')->where('product_code', $ret->product_code)->first();
                    if ($prodCode && !empty($prodCode->product_id)) {
                        DB::table('tbl_purchase_return')
                            ->where('return_id', $ret->return_id)
                            ->update(['product_id' => $prodCode->product_id]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tbl_purchase_return', 'product_id')) {
            Schema::table('tbl_purchase_return', function (Blueprint $table) {
                $table->dropColumn('product_id');
            });
        }
    }
};
