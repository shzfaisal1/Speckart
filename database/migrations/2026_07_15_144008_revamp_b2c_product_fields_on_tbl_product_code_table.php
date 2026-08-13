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
        Schema::table('tbl_product_code', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_product_code', 'lens_width')) {
                $table->string('lens_width')->nullable();
                $table->string('temple_length')->nullable();
                $table->string('frame_width')->nullable();
                $table->integer('stock_quantity')->nullable()->default(0);
                $table->string('stock_status')->nullable();
                $table->boolean('polarized')->nullable()->default(0);
                $table->string('uv_protection')->nullable();
            }
            if (Schema::hasColumn('tbl_product_code', 'sunglass_colour')) {
                $table->dropColumn('sunglass_colour');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_product_code', function (Blueprint $table) {
            $table->dropColumn([
                'lens_width', 'temple_length', 'frame_width', 'stock_quantity', 
                'stock_status', 'polarized', 'uv_protection'
            ]);
            $table->string('sunglass_colour')->nullable();
        });
    }
};
