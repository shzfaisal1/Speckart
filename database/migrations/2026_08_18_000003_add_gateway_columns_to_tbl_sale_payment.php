<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add payment gateway columns to tbl_sale_payment.
     * Replaces b2c_order_payments table entirely.
     */
    public function up(): void
    {
        Schema::table('tbl_sale_payment', function (Blueprint $table) {

            // ── Gateway Identification ─────────────────────────────────────
            if (!Schema::hasColumn('tbl_sale_payment', 'payment_gateway')) {
                $table->string('payment_gateway', 50)->nullable()
                      ->comment('e.g. razorpay, stripe, payu, cod, wallet')
                      ->after('pay_method');
            }
            if (!Schema::hasColumn('tbl_sale_payment', 'transaction_id')) {
                $table->string('transaction_id', 200)->nullable()->unique()
                      ->comment('Gateway payment / transaction ID')
                      ->after('payment_gateway');
            }
            if (!Schema::hasColumn('tbl_sale_payment', 'gateway_order_id')) {
                $table->string('gateway_order_id', 200)->nullable()
                      ->comment('Gateway-side order ID (e.g. Razorpay order_id)')
                      ->after('transaction_id');
            }

            // ── Currency & Card Details ────────────────────────────────────
            if (!Schema::hasColumn('tbl_sale_payment', 'currency')) {
                $table->string('currency', 10)->default('INR')->after('gateway_order_id');
            }
            if (!Schema::hasColumn('tbl_sale_payment', 'bank')) {
                $table->string('bank', 100)->nullable()
                      ->comment('Bank name if netbanking/card')->after('currency');
            }
            if (!Schema::hasColumn('tbl_sale_payment', 'card_network')) {
                $table->string('card_network', 50)->nullable()
                      ->comment('e.g. Visa, Mastercard, Rupay')->after('bank');
            }

            // ── Gateway Status ─────────────────────────────────────────────
            if (!Schema::hasColumn('tbl_sale_payment', 'gateway_status')) {
                $table->enum('gateway_status', [
                    'initiated', 'success', 'failed', 'pending', 'refunded', 'partially_refunded',
                ])->nullable()->after('card_network');
            }

            // ── Refund Details ────────────────────────────────────────────
            if (!Schema::hasColumn('tbl_sale_payment', 'refund_id')) {
                $table->string('refund_id', 200)->nullable()
                      ->comment('Gateway refund ID')->after('gateway_status');
            }
            if (!Schema::hasColumn('tbl_sale_payment', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable()->after('refund_id');
            }
            if (!Schema::hasColumn('tbl_sale_payment', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('refund_amount');
            }
            if (!Schema::hasColumn('tbl_sale_payment', 'refund_reason')) {
                $table->text('refund_reason')->nullable()->after('refunded_at');
            }

            // ── Raw Gateway Payload ────────────────────────────────────────
            if (!Schema::hasColumn('tbl_sale_payment', 'webhook_payload')) {
                $table->text('webhook_payload')->nullable()
                      ->comment('Raw JSON from payment gateway webhook')->after('refund_reason');
            }
            if (!Schema::hasColumn('tbl_sale_payment', 'failure_reason')) {
                $table->text('failure_reason')->nullable()
                      ->comment('Error message if payment failed')->after('webhook_payload');
            }

            // ── Payment Timestamp ─────────────────────────────────────────
            if (!Schema::hasColumn('tbl_sale_payment', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()
                      ->comment('When payment was confirmed successful')->after('failure_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_sale_payment', function (Blueprint $table) {
            $cols = [
                'payment_gateway', 'transaction_id', 'gateway_order_id',
                'currency', 'bank', 'card_network', 'gateway_status',
                'refund_id', 'refund_amount', 'refunded_at', 'refund_reason',
                'webhook_payload', 'failure_reason', 'paid_at',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('tbl_sale_payment', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
