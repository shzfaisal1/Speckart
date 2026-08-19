<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add B2C online order item columns to tbl_sales_product.
     * Replaces b2c_order_items table entirely.
     */
    public function up(): void
    {
        Schema::table('tbl_sales_product', function (Blueprint $table) {

            // ── Prescription Source (B2C online upload) ────────────────────
            if (!Schema::hasColumn('tbl_sales_product', 'prescription_source')) {
                $table->string('prescription_source', 50)->nullable()->default('manual_entry')
                      ->after('product_type');
            }
            if (!Schema::hasColumn('tbl_sales_product', 'prescription_file_url')) {
                $table->string('prescription_file_url', 255)->nullable()->after('prescription_source');
            }
            if (!Schema::hasColumn('tbl_sales_product', 'prescription_type')) {
                $table->string('prescription_type', 50)->nullable()->after('prescription_file_url');
            }

            // ── Lens Details (B2C package selection) ──────────────────────
            if (!Schema::hasColumn('tbl_sales_product', 'lens_type')) {
                $table->string('lens_type', 100)->nullable()->after('package_id');
            }
            if (!Schema::hasColumn('tbl_sales_product', 'lens_coating')) {
                $table->string('lens_coating', 150)->nullable()->after('lens_type');
            }
            if (!Schema::hasColumn('tbl_sales_product', 'lens_index')) {
                $table->string('lens_index', 50)->nullable()->after('lens_coating');
            }
            if (!Schema::hasColumn('tbl_sales_product', 'lens_package_price')) {
                $table->decimal('lens_package_price', 10, 2)->default(0)->after('lens_index');
            }

            // ── Frame SKU (B2C catalog reference) ─────────────────────────
            if (!Schema::hasColumn('tbl_sales_product', 'frame_sku')) {
                $table->string('frame_sku', 100)->nullable()->after('product_code');
            }

            // ── Extended Prescription PD ──────────────────────────────────
            if (!Schema::hasColumn('tbl_sales_product', 'GL_EYE_RPD')) {
                $table->decimal('GL_EYE_RPD', 6, 2)->nullable()
                      ->comment('Right Monocular PD')->after('GL_EYE_totalPD');
            }
            if (!Schema::hasColumn('tbl_sales_product', 'GL_EYE_LPD')) {
                $table->decimal('GL_EYE_LPD', 6, 2)->nullable()
                      ->comment('Left Monocular PD')->after('GL_EYE_RPD');
            }
            if (!Schema::hasColumn('tbl_sales_product', 'fitting_height')) {
                $table->decimal('fitting_height', 6, 2)->nullable()
                      ->comment('Fitting Height for Progressives')->after('GL_EYE_LPD');
            }

            // ── Item-level Status (B2C per-item tracking) ─────────────────
            if (!Schema::hasColumn('tbl_sales_product', 'item_status')) {
                $table->enum('item_status', [
                    'pending', 'confirmed', 'processing',
                    'ready', 'shipped', 'delivered', 'cancelled', 'returned',
                ])->default('pending')->after('return_status');
            }
            if (!Schema::hasColumn('tbl_sales_product', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('item_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_sales_product', function (Blueprint $table) {
            $cols = [
                'prescription_source', 'prescription_file_url', 'prescription_type',
                'lens_type', 'lens_coating', 'lens_index', 'lens_package_price',
                'frame_sku', 'GL_EYE_RPD', 'GL_EYE_LPD', 'fitting_height',
                'item_status', 'cancellation_reason',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('tbl_sales_product', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
