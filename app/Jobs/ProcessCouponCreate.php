<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessCouponCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $no_of_coupon;
    public $coupon_codes_type;
    public $coupon_code;
    public $coupon_type;
    public $coupon_value;
    public $min_sale_vale;
    public $valid_from;
    public $valid_to;
    public $coupon_usages;
    public $couptype;
    public $store_id;
    public $cust_category;

    public function __construct(
        $user,
        $no_of_coupon,
        $coupon_codes_type,
        $coupon_code,
        $coupon_type,
        $coupon_value,
        $min_sale_vale,
        $valid_from,
        $valid_to,
        $coupon_usages,
        $couptype,
        $store_id,
        $cust_category
    ) {
        $this->user = $user;
        $this->no_of_coupon = $no_of_coupon;
        $this->coupon_codes_type = $coupon_codes_type;
        $this->coupon_code = $coupon_code;
        $this->coupon_type = $coupon_type;
        $this->coupon_value = $coupon_value;
        $this->min_sale_vale = $min_sale_vale;
        $this->valid_from = $valid_from;
        $this->valid_to = $valid_to;
        $this->coupon_usages = $coupon_usages;
        $this->couptype = $couptype;
        $this->store_id = $store_id;
        $this->cust_category = $cust_category;
    }

    public function handle()
    {
        try {

            if ($this->couptype == '0') {
                $this->createManualCoupons();
            } elseif ($this->couptype == '1') {
                $this->createCustomerCoupons();
            }

        } catch (\Throwable $e) {

            Log::error('Coupon Create Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * ✅ Manual Coupon Creation (Bulk Insert)
     */
    private function createManualCoupons()
    {
        $totalCoupons = (int) $this->no_of_coupon;

        $insertData = [];

        for ($i = 0; $i < $totalCoupons; $i++) {

            $coupon_code = ($this->coupon_codes_type == '1')
                ? $this->coupon_code
                : $this->generateUniqueRandomId(6);

            $insertData[] = [
                'coupon_type'          => $this->coupon_type,
                'coupon_code'          => $coupon_code,
                'coupon_value'         => $this->coupon_value,
                'min_sale_vale'        => $this->min_sale_vale,
                'valid_from'           => $this->valid_from,
                'valid_to'             => $this->valid_to,
                'coupon_usages'        => $this->coupon_usages,
                'coupon_generate_type' => '1',
                'coupon_status'        => '0',
                'added_by'             => $this->user->id,
                'store_id'             => $this->user->store_id,
                'created_at'           => now(),
                'updated_at'           => now(),
            ];
        }

        // ✅ Bulk Insert (fast)
        DB::table('tbl_coupon')->insert($insertData);
    }

    /**
     * ✅ Customer-based Coupon Creation (Chunk Processing)
     */
    private function createCustomerCoupons()
    {
        $query = DB::table('tbl_customer')->where('is_Deleted', '0');

        if (!empty($this->store_id)) {
            $query->where('store_id', $this->store_id);
        }

        if (!empty($this->cust_category)) {
            $query->where('cust_category', $this->cust_category);
        }

        // ✅ Chunk processing (memory safe)
        $query->orderBy('customer_id')->chunk(500, function ($customers) {

            $insertData = [];

            foreach ($customers as $customer) {

                $coupon_code = ($this->coupon_codes_type == '1')
                    ? $this->coupon_code
                    : $this->generateUniqueRandomId(6);

                $insertData[] = [
                    'coupon_type'          => $this->coupon_type,
                    'coupon_code'          => $coupon_code,
                    'coupon_value'         => $this->coupon_value,
                    'min_sale_vale'        => $this->min_sale_vale,
                    'valid_from'           => $this->valid_from,
                    'valid_to'             => $this->valid_to,
                    'coupon_usages'        => $this->coupon_usages,
                    'coupon_generate_type' => '2',
                    'coupon_status'        => '0',
                    'added_by'             => $this->user->id,
                    'store_id'             => $this->user->store_id,
                    'contact_no'          => $customer->contact_no,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ];
            }

            DB::table('tbl_coupon')->insert($insertData);
        });
    }

    /**
     * ✅ Unique Coupon Generator
     */
    private function generateUniqueRandomId($length = 6)
    {
        do {
            $id = strtoupper(substr(bin2hex(random_bytes(4)), 0, $length));
        } while (DB::table('tbl_coupon')->where('coupon_code', $id)->exists());

        return $id;
    }
}