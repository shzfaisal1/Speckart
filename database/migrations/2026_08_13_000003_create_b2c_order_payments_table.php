<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the B2C order payments table — tracks online payment gateway
     * transactions per B2C order.
     * 
     * Separate from tbl_sale_payment (used for B2B in-store cash/offline payments).
     * This table is designed to work with online gateways (Razorpay, Stripe, PayU, COD).
     */
    public function up(): void
    {
        Schema::create('b2c_order_payments', function (Blueprint $table) {
            $table->id();

            // ── Parent Order ──────────────────────────────────────────────
            $table->unsignedBigInteger('order_id')->index()->comment('FK to b2c_orders.id');
            $table->foreign('order_id')->references('id')->on('b2c_orders')->onDelete('cascade');

            // ── Gateway Details ───────────────────────────────────────────
            $table->string('payment_gateway', 50)->comment('e.g. razorpay, stripe, payu, cod, wallet');
            $table->string('transaction_id')->nullable()->unique()->comment('Gateway payment ID / transaction reference');
            $table->string('gateway_order_id')->nullable()->comment('Gateway-side order ID (e.g. Razorpay order_id)');

            // ── Amount ────────────────────────────────────────────────────
            $table->decimal('amount', 10, 2)->comment('Amount attempted/paid');
            $table->string('currency', 10)->default('INR');

            // ── Payment Method ────────────────────────────────────────────
            $table->string('payment_method', 50)->nullable()->comment('e.g. card, upi, netbanking, wallet, cod, emi');
            $table->string('bank')->nullable()->comment('Bank name if netbanking/card');
            $table->string('card_network')->nullable()->comment('e.g. Visa, Mastercard, Rupay');

            // ── Status ────────────────────────────────────────────────────
            $table->enum('status', [
                'initiated',
                'success',
                'failed',
                'pending',
                'refunded',
                'partially_refunded',
            ])->default('initiated');

            // ── Refund Details ────────────────────────────────────────────
            $table->string('refund_id')->nullable()->comment('Gateway refund ID');
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('refund_reason')->nullable();

            // ── Raw Response ──────────────────────────────────────────────
            $table->json('webhook_payload')->nullable()->comment('Raw JSON response from payment gateway webhook');
            $table->text('failure_reason')->nullable()->comment('Error message if payment failed');

            // ── Timestamps ────────────────────────────────────────────────
            $table->timestamp('paid_at')->nullable()->comment('Timestamp when payment was confirmed successful');
            $table->timestamps();

            // ── Indexes ───────────────────────────────────────────────────
            $table->index('status');
            $table->index('payment_gateway');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2c_order_payments');
    }
};
