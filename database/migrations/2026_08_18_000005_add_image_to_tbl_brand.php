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
        Schema::table('tbl_brand', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_brand', 'image')) {
                $table->string('image', 255)->nullable()->after('brand_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_brand', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_brand', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};
