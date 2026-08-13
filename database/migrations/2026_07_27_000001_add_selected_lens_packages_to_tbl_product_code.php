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
            $table->json('selected_lens_packages')->nullable()->after('supported_product_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_product_code', function (Blueprint $table) {
            $table->dropColumn('selected_lens_packages');
        });
    }
};
