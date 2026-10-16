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
                if (!Schema::hasColumn('lens_packages', 'min_axis')) {
                    $table->unsignedSmallInteger('min_axis')->nullable()->after('max_cyl')->comment('Minimum Axis angle (1 to 180)');
                }
                if (!Schema::hasColumn('lens_packages', 'max_axis')) {
                    $table->unsignedSmallInteger('max_axis')->nullable()->after('min_axis')->comment('Maximum Axis angle (1 to 180)');
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
                $columns = ['min_axis', 'max_axis'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('lens_packages', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
