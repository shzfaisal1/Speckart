<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add B2C online order columns to tbl_sales.
     * Replaces b2c_orders table entirely.
     */
    public function up(): void
    {
        Schema::table('tbl_sales', function (Blueprint $table) {

            // ── B2C User Reference ─────────────────────────────────────────
            if (!Schema::hasColumn('tbl_sales', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('sale_id')
                      ->comment('FK to users.id for B2C web customers');
            }

            // ── Order Status (replaces numeric sales_status for B2C) ───────
            if (!Schema::hasColumn('tbl_sales', 'order_status')) {
                $table->enum('order_status', [
                    'pending', 'confirmed', 'processing',
                    'ready_to_ship', 'shipped', 'out_for_delivery',
                    'delivered', 'cancelled', 'returned',
                ])->default('pending')->after('sales_status');
            }

            // ── Payment Status ─────────────────────────────────────────────
            if (!Schema::hasColumn('tbl_sales', 'payment_status')) {
                $table->enum('payment_status', [
                    'pending', 'paid', 'failed',
                    'refunded', 'partially_refunded', 'cod_pending',
                ])->default('pending')->after('order_status');
            }

            // ── Prescription / RX Verification ────────────────────────────
            if (!Schema::hasColumn('tbl_sales', 'rx_verification_status')) {
                $table->enum('rx_verification_status', [
                    'not_required', 'pending_upload', 'pending_review',
                    'approved', 'clarification_needed', 'rejected',
                ])->default('not_required')->after('payment_status');
            }
            if (!Schema::hasColumn('tbl_sales', 'is_rx_required')) {
                $table->boolean('is_rx_required')->default(false)->after('rx_verification_status');
            }
            if (!Schema::hasColumn('tbl_sales', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('is_rx_required');
            }
            if (!Schema::hasColumn('tbl_sales', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
            if (!Schema::hasColumn('tbl_sales', 'optometrist_notes')) {
                $table->text('optometrist_notes')->nullable()->after('verified_at');
            }

            // ── Delivery & Shipping ────────────────────────────────────────
            if (!Schema::hasColumn('tbl_sales', 'delivery_method')) {
                $table->enum('delivery_method', ['standard', 'express', 'store_pickup'])
                      ->default('standard')->after('optometrist_notes');
            }
            if (!Schema::hasColumn('tbl_sales', 'expected_delivery_date')) {
                $table->date('expected_delivery_date')->nullable()->after('delivery_method');
            }
            if (!Schema::hasColumn('tbl_sales', 'courier_partner')) {
                $table->string('courier_partner', 100)->nullable()->after('expected_delivery_date');
            }
            if (!Schema::hasColumn('tbl_sales', 'tracking_number')) {
                $table->string('tracking_number', 100)->nullable()->after('courier_partner');
            }
            if (!Schema::hasColumn('tbl_sales', 'tracking_url')) {
                $table->string('tracking_url', 255)->nullable()->after('tracking_number');
            }
            if (!Schema::hasColumn('tbl_sales', 'shipping_address_snapshot')) {
                $table->text('shipping_address_snapshot')->nullable()
                      ->comment('JSON snapshot of delivery address')->after('tracking_url');
            }

            // ── Lab / Fulfillment ──────────────────────────────────────────
            if (!Schema::hasColumn('tbl_sales', 'assigned_lab_id')) {
                $table->unsignedBigInteger('assigned_lab_id')->nullable()->after('shipping_address_snapshot');
            }
            if (!Schema::hasColumn('tbl_sales', 'lab_status')) {
                $table->enum('lab_status', [
                    'pending', 'assigned', 'cutting', 'fitting',
                    'qc_passed', 'qc_failed', 'completed',
                ])->default('pending')->after('assigned_lab_id');
            }
            if (!Schema::hasColumn('tbl_sales', 'lab_job_number')) {
                $table->string('lab_job_number', 50)->nullable()->after('lab_status');
            }
            if (!Schema::hasColumn('tbl_sales', 'lab_notes')) {
                $table->text('lab_notes')->nullable()->after('lab_job_number');
            }
            if (!Schema::hasColumn('tbl_sales', 'lab_assigned_at')) {
                $table->timestamp('lab_assigned_at')->nullable()->after('lab_notes');
            }
            if (!Schema::hasColumn('tbl_sales', 'lab_completed_at')) {
                $table->timestamp('lab_completed_at')->nullable()->after('lab_assigned_at');
            }

            // ── Source & Attribution ───────────────────────────────────────
            if (!Schema::hasColumn('tbl_sales', 'device_type')) {
                $table->enum('device_type', ['web', 'android', 'ios', 'unknown', 'pos'])
                      ->default('pos')->after('lab_completed_at');
            }
            if (!Schema::hasColumn('tbl_sales', 'utm_source')) {
                $table->string('utm_source', 150)->nullable()->after('device_type');
            }

            // ── Pricing (B2C specific) ─────────────────────────────────────
            if (!Schema::hasColumn('tbl_sales', 'frame_total')) {
                $table->decimal('frame_total', 10, 2)->default(0)->after('total_item_price');
            }
            if (!Schema::hasColumn('tbl_sales', 'lens_total')) {
                $table->decimal('lens_total', 10, 2)->default(0)->after('frame_total');
            }
            if (!Schema::hasColumn('tbl_sales', 'bogo_discount')) {
                $table->decimal('bogo_discount', 10, 2)->default(0)->after('total_discount');
            }
            if (!Schema::hasColumn('tbl_sales', 'shipping_fee')) {
                $table->decimal('shipping_fee', 10, 2)->default(0)->after('bogo_discount');
            }

            // ── Notes ──────────────────────────────────────────────────────
            if (!Schema::hasColumn('tbl_sales', 'customer_note')) {
                $table->text('customer_note')->nullable()->after('utm_source');
            }
            if (!Schema::hasColumn('tbl_sales', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('customer_note');
            }
            if (!Schema::hasColumn('tbl_sales', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('admin_note');
            }

            // ── Return / Exchange (replaces b2c_order_returns) ─────────────
            if (!Schema::hasColumn('tbl_sales', 'return_type')) {
                $table->enum('return_type', ['refund', 'replacement', 'lens_remake'])
                      ->nullable()->after('return_amount');
            }
            if (!Schema::hasColumn('tbl_sales', 'return_reason')) {
                $table->enum('return_reason', [
                    'power_mismatch', 'frame_damage', 'fit_issue', 'changed_mind', 'other',
                ])->nullable()->after('return_type');
            }
            if (!Schema::hasColumn('tbl_sales', 'return_exchange_type')) {
                $table->enum('return_exchange_type', [
                    'same_product', 'different_power', 'different_frame', 'none',
                ])->nullable()->after('return_reason');
            }
            if (!Schema::hasColumn('tbl_sales', 'return_stage')) {
                $table->enum('return_stage', [
                    'requested', 'approved', 'rejected',
                    'item_received', 'remake_in_progress', 'completed',
                ])->nullable()->after('return_exchange_type');
            }
            if (!Schema::hasColumn('tbl_sales', 'return_admin_notes')) {
                $table->text('return_admin_notes')->nullable()->after('return_stage');
            }
            if (!Schema::hasColumn('tbl_sales', 'warranty_claim')) {
                $table->boolean('warranty_claim')->default(false)->after('return_admin_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_sales', function (Blueprint $table) {
            $cols = [
                'user_id', 'order_status', 'payment_status', 'rx_verification_status',
                'is_rx_required', 'verified_by', 'verified_at', 'optometrist_notes',
                'delivery_method', 'expected_delivery_date', 'courier_partner',
                'tracking_number', 'tracking_url', 'shipping_address_snapshot',
                'assigned_lab_id', 'lab_status', 'lab_job_number', 'lab_notes',
                'lab_assigned_at', 'lab_completed_at', 'device_type', 'utm_source',
                'frame_total', 'lens_total', 'bogo_discount', 'shipping_fee',
                'customer_note', 'admin_note', 'updated_by',
                'return_type', 'return_reason', 'return_exchange_type',
                'return_stage', 'return_admin_notes', 'warranty_claim',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('tbl_sales', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
