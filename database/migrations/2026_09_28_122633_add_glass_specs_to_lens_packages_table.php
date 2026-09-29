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
                if (!Schema::hasColumn('lens_packages', 'company')) {
                    $table->string('company', 100)->nullable()->after('product_code');
                }
                if (!Schema::hasColumn('lens_packages', 'lens_index')) {
                    $table->string('lens_index', 50)->nullable()->after('company');
                }
                if (!Schema::hasColumn('lens_packages', 'coating')) {
                    $table->string('coating', 100)->nullable()->after('lens_index');
                }
                if (!Schema::hasColumn('lens_packages', 'material')) {
                    $table->string('material', 100)->nullable()->after('coating');
                }
                if (!Schema::hasColumn('lens_packages', 'design')) {
                    $table->string('design', 100)->nullable()->after('material');
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
                $columns = ['company', 'lens_index', 'coating', 'material', 'design'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('lens_packages', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
