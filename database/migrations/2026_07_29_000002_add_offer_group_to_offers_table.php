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
            if (!Schema::hasColumn('offers', 'offer_group')) {
                $table->string('offer_group')->nullable()->after('reward_type');
            }
            if (!Schema::hasColumn('offers', 'is_mutually_exclusive')) {
                $table->boolean('is_mutually_exclusive')->default(true)->after('offer_group');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (Schema::hasColumn('offers', 'offer_group')) {
                $table->dropColumn('offer_group');
            }
            if (Schema::hasColumn('offers', 'is_mutually_exclusive')) {
                $table->dropColumn('is_mutually_exclusive');
            }
        });
    }
};
