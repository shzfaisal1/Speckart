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
        if (!Schema::hasTable('tbl_gift_vouchers')) {
            Schema::create('tbl_gift_vouchers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->decimal('voucher_value', 10, 2);
                $table->decimal('min_cart_amount', 10, 2)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->integer('validity_days')->default(30);
                
                // Membership targeting
                $table->string('membership_scope')->default('all_users'); // 'all_users', 'any_membership', 'specific_membership'
                $table->unsignedBigInteger('membership_card_id')->nullable();
                
                // Anti-stacking & margin protection
                $table->boolean('allow_bogo_stacking')->default(false);
                $table->boolean('allow_coupon_stacking')->default(false);
                
                // Product eligibility (Same as BOGO module)
                $table->string('apply_on')->default('all_products'); // 'all_products', 'specific_category', 'specific_brand', 'specific_products'
                $table->json('category_ids')->nullable();
                $table->json('brand_ids')->nullable();
                $table->json('product_ids')->nullable();
                
                $table->text('description')->nullable();
                $table->integer('usage_limit_per_user')->default(1);
                $table->integer('total_used')->default(0);
                $table->string('status')->default('active'); // 'active', 'inactive', 'draft'
                $table->unsignedBigInteger('added_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_gift_vouchers');
    }
};
