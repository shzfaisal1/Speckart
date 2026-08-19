<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop all 6 b2c_* tables that have been consolidated into tbl_sales*.
     *
     * ⚠️  Run this ONLY after:
     *  1. Migrations 000001, 000002, 000003 have been run successfully.
     *  2. All controllers have been refactored to use tbl_sales*.
     *  3. You have verified the application works correctly.
     *  4. You have taken a full database backup.
     */
    public function up(): void
    {
        // Drop child tables first (FK constraints)
        Schema::dropIfExists('b2c_order_returns');
        Schema::dropIfExists('b2c_order_notes');
        Schema::dropIfExists('b2c_order_logs');
        Schema::dropIfExists('b2c_order_items');
        Schema::dropIfExists('b2c_order_payments');
        Schema::dropIfExists('b2c_orders');
    }

    public function down(): void
    {
        // Cannot restore dropped tables automatically.
        // Restore from database backup if needed.
    }
};
