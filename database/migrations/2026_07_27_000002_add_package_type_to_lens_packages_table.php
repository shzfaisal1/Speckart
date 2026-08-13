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
        if (Schema::hasTable('lens_packages') && !Schema::hasColumn('lens_packages', 'package_type')) {
            Schema::table('lens_packages', function (Blueprint $table) {
                $table->string('package_type', 50)->default('frame_and_lens')->after('is_free_lens');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lens_packages') && Schema::hasColumn('lens_packages', 'package_type')) {
            Schema::table('lens_packages', function (Blueprint $table) {
                $table->dropColumn('package_type');
            });
        }
    }
};
