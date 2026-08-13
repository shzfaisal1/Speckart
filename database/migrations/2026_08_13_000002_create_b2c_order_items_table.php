<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the B2C order items table — one row per product line in an online order.
     * This is separate from the B2B tbl_sales_product table.
     * 
     * Includes:
     *  - Product snapshot (name, code, price at time of order)
     *  - Lens prescription fields (same GL_EYE_* pattern as tbl_sales_product)
     *  - Linked lens package
     */
    public function up(): void
    {
        Schema::create('b2c_order_items', function (Blueprint $table) {
            $table->id();

            // ── Parent Order ──────────────────────────────────────────────
            $table->unsignedBigInteger('order_id')->index()->comment('FK to b2c_orders.id');
            $table->foreign('order_id')->references('id')->on('b2c_orders')->onDelete('cascade');

            // ── Product Reference ─────────────────────────────────────────
            $table->unsignedBigInteger('product_id')->nullable()->index()->comment('FK to tbl_product_code.id');
            $table->string('product_code', 100)->nullable();
            $table->string('product_name')->nullable()->comment('Snapshot of product name at time of order');
            $table->enum('product_type', ['frame', 'lens', 'contact_lens', 'sunglass', 'accessories', 'other'])->nullable();
            $table->string('barcode', 100)->nullable();

            // ── Quantity & Pricing ────────────────────────────────────────
            $table->integer('qty')->default(1);
            $table->decimal('base_price', 10, 2)->default(0)->comment('MRP / original price');
            $table->decimal('sale_price', 10, 2)->default(0)->comment('Price after product-level discount');
            $table->decimal('discount_amt', 10, 2)->default(0)->comment('Product-level discount amount');
            $table->decimal('total_price', 10, 2)->default(0)->comment('sale_price * qty');
            $table->string('hsn_code', 50)->nullable();
            $table->decimal('gst', 5, 2)->default(0)->comment('GST percentage');
            $table->decimal('gst_amount', 10, 2)->default(0)->comment('GST amount on this item');

            // ── Lens Package ──────────────────────────────────────────────
            $table->unsignedBigInteger('lens_package_id')->nullable()->index()->comment('FK to lens_packages.id');
            $table->decimal('lens_package_price', 10, 2)->default(0)->comment('Price of selected lens package');
            $table->string('coating_apply')->nullable();

            // ── Prescription / Eye Power ──────────────────────────────────
            // Right Eye - Distance
            $table->decimal('GL_EYE_RS_D', 6, 2)->nullable()->comment('Right Sphere Distance');
            $table->decimal('GL_EYE_RC_D', 6, 2)->nullable()->comment('Right Cylinder Distance');
            $table->decimal('GL_EYE_RA_D', 6, 2)->nullable()->comment('Right Axis Distance');
            $table->decimal('GL_EYE_RP_D', 6, 2)->nullable()->comment('Right Prism Distance');
            $table->decimal('GL_EYE_RV_D', 6, 2)->nullable()->comment('Right Vision Distance');
            // Right Eye - Near
            $table->decimal('GL_EYE_RS_N', 6, 2)->nullable()->comment('Right Sphere Near');
            $table->decimal('GL_EYE_RC_N', 6, 2)->nullable()->comment('Right Cylinder Near');
            $table->decimal('GL_EYE_RA_N', 6, 2)->nullable()->comment('Right Axis Near');
            $table->decimal('GL_EYE_RP_N', 6, 2)->nullable()->comment('Right Prism Near');
            $table->decimal('GL_EYE_RV_N', 6, 2)->nullable()->comment('Right Vision Near');
            $table->decimal('GL_EYE_RADD', 6, 2)->nullable()->comment('Right Addition');
            // Left Eye - Distance
            $table->decimal('GL_EYE_LS_D', 6, 2)->nullable()->comment('Left Sphere Distance');
            $table->decimal('GL_EYE_LC_D', 6, 2)->nullable()->comment('Left Cylinder Distance');
            $table->decimal('GL_EYE_LA_D', 6, 2)->nullable()->comment('Left Axis Distance');
            $table->decimal('GL_EYE_LP_D', 6, 2)->nullable()->comment('Left Prism Distance');
            $table->decimal('GL_EYE_LV_D', 6, 2)->nullable()->comment('Left Vision Distance');
            // Left Eye - Near
            $table->decimal('GL_EYE_LS_N', 6, 2)->nullable()->comment('Left Sphere Near');
            $table->decimal('GL_EYE_LC_N', 6, 2)->nullable()->comment('Left Cylinder Near');
            $table->decimal('GL_EYE_LA_N', 6, 2)->nullable()->comment('Left Axis Near');
            $table->decimal('GL_EYE_LP_N', 6, 2)->nullable()->comment('Left Prism Near');
            $table->decimal('GL_EYE_LV_N', 6, 2)->nullable()->comment('Left Vision Near');
            $table->decimal('GL_EYE_LADD', 6, 2)->nullable()->comment('Left Addition');
            // PD
            $table->decimal('GL_EYE_totalPD', 6, 2)->nullable()->comment('Total Pupillary Distance');
            $table->text('prescription_notes')->nullable();

            // ── Contact Lens Specific ─────────────────────────────────────
            $table->string('wearing_type')->nullable()->comment('e.g. Daily, Monthly, Yearly');
            $table->integer('lensRightNoOfBoxes')->nullable();
            $table->integer('lensRightTotalPieces')->nullable();
            $table->integer('lensLeftNoOfBoxes')->nullable();
            $table->integer('lensLeftTotalPieces')->nullable();

            // ── Item Status ───────────────────────────────────────────────
            $table->enum('item_status', [
                'pending',
                'confirmed',
                'processing',
                'ready',
                'shipped',
                'delivered',
                'cancelled',
                'returned',
            ])->default('pending');
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2c_order_items');
    }
};
