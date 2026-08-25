<?php

namespace App\Imports;

use App\Models\ShippingCharge;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;

class ShippingChargesImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public int $importedCount = 0;
    public int $updatedCount = 0;
    public int $skippedCount = 0;
    public array $errors = [];

    protected bool $updateExisting;

    public function __construct(bool $updateExisting = true)
    {
        $this->updateExisting = $updateExisting;
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        $now = now();
        $upsertData = [];
        $pincodesInChunk = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // account for header row
            $pincode = $this->extractPincode($row);

            if (empty($pincode)) {
                $this->skippedCount++;
                continue;
            }

            // Basic validation
            if (strlen($pincode) > 10) {
                $this->errors[] = "Row {$rowNum}: Pincode '{$pincode}' exceeds 10 characters.";
                $this->skippedCount++;
                continue;
            }

            $amount = $this->extractAmount($row);
            if ($amount < 0) {
                $this->errors[] = "Row {$rowNum}: Amount cannot be negative for pincode '{$pincode}'.";
                $this->skippedCount++;
                continue;
            }

            $isCod = $this->extractCod($row);
            $status = $this->extractStatus($row);

            // Avoid duplicate pincodes within the same uploaded file
            if (in_array($pincode, $pincodesInChunk)) {
                continue;
            }
            $pincodesInChunk[] = $pincode;

            $upsertData[] = [
                'pincode'          => $pincode,
                'amount'           => $amount,
                'is_cod_available' => $isCod,
                'status'           => $status,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        if (!empty($upsertData)) {
            // Check existing pincodes for stats tracking
            $existingPincodes = ShippingCharge::whereIn('pincode', $pincodesInChunk)->pluck('pincode')->toArray();

            if ($this->updateExisting) {
                // Upsert records
                DB::table('tbl_shipping_charges')->upsert(
                    $upsertData,
                    ['pincode'],
                    ['amount', 'is_cod_available', 'status', 'updated_at']
                );

                $updated = count($existingPincodes);
                $inserted = count($upsertData) - $updated;

                $this->updatedCount += $updated;
                $this->importedCount += $inserted;
            } else {
                // Insert only non-existing records
                $newRecords = array_values(array_filter($upsertData, function ($item) use ($existingPincodes) {
                    return !in_array($item['pincode'], $existingPincodes);
                }));

                if (!empty($newRecords)) {
                    DB::table('tbl_shipping_charges')->insert($newRecords);
                    $this->importedCount += count($newRecords);
                }

                $this->skippedCount += count($existingPincodes);
            }
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Extract and sanitize pincode from various possible column names.
     */
    private function extractPincode($row): ?string
    {
        $raw = $row['pincode'] ?? ($row['pin_code'] ?? ($row['postal_code'] ?? ($row['pincodes'] ?? null)));
        if ($raw === null || $raw === '') {
            return null;
        }

        $clean = trim((string)$raw);
        // Remove decimal if numeric was formatted as float (e.g. 400001.0)
        if (str_contains($clean, '.')) {
            $parts = explode('.', $clean);
            $clean = $parts[0];
        }

        return !empty($clean) ? $clean : null;
    }

    /**
     * Extract and parse amount from various possible column names.
     */
    private function extractAmount($row): float
    {
        $raw = $row['amount'] ?? ($row['shipping_charge'] ?? ($row['charge'] ?? ($row['shipping_fee'] ?? ($row['fee'] ?? null))));
        if ($raw === null || $raw === '') {
            return 0.00;
        }

        $str = trim((string)$raw);
        $isNegative = str_starts_with($str, '-') || str_contains($str, '-');

        $numeric = preg_replace('/[^0-9.]/', '', $str);
        if ($numeric === '') {
            return 0.00;
        }

        $val = round((float)$numeric, 2);
        return $isNegative ? -$val : $val;
    }

    /**
     * Extract COD flag (1 = available, 0 = disabled).
     */
    private function extractCod($row): int
    {
        $raw = $row['is_cod_available'] ?? ($row['cod'] ?? ($row['cod_available'] ?? ($row['is_cod'] ?? null)));
        if ($raw === null || $raw === '') {
            return 1; // default COD available
        }

        $str = strtolower(trim((string)$raw));
        if (in_array($str, ['1', 'yes', 'true', 'y', 'enabled', 'available'])) {
            return 1;
        }
        if (in_array($str, ['0', 'no', 'false', 'n', 'disabled', 'prepaid_only', 'prepaid'])) {
            return 0;
        }

        return (int)$raw > 0 ? 1 : 0;
    }

    /**
     * Extract Status flag (1 = active/serviceable, 0 = disabled).
     */
    private function extractStatus($row): int
    {
        $raw = $row['status'] ?? ($row['is_active'] ?? ($row['serviceable'] ?? ($row['is_serviceable'] ?? null)));
        if ($raw === null || $raw === '') {
            return 1; // default active
        }

        $str = strtolower(trim((string)$raw));
        if (in_array($str, ['1', 'active', 'enabled', 'serviceable', 'yes', 'true', 'y'])) {
            return 1;
        }
        if (in_array($str, ['0', 'inactive', 'disabled', 'unserviceable', 'no', 'false', 'n'])) {
            return 0;
        }

        return (int)$raw > 0 ? 1 : 0;
    }
}
