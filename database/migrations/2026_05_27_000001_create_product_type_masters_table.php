<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: product_type_masters
     * Used by: Masters → Product Types (list.blade.php)
     */
    public function up(): void
    {
        Schema::create('product_type_masters', function (Blueprint $table) {

            // ── Primary ──────────────────────────────────────────
            $table->id();

            // ── Core fields (from modal form) ────────────────────
            $table->string('name', 100);                    // "Reading Glasses"
            $table->string('slug', 100)->unique();          // "reading"  (auto-generated)
            $table->string('subtitle', 150)->nullable();    // "+ Positive Power"
            $table->string('icon', 20)->nullable();         // "📖" or image path

            // ── Power config ─────────────────────────────────────
            $table->boolean('has_power')->default(false);   // show power chips on frontend?
            $table->json('default_powers')->nullable();
            // e.g. ["+1.25", "+1.5", "+1.75", "+2", "+2.25", "+2.5"]
            // null when has_power = false

            // ── Display ──────────────────────────────────────────
            $table->unsignedTinyInteger('sort_order')->default(0);
            // controls tab order on frontend product page

            // ── Status ───────────────────────────────────────────
            $table->boolean('is_active')->default(true);

            // ── Timestamps ───────────────────────────────────────
            $table->timestamps();
            $table->softDeletes();  // safe delete — won't break existing products

        });


        // ── Per-product override table ───────────────────────────────────
        // Each product row references master types and can override powers
        Schema::create('product_type_overrides', function (Blueprint $table) {

            $table->id();

            $table->integer('product_id');

            $table->foreign('product_id')
                  ->references('id')
                  ->on('tbl_product_code')
                  ->cascadeOnDelete();

            $table->foreignId('type_master_id')
                  ->constrained('product_type_masters')
                  ->cascadeOnDelete();

            $table->boolean('is_enabled')->default(true);

            $table->json('custom_powers')->nullable();
            // null = use master default_powers
            // set = overrides master for this specific product

            $table->timestamps();

            // one row per product+type combination
            $table->unique(['product_id', 'type_master_id'], 'unique_product_type');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_type_overrides');
        Schema::dropIfExists('product_type_masters');
    }
};
