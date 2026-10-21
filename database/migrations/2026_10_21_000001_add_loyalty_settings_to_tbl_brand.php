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
            if (!Schema::hasColumn('tbl_brand', 'loyalty_eligible')) {
                $table->tinyInteger('loyalty_eligible')->default(1)->after('by_one_get_one')->comment('1 = Earns Loyalty Points, 0 = Ineligible');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_brand', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_brand', 'loyalty_eligible')) {
                $table->dropColumn('loyalty_eligible');
            }
        });
    }
};
