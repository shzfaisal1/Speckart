<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_gift_vouchers')) {
            return;
        }

        Schema::table('tbl_gift_vouchers', function (Blueprint $table) {
            // ── Lifecycle timestamps ───────────────────────────────────────────
            if (!Schema::hasColumn('tbl_gift_vouchers', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('end_date')
                      ->comment('Stored at creation time (created_at + valid_days). Queried by expiry sweep.');
            }
            if (!Schema::hasColumn('tbl_gift_vouchers', 'redeemed_at')) {
                $table->timestamp('redeemed_at')->nullable()->after('expires_at');
            }
            if (!Schema::hasColumn('tbl_gift_vouchers', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('redeemed_at');
            }
            if (!Schema::hasColumn('tbl_gift_vouchers', 'expired_at')) {
                $table->timestamp('expired_at')->nullable()->after('cancelled_at');
            }
        });

        // ── Unique constraint on source_order_no (idempotency guard) ──────────
        // Check if constraint already exists before adding
        $constraintExists = collect(DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'tbl_gift_vouchers'
               AND CONSTRAINT_NAME = 'tbl_gift_vouchers_source_order_no_unique'"
        ))->isNotEmpty();

        if (!$constraintExists) {
            // Only add unique if source_order_no column exists
            if (Schema::hasColumn('tbl_gift_vouchers', 'source_order_no')) {
                Schema::table('tbl_gift_vouchers', function (Blueprint $table) {
                    // Make NULLs not collide on unique — MySQL allows multiple NULLs in a unique column
                    $table->unique('source_order_no', 'tbl_gift_vouchers_source_order_no_unique');
                });
            }
        }

        // ── Composite index on (status, expires_at) for fast sweep queries ────
        $indexExists = collect(DB::select(
            "SELECT INDEX_NAME FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'tbl_gift_vouchers'
               AND INDEX_NAME = 'idx_gv_status_expires'"
        ))->isNotEmpty();

        if (!$indexExists) {
            Schema::table('tbl_gift_vouchers', function (Blueprint $table) {
                $table->index(['status', 'expires_at'], 'idx_gv_status_expires');
            });
        }

        // ── Backfill expires_at for any existing active records ───────────────
        DB::statement("
            UPDATE tbl_gift_vouchers
            SET expires_at = DATE_ADD(created_at, INTERVAL validity_days DAY)
            WHERE expires_at IS NULL
              AND validity_days IS NOT NULL
              AND validity_days > 0
        ");
    }

    public function down(): void
    {
        if (!Schema::hasTable('tbl_gift_vouchers')) {
            return;
        }

        Schema::table('tbl_gift_vouchers', function (Blueprint $table) {
            // Drop indexes first, then columns
            try { $table->dropIndex('idx_gv_status_expires'); } catch (\Exception $e) {}
            try { $table->dropUnique('tbl_gift_vouchers_source_order_no_unique'); } catch (\Exception $e) {}

            $cols = ['expired_at', 'cancelled_at', 'redeemed_at', 'expires_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('tbl_gift_vouchers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
