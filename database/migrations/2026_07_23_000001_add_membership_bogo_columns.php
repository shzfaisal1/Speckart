<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add enable_bogo to tbl_membership_card
        if (Schema::hasTable('tbl_membership_card') && !Schema::hasColumn('tbl_membership_card', 'enable_bogo')) {
            Schema::table('tbl_membership_card', function (Blueprint $table) {
                $table->tinyInteger('enable_bogo')->default(0)->after('voucher_validity_days')->comment('1=BOGO enabled, 0=disabled');
            });
        }

        // 2. Add membership columns to tbl_customer
        if (Schema::hasTable('tbl_customer')) {
            Schema::table('tbl_customer', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_customer', 'membership_card_id')) {
                    $table->unsignedBigInteger('membership_card_id')->nullable()->after('cust_note')->comment('FK to tbl_membership_card.card_id');
                }
                if (!Schema::hasColumn('tbl_customer', 'membership_expiry')) {
                    $table->date('membership_expiry')->nullable()->after('membership_card_id')->comment('Expiry date of membership');
                }
            });
        }

        // 3. Add bogo_discount to tbl_sales
        if (Schema::hasTable('tbl_sales') && !Schema::hasColumn('tbl_sales', 'bogo_discount')) {
            Schema::table('tbl_sales', function (Blueprint $table) {
                $table->decimal('bogo_discount', 10, 2)->default(0)->after('cart_discount_resion')->comment('BOGO free item discount amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_membership_card')) {
            Schema::table('tbl_membership_card', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_membership_card', 'enable_bogo')) {
                    $table->dropColumn('enable_bogo');
                }
            });
        }

        if (Schema::hasTable('tbl_customer')) {
            Schema::table('tbl_customer', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('tbl_customer', 'membership_card_id')) $columns[] = 'membership_card_id';
                if (Schema::hasColumn('tbl_customer', 'membership_expiry')) $columns[] = 'membership_expiry';
                if (!empty($columns)) $table->dropColumn($columns);
            });
        }

        if (Schema::hasTable('tbl_sales')) {
            Schema::table('tbl_sales', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_sales', 'bogo_discount')) {
                    $table->dropColumn('bogo_discount');
                }
            });
        }
    }
};
