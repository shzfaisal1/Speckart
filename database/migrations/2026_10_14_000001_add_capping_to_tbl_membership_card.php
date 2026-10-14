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
        Schema::table('tbl_membership_card', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_membership_card', 'loyalty_min_order_amount')) {
                $table->decimal('loyalty_min_order_amount', 10, 2)->nullable()->default(0)->after('loyalty_use_percent');
            }
            if (!Schema::hasColumn('tbl_membership_card', 'loyalty_max_discount')) {
                $table->decimal('loyalty_max_discount', 10, 2)->nullable()->after('loyalty_min_order_amount');
            }
            if (!Schema::hasColumn('tbl_membership_card', 'coupon_min_order_amount')) {
                $table->decimal('coupon_min_order_amount', 10, 2)->nullable()->default(0)->after('coupon_percent');
            }
            if (!Schema::hasColumn('tbl_membership_card', 'coupon_max_discount')) {
                $table->decimal('coupon_max_discount', 10, 2)->nullable()->after('coupon_min_order_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_membership_card', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('tbl_membership_card', 'loyalty_min_order_amount')) {
                $columnsToDrop[] = 'loyalty_min_order_amount';
            }
            if (Schema::hasColumn('tbl_membership_card', 'loyalty_max_discount')) {
                $columnsToDrop[] = 'loyalty_max_discount';
            }
            if (Schema::hasColumn('tbl_membership_card', 'coupon_min_order_amount')) {
                $columnsToDrop[] = 'coupon_min_order_amount';
            }
            if (Schema::hasColumn('tbl_membership_card', 'coupon_max_discount')) {
                $columnsToDrop[] = 'coupon_max_discount';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
