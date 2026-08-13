<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add bundle banner and 3-state fields to offers table.
     */
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'trigger_product_id')) {
                $table->unsignedBigInteger('trigger_product_id')->nullable()->after('linked_product_id');
            }
            if (!Schema::hasColumn('offers', 'banner_message_1')) {
                $table->string('banner_message_1', 500)->nullable()->after('trigger_product_id');
            }
            if (!Schema::hasColumn('offers', 'banner_message_2')) {
                $table->string('banner_message_2', 500)->nullable()->after('banner_message_1');
            }
            if (!Schema::hasColumn('offers', 'banner_message_3')) {
                $table->string('banner_message_3', 500)->nullable()->after('banner_message_2');
            }
            if (!Schema::hasColumn('offers', 'free_item_scope')) {
                $table->string('free_item_scope', 100)->default('all_products')->nullable()->after('banner_message_3');
            }
            if (!Schema::hasColumn('offers', 'get_item_discount_percent')) {
                $table->decimal('get_item_discount_percent', 5, 2)->default(100.00)->nullable()->after('free_item_scope');
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
                'trigger_product_id',
                'banner_message_1',
                'banner_message_2',
                'banner_message_3',
                'free_item_scope',
                'get_item_discount_percent',
            ];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('offers', $c));
            if (!empty($existing)) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};
