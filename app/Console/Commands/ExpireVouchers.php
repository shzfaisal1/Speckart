<?php

namespace App\Console\Commands;

use App\Services\VoucherService;
use Illuminate\Console\Command;

/**
 * ExpireVouchers
 *
 * Daily Artisan command to sweep all overdue active Gold deferred vouchers
 * and transition them to 'expired' status.
 *
 * Schedule: runs at 01:00 daily (registered in Console/Kernel.php)
 * Usage: php artisan vouchers:expire
 *
 * Without this command, no voucher will ever reach the 'expired' state,
 * which breaks the refund logic in VoucherService::calculateRefund().
 */
class ExpireVouchers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'vouchers:expire
                            {--dry-run : Preview how many vouchers would be expired without actually updating}';

    /**
     * The console command description.
     */
    protected $description = 'Expire all active Gift Vouchers whose validity window has lapsed. Run daily at 01:00.';

    public function __construct(protected VoucherService $voucherService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $this->info('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info(' Speckart • Gift Voucher Expiry Sweep');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($isDryRun) {
            $this->warn(' [DRY RUN] No database changes will be made.');

            // Count only, no update
            $count = \App\Models\GiftVoucher::where('status', 'active')
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now())
                ->count();

            $this->info(" Vouchers that would be expired: {$count}");
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info('');
            return self::SUCCESS;
        }

        $count = $this->voucherService->expireOverdueVouchers();

        if ($count === 0) {
            $this->line(' ✓ No overdue vouchers found. Nothing to expire.');
        } else {
            $this->info(" ✓ Expired {$count} voucher(s) successfully.");
        }

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('');

        return self::SUCCESS;
    }
}
