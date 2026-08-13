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
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'reward_type')) {
                $table->enum('reward_type', ['instant_discount', 'issue_voucher'])->default('instant_discount')->after('offer_type');
            }
            if (!Schema::hasColumn('offers', 'voucher_validity_days')) {
                $table->integer('voucher_validity_days')->nullable()->default(30)->after('reward_type');
            }
            if (!Schema::hasColumn('offers', 'usage_limit_per_user')) {
                $table->integer('usage_limit_per_user')->default(1)->after('usage_limit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (Schema::hasColumn('offers', 'reward_type')) {
                $table->dropColumn('reward_type');
            }
            if (Schema::hasColumn('offers', 'voucher_validity_days')) {
                $table->dropColumn('voucher_validity_days');
            }
            if (Schema::hasColumn('offers', 'usage_limit_per_user')) {
                $table->dropColumn('usage_limit_per_user');
            }
        });
    }
};
