<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessAudit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $store_id;
    protected $product_type;
    protected $company;
    protected $barcodeArray;
    protected $audit_id;

    public function __construct($user, $store_id, $product_type, $company, $barcodeArray, $audit_id)
    {
        $this->user = $user;
        $this->store_id = $store_id;
        $this->product_type = $product_type;
        $this->company = $company;
        $this->barcodeArray = $barcodeArray;
        $this->audit_id = $audit_id;
    }

    public function handle()
    {
        $totalmatch = 0;
        $totalinvalid = 0;
        $totalmissing = 0;

        $rows = DB::table('tbl_barcode')
            ->whereIn('barcode_no', $this->barcodeArray)
            ->where('product_type', $this->product_type)
            ->where('store_id', $this->store_id)
            ->get()
            ->keyBy('barcode_no');

        $barcodeRecords = [];

        foreach ($this->barcodeArray as $barcode) {
            $barcode = trim($barcode);
            if (isset($rows[$barcode])) {
                $row = $rows[$barcode];
                if ($row->refrence_type == '1') {
                    $totalmatch++;
                    $status = 0;
                } else {
                    $totalinvalid++;
                    $status = 1;
                }
            } else {
                $totalmissing++;
                $status = 2;
            }

            $barcodeRecords[] = [
                'audit_id' => $this->audit_id,
                'barcode'  => $barcode,
                'status'   => $status,
            ];
        }

        DB::table('tbl_inventory_audit_barcode')->insert($barcodeRecords);

        // Update audit record to mark processing complete
        DB::table('tbl_inventory_audit')
            ->where('id', $this->audit_id)
            ->update([
                'match_barcode'   => $totalmatch,
                'invalid_barcode' => $totalinvalid,
                'missing_barcode' => $totalmissing,
                'audit_status'    => 1, // completed
                'updated_at'      => now(),
            ]);
    }
}