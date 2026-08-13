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
        // 1. lens_packages
        if (!Schema::hasTable('lens_packages')) {
            Schema::create('lens_packages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('slug', 150)->unique();
                $table->text('short_description')->nullable();
                $table->decimal('current_price', 10, 2);
                $table->decimal('original_price', 10, 2)->nullable();
                $table->unsignedTinyInteger('warranty_months')->default(0);
                $table->boolean('is_free_lens')->default(false);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. lens_package_tags
        if (!Schema::hasTable('lens_package_tags')) {
            Schema::create('lens_package_tags', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 100)->unique();
                $table->string('icon_url', 255)->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
            });
        }

        // 3. lens_package_tag_map
        if (!Schema::hasTable('lens_package_tag_map')) {
            Schema::create('lens_package_tag_map', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lens_package_id')->constrained('lens_packages')->cascadeOnDelete();
                $table->foreignId('tag_id')->constrained('lens_package_tags')->cascadeOnDelete();
                $table->unique(['lens_package_id', 'tag_id'], 'unique_package_tag');
            });
        }

        // 4. lens_benefits
        if (!Schema::hasTable('lens_benefits')) {
            Schema::create('lens_benefits', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->text('description')->nullable();
                $table->string('icon_emoji', 20)->nullable();
                $table->string('icon_image', 255)->nullable();
                $table->boolean('is_active')->default(true);
            });
        }

        // 5. lens_package_benefits
        if (!Schema::hasTable('lens_package_benefits')) {
            Schema::create('lens_package_benefits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lens_package_id')->constrained('lens_packages')->cascadeOnDelete();
                $table->foreignId('benefit_id')->constrained('lens_benefits')->cascadeOnDelete();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_highlighted')->default(false);
                $table->unique(['lens_package_id', 'benefit_id'], 'unique_package_benefit');
            });
        }

        // 6. lens_package_media
        if (!Schema::hasTable('lens_package_media')) {
            Schema::create('lens_package_media', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lens_package_id')->constrained('lens_packages')->cascadeOnDelete();
                $table->enum('media_type', ['image', 'video', 'comparison']);
                $table->string('url', 500);
                $table->string('alt_text', 255)->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamp('created_at')->nullable();
            });
        }

        // 7. lens_package_badges
        if (!Schema::hasTable('lens_package_badges')) {
            Schema::create('lens_package_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lens_package_id')->constrained('lens_packages')->cascadeOnDelete();
                $table->string('label', 100);
                $table->string('bg_color', 7)->nullable();
                $table->string('text_color', 7)->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
            });
        }

        // 8. coupons
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique();
                $table->string('description', 255)->nullable();
                $table->enum('discount_type', ['percentage', 'fixed']);
                $table->decimal('discount_value', 10, 2);
                $table->decimal('min_order_value', 10, 2)->nullable();
                $table->decimal('max_discount_amount', 10, 2)->nullable();
                $table->timestamp('valid_from')->nullable();
                $table->timestamp('valid_until')->nullable();
                $table->unsignedInteger('max_uses')->nullable();
                $table->unsignedInteger('current_uses')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 9. lens_package_coupons
        if (!Schema::hasTable('lens_package_coupons')) {
            Schema::create('lens_package_coupons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lens_package_id')->constrained('lens_packages')->cascadeOnDelete();
                $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
                $table->unique(['lens_package_id', 'coupon_id'], 'unique_package_coupon');
            });
        }

        // 10. lens_package_power_types
        if (!Schema::hasTable('lens_package_power_types')) {
            Schema::create('lens_package_power_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lens_package_id')->constrained('lens_packages')->cascadeOnDelete();
                $table->integer('power_type_cat_id');
                $table->foreign('power_type_cat_id')->references('id')->on('power_type_cat')->cascadeOnDelete();
                $table->unique(['lens_package_id', 'power_type_cat_id'], 'unique_pkg_power_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lens_package_power_types');
        Schema::dropIfExists('lens_package_coupons');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('lens_package_badges');
        Schema::dropIfExists('lens_package_media');
        Schema::dropIfExists('lens_package_benefits');
        Schema::dropIfExists('lens_benefits');
        Schema::dropIfExists('lens_package_tag_map');
        Schema::dropIfExists('lens_package_tags');
        Schema::dropIfExists('lens_packages');
    }
};
