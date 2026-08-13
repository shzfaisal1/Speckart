<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add BOGO package fields + Voucher value to the offers table.
     *
     * New columns:
     *  - bogo_buy_qty        : Buy quantity (hidden, always 1 for standard BOGO)
     *  - bogo_get_qty        : Get quantity free (hidden, always 1)
     *  - bogo_free_discount  : % off on the "get" item (100 = fully free)
     *  - bogo_extra_enabled  : Toggle — activate the bonus 3rd-item tier
     *  - bogo_extra_discount : % discount on the 3rd item (e.g. 60)
     *  - voucher_value       : Monetary value of a Gift Voucher (₹)
     *  - voucher_validity_days: Days the voucher is valid after issue
     */
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {

            if (!Schema::hasColumn('offers', 'bogo_buy_qty')) {
                $table->unsignedTinyInteger('bogo_buy_qty')->default(1)->nullable()->after('max_discount')
                      ->comment('Buy quantity for BOGO — always 1 for standard Buy 1 Get 1 Free');
            }

            if (!Schema::hasColumn('offers', 'bogo_get_qty')) {
                $table->unsignedTinyInteger('bogo_get_qty')->default(1)->nullable()->after('bogo_buy_qty')
                      ->comment('Get quantity free for BOGO — always 1');
            }

            if (!Schema::hasColumn('offers', 'bogo_free_discount')) {
                $table->decimal('bogo_free_discount', 5, 2)->default(100)->nullable()->after('bogo_get_qty')
                      ->comment('% discount on the free BOGO item — 100 = fully free');
            }

            if (!Schema::hasColumn('offers', 'bogo_extra_enabled')) {
                $table->boolean('bogo_extra_enabled')->default(false)->nullable()->after('bogo_free_discount')
                      ->comment('Whether the bonus 3rd-item tier is active');
            }

            if (!Schema::hasColumn('offers', 'bogo_extra_discount')) {
                $table->decimal('bogo_extra_discount', 5, 2)->nullable()->after('bogo_extra_enabled')
                      ->comment('% discount applied to the 3rd item in cart (e.g. 60 = 60% OFF)');
            }

            if (!Schema::hasColumn('offers', 'voucher_value')) {
                $table->decimal('voucher_value', 10, 2)->nullable()->after('bogo_extra_discount')
                      ->comment('Monetary value of a Gift Voucher in ₹');
            }

            if (!Schema::hasColumn('offers', 'voucher_validity_days')) {
                $table->unsignedSmallInteger('voucher_validity_days')->nullable()->after('voucher_value')
                      ->comment('Number of days a Gift Voucher is valid after it is issued');
            }
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $cols = [
                'bogo_buy_qty',
                'bogo_get_qty',
                'bogo_free_discount',
                'bogo_extra_enabled',
                'bogo_extra_discount',
                'voucher_value',
                'voucher_validity_days',
            ];

            $existing = array_filter($cols, fn($c) => Schema::hasColumn('offers', $c));
            if ($existing) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};
