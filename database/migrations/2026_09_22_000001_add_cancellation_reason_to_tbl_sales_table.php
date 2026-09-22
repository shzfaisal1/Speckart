<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tbl_sales')) {
            Schema::table('tbl_sales', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_sales', 'cancellation_reason')) {
                    $table->text('cancellation_reason')->nullable()->after('order_status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_sales')) {
            Schema::table('tbl_sales', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_sales', 'cancellation_reason')) {
                    $table->dropColumn('cancellation_reason');
                }
            });
        }
    }
};