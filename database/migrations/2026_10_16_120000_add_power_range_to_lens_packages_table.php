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
        if (Schema::hasTable('lens_packages')) {
            Schema::table('lens_packages', function (Blueprint $table) {
                if (!Schema::hasColumn('lens_packages', 'min_sph')) {
                    $table->decimal('min_sph', 5, 2)->nullable()->after('design')->comment('Minimum SPH power, e.g. -6.00');
                }
                if (!Schema::hasColumn('lens_packages', 'max_sph')) {
                    $table->decimal('max_sph', 5, 2)->nullable()->after('min_sph')->comment('Maximum SPH power, e.g. +4.00');
                }
                if (!Schema::hasColumn('lens_packages', 'min_cyl')) {
                    $table->decimal('min_cyl', 5, 2)->nullable()->after('max_sph')->comment('Minimum CYL power, e.g. -2.00');
                }
                if (!Schema::hasColumn('lens_packages', 'max_cyl')) {
                    $table->decimal('max_cyl', 5, 2)->nullable()->after('min_cyl')->comment('Maximum CYL power, e.g. 0.00 or +2.00');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lens_packages')) {
            Schema::table('lens_packages', function (Blueprint $table) {
                $columns = ['min_sph', 'max_sph', 'min_cyl', 'max_cyl'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('lens_packages', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
