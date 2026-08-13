<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the B2C orders table (online customer orders).
     * This is separate from the B2B tbl_sales table used for in-store/wholesale orders.
     */
    public function up(): void
    {
        Schema::create('b2c_orders', function (Blueprint $table) {
            $table->id();

            // ── Order Identity ──────────────────────────────────────────
            $table->string('order_number', 50)->unique()->comment('Public order reference e.g. B2C-2026-00123');

            // ── Customer ────────────────────────────────────────────────
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('FK to users.id; null for guest checkout');
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone', 20)->nullable();

            // ── Cart Reference ───────────────────────────────────────────
            $table->unsignedBigInteger('cart_id')->nullable()->index()->comment('FK to carts.id if persistent cart is implemented');

            // ── Shipping Address ─────────────────────────────────────────
            $table->unsignedBigInteger('shipping_address_id')->nullable()->index()->comment('FK to user_addresses.id');
            $table->text('shipping_address_snapshot')->nullable()->comment('JSON snapshot of address at time of order');

            // ── Pricing Breakdown ─────────────────────────────────────────
            $table->decimal('subtotal', 10, 2)->default(0)->comment('Sum of all item prices before discounts');
            $table->decimal('discount_amount', 10, 2)->default(0)->comment('Total discount applied');
            $table->decimal('tax_amount', 10, 2)->default(0)->comment('Total GST / tax amount');
            $table->decimal('shipping_fee', 10, 2)->default(0)->comment('Delivery / shipping charges');
            $table->decimal('grand_total', 10, 2)->default(0)->comment('Final payable: subtotal - discount + tax + shipping');
            $table->decimal('roundoff', 10, 2)->default(0)->comment('Rounding adjustment');

            // ── Offer / Coupon / Loyalty ──────────────────────────────────
            $table->unsignedBigInteger('offer_id')->nullable()->index()->comment('FK to offers.id');
            $table->string('coupon_code', 100)->nullable()->comment('Applied coupon code at time of order');
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->decimal('loyalty_points_used', 10, 2)->default(0)->comment('Loyalty points redeemed');
            $table->decimal('loyalty_points_earned', 10, 2)->default(0)->comment('Points earned on this order');
            $table->decimal('bogo_discount', 10, 2)->default(0)->comment('BOGO offer discount amount');

            // ── Order Status ──────────────────────────────────────────────
            $table->enum('order_status', [
                'pending',
                'confirmed',
                'processing',
                'ready_to_ship',
                'shipped',
                'out_for_delivery',
                'delivered',
                'cancelled',
                'returned',
            ])->default('pending');

            // ── Payment Status ────────────────────────────────────────────
            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed',
                'refunded',
                'partially_refunded',
                'cod_pending',
            ])->default('pending');

            // ── Delivery Info ─────────────────────────────────────────────
            $table->date('expected_delivery_date')->nullable();
            $table->string('courier_partner')->nullable()->comment('e.g. Bluedart, Delhivery');
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();

            // ── Source / Device ───────────────────────────────────────────
            $table->enum('device_type', ['web', 'android', 'ios', 'unknown'])->default('web');
            $table->string('utm_source')->nullable()->comment('Marketing attribution source');

            // ── Notes ─────────────────────────────────────────────────────
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ── Indexes ───────────────────────────────────────────────────
            $table->index('order_status');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2c_orders');
    }
};
