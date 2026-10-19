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
        Schema::table('lens_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('lens_packages', 'track_inventory')) {
                $table->tinyInteger('track_inventory')->default(1)->after('purchase_price')->comment('1 = Yes, 0 = No');
            }
            if (!Schema::hasColumn('lens_packages', 'allow_negative_inventory')) {
                $table->tinyInteger('allow_negative_inventory')->default(1)->after('track_inventory')->comment('1 = Yes, 0 = No');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lens_packages', function (Blueprint $table) {
            if (Schema::hasColumn('lens_packages', 'allow_negative_inventory')) {
                $table->dropColumn('allow_negative_inventory');
            }
            if (Schema::hasColumn('lens_packages', 'track_inventory')) {
                $table->dropColumn('track_inventory');
            }
        });
    }
};
