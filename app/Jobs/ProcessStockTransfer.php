<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class ProcessStockTransfer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $from_store;
    public $to_store;
    public $comment;
    public $items;

    public function __construct($user, $from_store, $to_store, $comment, $items)
    {
        $this->user = $user;
        $this->from_store = $from_store;
        $this->to_store = $to_store;
        $this->comment = $comment;
        $this->items = $items;
    }

    public function handle()
    {
        DB::beginTransaction();

        try {
            $referenceNo = $this->generateUniqueRandomId(8, 'tbl_transfer_stock', 'refrence_no');

            foreach ($this->items as $item) 
            {
                // --- Insert transfer record ---
                DB::table('tbl_transfer_stock')->insert([
                    'product_type'     => $item['product_type'],
                    'product_code'     => $item['product_code'],
                    'product_id'       => $item['product_id'],
                    'product_details'  => $item['product_details'],
                    'purchase_price'   => $item['purchase_price'],
                    'barcode_no'       => $item['barcode_no'],
                    'retail_price'     => $item['retail_price'],
                    'refrence_no'      => $referenceNo,
                    'from_store'       => $this->from_store,
                    'to_store'         => $this->to_store,
                    'transfer_stock'   => 1,
                    'transfer_by'      => $this->user->id,
                    'transfer_comment' => $this->comment,
                    'store_id'         => $this->user->store_id,
                    'created_at'       => now(),
                    'updated_at'       => now()
                ]);

                // --- Barcode Transfer ---
                $barcode = DB::table('tbl_barcode')
                    ->where('barcode_no', $item['barcode_no'])
                    ->where('store_id', $this->from_store)
                    ->first();

                if (!$barcode) {
                    throw new Exception("Barcode {$item['barcode_no']} not found in store {$this->from_store}");
                }

                // --- Lens-specific handling ---
                if ($item['product_type'] === 'Lens') {
                    $piece_retail = $item['retail_price'] / $barcode->perbox;

                    DB::table('tbl_barcode')
                        ->where('lens_box', $item['barcode_no'])
                        ->where('store_id', $this->from_store)
                        ->update([
                            'retail_price'    => $piece_retail,
                            'transfer_store'        => $this->to_store,
                            'recevied_ref_no' => $referenceNo,
                            'recevied_date'   => now()->toDateString(),
                            'transfer_inward_status'  => 1,
                            'outward_status'  => 4,
                            't_status'  => 1,
                            'updated_at'      => now()
                        ]);
                }

                // Update main barcode
                DB::table('tbl_barcode')
                    ->where('barcode_no', $item['barcode_no'])
                    ->where('store_id', $this->from_store)
                    ->update([
                        'retail_price'    => $item['retail_price'],
                        'transfer_store'        => $this->to_store,
                        'recevied_ref_no' => $referenceNo,
                        'recevied_date'   => now()->toDateString(),
                        'transfer_inward_status'  => 1,
                        't_status'  => 1,
                        'outward_status'  => 4,
                        'updated_at'      => now()
                    ]);

                // --- Inventory Records ---
                DB::table('tbl_inventory_record')->insert([
                    [
                        'product_code'   => $item['product_code'],
                        'product_id'     => $item['product_id'],
                        'product_type'   => $item['product_type'],
                        'product_details'=> $item['product_details'],
                        'store_id'       => $this->from_store,
                        'qty'            => 1,
                        'added_date'     => now()->toDateString(),
                        'outward_status' => 4,
                        'added_by'       => $this->user->id,
                        'created_at'     => now(),
                        'updated_at'     => now()
                    ],
                    [
                        'product_code'   => $item['product_code'],
                        'product_id'     => $item['product_id'],
                        'product_type'   => $item['product_type'],
                        'product_details'=> $item['product_details'],
                        'store_id'       => $this->to_store,
                        'qty'            => 1,
                        'added_date'     => now()->toDateString(),
                        'inward_status'  => 4,
                        'added_by'       => $this->user->id,
                        'created_at'     => now(),
                        'updated_at'     => now()
                    ]
                ]);

                // --- Barcode Activity Tracking ---
                DB::table('tbl_barcode_track_record')->insert([
                    'barcode_no'      => $item['barcode_no'],
                    'store_id'        => $this->to_store,
                    'reference_type'  => 'Transfer Barcode',
                    'action_perform'  => 'Recevied',
                    'added_by'        => $this->user->id,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ]);
                
                DB::table('tbl_barcode_track_record')->insert([
                    'barcode_no'      => $item['barcode_no'],
                    'store_id'        => $this->from_store,
                    'reference_type'  => 'Transfer Barcode',
                    'action_perform'  => 'Transfer',
                    'added_by'        => $this->user->id,
                    'created_at'      => now(),
                    'updated_at'      => now()
                ]);

                // --- Inventory: Remove from From_Store ---
                $fromInventory = DB::table('tbl_inventory_levels')
                    ->where('product_type', $item['product_type'])
                    ->where('product_code', $item['product_code'])
                    ->where('product_id', $item['product_id'])
                    ->where('product_details', $item['product_details'])
                    ->where('store_id', $this->from_store)
                    ->first();

                if (!$fromInventory) {
                    throw new Exception("Inventory record not found for product {$item['product_code']} in store {$this->from_store}");
                }

                $updateData = [
                    'available_quantity' => max(0, $fromInventory->available_quantity - 1),
                    'updated_at' => now()
                ];

                if ($item['product_type'] === 'Lens' && isset($barcode->perbox)) {
                    $updateData['tota_lens_qty'] = max(0, $fromInventory->tota_lens_qty - $barcode->perbox);
                }

                DB::table('tbl_inventory_levels')->where('id', $fromInventory->id)->update($updateData);

                // --- Inventory: Add to To_Store ---
                $toInventory = DB::table('tbl_inventory_levels')
                    ->where('product_type', $item['product_type'])
                    ->where('product_code', $item['product_code'])
                    ->where('product_id', $item['product_id'])
                    ->where('product_details', $item['product_details'])
                    ->where('store_id', $this->to_store)
                    ->first();

                if ($toInventory) {
                    $updateToData = [
                        'available_quantity' => $toInventory->available_quantity + 1,
                        'updated_at' => now()
                    ];
                    if ($item['product_type'] === 'Lens' && isset($barcode->perbox)) {
                        $updateToData['tota_lens_qty'] = $toInventory->tota_lens_qty + $barcode->perbox;
                    }
                    DB::table('tbl_inventory_levels')->where('id', $toInventory->id)->update($updateToData);
                } else {
                    $insertToData = [
                        'product_type'       => $item['product_type'],
                        'product_code'       => $item['product_code'],
                        'product_id'         => $item['product_id'],
                        'product_details'    => $item['product_details'],
                        'store_id'           => $this->to_store,
                        'available_quantity' => 1,
                        'created_at'         => now(),
                        'updated_at'         => now()
                    ];
                    if ($item['product_type'] === 'Lens' && isset($barcode->perbox)) {
                        $insertToData['tota_lens_qty'] = $barcode->perbox;
                    }
                    DB::table('tbl_inventory_levels')->insert($insertToData);
                }
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Stock transfer failed: ".$e->getMessage());
        }
    }

    private function generateUniqueRandomId($length, $table, $column)
    {
        do {
            $id = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
        } while (DB::table($table)->where($column, $id)->exists());

        return $id;
    }
}
