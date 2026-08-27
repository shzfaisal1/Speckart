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
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'bogo_third_apply_on')) {
                $table->string('bogo_third_apply_on')->default('same_as_bogo')->after('bogo_extra_discount');
            }
            if (!Schema::hasColumn('offers', 'bogo_third_brand_ids')) {
                $table->json('bogo_third_brand_ids')->nullable()->after('bogo_third_apply_on');
            }
            if (!Schema::hasColumn('offers', 'bogo_third_category_ids')) {
                $table->json('bogo_third_category_ids')->nullable()->after('bogo_third_brand_ids');
            }
            if (!Schema::hasColumn('offers', 'bogo_third_product_ids')) {
                $table->json('bogo_third_product_ids')->nullable()->after('bogo_third_category_ids');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn([
                'bogo_third_apply_on',
                'bogo_third_brand_ids',
                'bogo_third_category_ids',
                'bogo_third_product_ids',
            ]);
        });
    }
};
