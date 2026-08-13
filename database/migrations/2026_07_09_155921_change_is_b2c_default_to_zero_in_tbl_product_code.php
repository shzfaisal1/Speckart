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
            $table->tinyInteger('is_b2c')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_product_code', function (Blueprint $table) {
            $table->tinyInteger('is_b2c')->default(1)->change();
        });
    }
};
