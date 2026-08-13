<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendNpsMail;
use Throwable;
use Illuminate\Support\Facades\Log;

class NpsForm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Change this to a friendly signature so scheduling is easy to read.
     *
     * @var string
     */
    protected $signature = 'send:nps-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send NPS email links for sales that are 2 days old and not yet emailed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting NPS email job...');

        // Fetch sales where sale_date is exactly 2 days before today and email not sent
        $sales = DB::table('tbl_sales')
            ->whereDate('sale_date', now()->subDays(2))
           
            ->whereNotNull('email_id')
            ->where('email_id', '<>', '')
            ->limit(500) // safety limit
            ->get();

        if ($sales->isEmpty()) {
            $this->info('No sales to process.');
            return 0;
        }

        $sent = 0;
        foreach ($sales as $sale) {
            try {
                Mail::to($sale->email_id)->send(new SendNpsMail($sale));

                // mark as sent
                DB::table('tbl_sales')
                    ->where('sale_id', $sale->sale_id)
                    ->update([
                        'nps_email_sent' => 1,
                        'nps_email_sent_at' => now()
                    ]);

                $this->info("Email sent to sale_id={$sale->sale_id} ({$sale->email_id})");
                $sent++;
            } catch (Throwable $e) {
                // Log and continue
                Log::error("NPS mail failed for sale_id={$sale->sale_id}: ".$e->getMessage());
                $this->error("Failed for sale_id={$sale->sale_id}: see log");
            }
        }

        $this->info("Finished. Total emails sent: {$sent}");
        return 0;
    }
}