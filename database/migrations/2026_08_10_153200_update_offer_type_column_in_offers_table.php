<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify offer_type column in offers table to VARCHAR(100) so gift_voucher and custom offer types are allowed
        DB::statement("ALTER TABLE `offers` MODIFY COLUMN `offer_type` VARCHAR(100) NOT NULL DEFAULT 'buy1get1'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op or revert if needed
    }
};
