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
            if (!Schema::hasColumn('lens_packages', 'quality')) {
                $table->string('quality', 100)->nullable()->after('company');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lens_packages', function (Blueprint $table) {
            if (Schema::hasColumn('lens_packages', 'quality')) {
                $table->dropColumn('quality');
            }
        });
    }
};
