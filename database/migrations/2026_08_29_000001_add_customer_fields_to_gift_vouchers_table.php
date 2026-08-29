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
        if (Schema::hasTable('tbl_gift_vouchers')) {
            Schema::table('tbl_gift_vouchers', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_gift_vouchers', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('added_by');
                }
                if (!Schema::hasColumn('tbl_gift_vouchers', 'contact_no')) {
                    $table->string('contact_no', 50)->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('tbl_gift_vouchers', 'source_order_no')) {
                    $table->string('source_order_no', 100)->nullable()->after('contact_no');
                }
                if (!Schema::hasColumn('tbl_gift_vouchers', 'redeemed_order_no')) {
                    $table->string('redeemed_order_no', 100)->nullable()->after('source_order_no');
                }
                if (!Schema::hasColumn('tbl_gift_vouchers', 'voucher_type')) {
                    $table->string('voucher_type', 50)->default('promotional')->after('redeemed_order_no');
                }
                if (!Schema::hasColumn('tbl_gift_vouchers', 'is_single_use')) {
                    $table->boolean('is_single_use')->default(true)->after('voucher_type');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tbl_gift_vouchers')) {
            Schema::table('tbl_gift_vouchers', function (Blueprint $table) {
                $columns = ['user_id', 'contact_no', 'source_order_no', 'redeemed_order_no', 'voucher_type', 'is_single_use'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('tbl_gift_vouchers', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
