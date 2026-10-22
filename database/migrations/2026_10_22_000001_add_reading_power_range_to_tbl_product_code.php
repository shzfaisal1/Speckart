-p0<?php

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
            if (!Schema::hasColumn('tbl_product_code', 'reading_power_from')) {
                $table->string('reading_power_from', 10)->nullable()->after('supported_product_types');
            }
            if (!Schema::hasColumn('tbl_product_code', 'reading_power_to')) {
                $table->string('reading_power_to', 10)->nullable()->after('reading_power_from');
            }
            if (!Schema::hasColumn('tbl_product_code', 'reading_powers')) {
                $table->json('reading_powers')->nullable()->after('reading_power_to');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_product_code', function (Blueprint $table) {
            $table->dropColumn(['reading_power_from', 'reading_power_to', 'reading_powers']);
        });
    }
};
