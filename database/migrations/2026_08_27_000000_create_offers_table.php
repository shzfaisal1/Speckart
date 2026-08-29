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
        if (!Schema::hasTable('offers')) {
            Schema::create('offers', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('coupon_code')->nullable()->index();
                $table->string('offer_type', 50)->default('buy1get1'); // 'buy1get1', 'gift_voucher', 'discount'
                $table->string('discount_type', 50)->default('percentage'); // 'percentage', 'fixed'
                $table->decimal('discount_value', 10, 2)->default(0.00);
                $table->decimal('voucher_value', 10, 2)->nullable();
                $table->decimal('min_cart_amount', 10, 2)->nullable();
                $table->decimal('max_discount', 10, 2)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('status', 50)->default('active'); // 'active', 'inactive'
                $table->boolean('bogo_extra_enabled')->default(false);
                $table->decimal('bogo_extra_discount', 8, 2)->default(60.00);
                $table->string('bogo_third_apply_on')->default('same_as_bogo');
                $table->json('bogo_third_brand_ids')->nullable();
                $table->json('bogo_third_category_ids')->nullable();
                $table->json('bogo_third_product_ids')->nullable();
                $table->integer('voucher_validity_days')->default(30);
                $table->integer('total_used')->default(0);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
