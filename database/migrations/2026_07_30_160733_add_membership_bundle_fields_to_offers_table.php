<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add membership_bundle fields to the offers table.
     */
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'linked_product_id')) {
                $table->unsignedBigInteger('linked_product_id')->nullable()->after('store_id');
            }
            if (!Schema::hasColumn('offers', 'membership_mrp')) {
                $table->decimal('membership_mrp', 10, 2)->nullable()->after('linked_product_id');
            }
            if (!Schema::hasColumn('offers', 'membership_sale_price')) {
                $table->decimal('membership_sale_price', 10, 2)->nullable()->after('membership_mrp');
            }
            if (!Schema::hasColumn('offers', 'entitlement_type')) {
                $table->string('entitlement_type', 100)->default('bogo_storewide')->nullable()->after('membership_sale_price');
            }
            if (!Schema::hasColumn('offers', 'entitlement_scope')) {
                $table->string('entitlement_scope', 255)->nullable()->after('entitlement_type');
            }
            if (!Schema::hasColumn('offers', 'cashback_percent')) {
                $table->decimal('cashback_percent', 5, 2)->nullable()->after('entitlement_scope');
            }
            if (!Schema::hasColumn('offers', 'cashback_delay_days')) {
                $table->unsignedInteger('cashback_delay_days')->default(14)->nullable()->after('cashback_percent');
            }
            if (!Schema::hasColumn('offers', 'stack_with_coupons')) {
                $table->boolean('stack_with_coupons')->default(true)->after('cashback_delay_days');
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
                'linked_product_id',
                'membership_mrp',
                'membership_sale_price',
                'entitlement_type',
                'entitlement_scope',
                'cashback_percent',
                'cashback_delay_days',
                'stack_with_coupons',
            ];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('offers', $c));
            if (!empty($existing)) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};
