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
            if (!Schema::hasColumn('lens_packages', 'purchase_price')) {
                $table->decimal('purchase_price', 10, 2)->default(0)->after('current_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lens_packages', function (Blueprint $table) {
            if (Schema::hasColumn('lens_packages', 'purchase_price')) {
                $table->dropColumn('purchase_price');
            }
        });
    }
};
