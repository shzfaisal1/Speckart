<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockSyncService
{
    /**
     * Get the designated E-Commerce Store record.
     * Defaults to store_id = 'SPECKART-1775' or id = 6.
     */
    public static function getEcommerceStore()
    {
        return DB::table('tbl_store')
            ->where('store_id', 'SPECKART-1775')
            ->orWhere('id', 6)
            ->first() ?? DB::table('tbl_store')->first();
    }

    /**
     * Get the designated E-Commerce Store ID (integer PK).
     */
    public static function getEcommerceStoreId(): int
    {
        $store = self::getEcommerceStore();
        return (int)($store->id ?? 6);
    }

    /**
     * Get the live available stock for a given product code from tbl_inventory_levels.
     */
    public static function getLiveStock(string $productCode, ?int $storeId = null): int
    {
        if (empty(trim($productCode))) {
            return 0;
        }

        $targetStoreId = $storeId ?? self::getEcommerceStoreId();

        $total = DB::table('tbl_inventory_levels')
            ->where('product_code', $productCode)
            ->where('store_id', $targetStoreId)
            ->sum('available_quantity');

        return max(0, (int)$total);
    }

    /**
     * Synchronize physical warehouse stock to tbl_product_code master record.
     * Updates both stock_quantity and stock_status ('in_stock' vs 'out_of_stock').
     */
    public static function syncProductStock(string $productCode, ?int $storeId = null): int
    {
        if (empty(trim($productCode))) {
            return 0;
        }

        $liveQty = self::getLiveStock($productCode, $storeId);
        $status = $liveQty > 0 ? 'in_stock' : 'out_of_stock';

        DB::table('tbl_product_code')
            ->where('product_code', $productCode)
            ->update([
                'stock_quantity' => $liveQty,
                'stock_status'   => $status,
                'updated_at'     => now(),
            ]);

        return $liveQty;
    }

    /**
     * Bulk synchronize all products in tbl_product_code against tbl_inventory_levels for the e-commerce store.
     */
    public static function syncAll(?int $storeId = null): array
    {
        $targetStoreId = $storeId ?? self::getEcommerceStoreId();

        // Fetch sum of all available quantities per product code in the target store
        $stockMap = DB::table('tbl_inventory_levels')
            ->where('store_id', $targetStoreId)
            ->groupBy('product_code')
            ->select('product_code', DB::raw('SUM(available_quantity) as total_qty'))
            ->pluck('total_qty', 'product_code')
            ->toArray();

        $updatedCount = 0;

        // Update all products matching those with positive inventory
        foreach ($stockMap as $code => $qty) {
            $intQty = max(0, (int)$qty);
            $status = $intQty > 0 ? 'in_stock' : 'out_of_stock';

            DB::table('tbl_product_code')
                ->where('product_code', $code)
                ->update([
                    'stock_quantity' => $intQty,
                    'stock_status'   => $status,
                    'updated_at'     => now(),
                ]);
            $updatedCount++;
        }

        // Update remaining products with 0 stock if they have no inventory rows in target store
        $codesWithStock = array_keys($stockMap);
        $zeroCount = DB::table('tbl_product_code')
            ->where('is_b2c', 1)
            ->whereNotIn('product_code', $codesWithStock)
            ->update([
                'stock_quantity' => 0,
                'stock_status'   => 'out_of_stock',
                'updated_at'     => now(),
            ]);

        return [
            'store_id'      => $targetStoreId,
            'stocked_skus'  => $updatedCount,
            'zero_skus'     => $zeroCount,
        ];
    }

    /**
     * Restock an order upon cancellation or return.
     * Restores physical stock in tbl_inventory_levels and synchronizes tbl_product_code master catalog.
     *
     * @param \App\Models\sale\Sale|int $orderOrSaleId
     * @param string $actionType 'cancelled'|'returned'
     * @return array ['success' => bool, 'restocked_items' => int, 'message' => string]
     */
    public static function restockOrder($orderOrSaleId, string $actionType = 'cancelled'): array
    {
        $sale = $orderOrSaleId instanceof \App\Models\sale\Sale
            ? $orderOrSaleId
            : \App\Models\sale\Sale::with('products')->find($orderOrSaleId);

        if (!$sale) {
            return ['success' => false, 'restocked_items' => 0, 'message' => 'Sale record not found'];
        }

        $storeId = (int)($sale->store_id ?: self::getEcommerceStoreId());
        $ecomStoreId = self::getEcommerceStoreId();

        $items = $sale->products;
        if (!$items || $items->isEmpty()) {
            $items = DB::table('tbl_sales_product')->where('sale_id', $sale->sale_id)->get();
        }

        $restockedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                // If item is already marked cancelled or returned, skip to prevent double-restock
                $itemStatus = is_object($item) ? ($item->item_status ?? null) : null;
                if (in_array(strtolower((string)$itemStatus), ['cancelled', 'returned'])) {
                    continue;
                }

                $code = is_object($item) ? ($item->product_code ?? ($item->frame_sku ?? null)) : null;
                $qty  = max(1, (int)(is_object($item) ? ($item->qty ?? 1) : 1));

                if (!empty($code)) {
                    // Find or create inventory row in tbl_inventory_levels
                    $invRow = DB::table('tbl_inventory_levels')
                        ->where('product_code', $code)
                        ->where('store_id', $storeId)
                        ->lockForUpdate()
                        ->first();

                    if ($invRow) {
                        $updateData = [
                            'available_quantity' => DB::raw("available_quantity + {$qty}"),
                            'updated_at'         => now(),
                        ];
                        if (($invRow->product_type ?? '') === 'Lens' && !empty($invRow->perbox)) {
                            $pieces = $qty * (int)$invRow->perbox;
                            $updateData['tota_lens_qty'] = DB::raw("COALESCE(tota_lens_qty, 0) + {$pieces}");
                        }
                        DB::table('tbl_inventory_levels')
                            ->where('id', $invRow->id)
                            ->update($updateData);
                    } else {
                        DB::table('tbl_inventory_levels')->insert([
                            'product_code'       => $code,
                            'product_id'         => is_object($item) ? ($item->product_id ?? 0) : 0,
                            'product_type'       => is_object($item) ? ($item->product_type ?? 'Frame') : 'Frame',
                            'product_details'    => is_object($item) ? ($item->product_deatils ?? $code) : $code,
                            'store_id'           => $storeId,
                            'available_quantity' => $qty,
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ]);
                    }

                    // Mark item status as cancelled/returned
                    if (is_object($item) && isset($item->id)) {
                        DB::table('tbl_sales_product')->where('id', $item->id)->update([
                            'item_status' => $actionType,
                            'updated_at'  => now(),
                        ]);
                    }

                    // Live sync website catalog if the store is the e-commerce store
                    if ($storeId === $ecomStoreId) {
                        self::syncProductStock($code, $storeId);
                    }

                    $restockedCount++;
                }
            }

            // Note the restock event in admin_note
            $restockNote = "[" . date('Y-m-d H:i') . "] Restocked {$restockedCount} item(s) to Store {$storeId} via {$actionType}";
            $existingNote = $sale->admin_note ?? '';
            $newNote = $existingNote ? ($existingNote . " | " . $restockNote) : $restockNote;

            DB::table('tbl_sales')
                ->where('sale_id', $sale->sale_id)
                ->update([
                    'admin_note' => $newNote,
                    'updated_at' => now(),
                ]);

            DB::commit();

            Log::info("StockSyncService::restockOrder - Successfully restocked Order #{$sale->order_no} ({$restockedCount} items).");
            return ['success' => true, 'restocked_items' => $restockedCount, 'message' => "Restocked {$restockedCount} item(s) successfully"];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("StockSyncService::restockOrder failed: " . $e->getMessage());
            return ['success' => false, 'restocked_items' => 0, 'message' => $e->getMessage()];
        }
    }
}
