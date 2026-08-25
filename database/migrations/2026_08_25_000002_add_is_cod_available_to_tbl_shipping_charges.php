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
        if (Schema::hasTable('tbl_shipping_charges')) {
            Schema::table('tbl_shipping_charges', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_shipping_charges', 'is_cod_available')) {
                    $table->tinyInteger('is_cod_available')
                          ->default(1)
                          ->after('amount')
                          ->comment('1 = COD Available, 0 = Prepaid Only');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tbl_shipping_charges')) {
            Schema::table('tbl_shipping_charges', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_shipping_charges', 'is_cod_available')) {
                    $table->dropColumn('is_cod_available');
                }
            });
        }
    }
};
