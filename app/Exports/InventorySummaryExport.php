<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use App\Models\Store;

class InventorySummaryExport implements FromCollection, WithHeadings, WithStyles, ShouldQueue
{
    protected $store_id, $product_type, $search, $inv_status, $storeid;

    public function __construct($store_id, $product_type, $search, $inv_status, $storeid)
    {
        $this->store_id = $store_id;
        $this->product_type = $product_type;
        $this->search = $search;
        $this->inv_status = $inv_status;
        $this->storeid = $storeid;
    }

    public function collection()
    {
        
        if(!empty($this->storeid))
        {
            $inventoryQuery = DB::table('tbl_inventory_levels')->where('store_id', $this->storeid);
        }
        else
        {
            if($this->store_id == 0)
            {
                $inventoryQuery = DB::table('tbl_inventory_levels');
            }
            else
            {
                $inventoryQuery = DB::table('tbl_inventory_levels')
                ->where('store_id', $this->store_id);
            }
        }
        


        if (!empty($this->search)) {
            $inventoryQuery->where(function ($q) {
                $q->where('product_details', 'like', "%{$this->search}%")
                  ->orWhere('product_code', 'like', "%{$this->search}%");
            });
        }

        if (!empty($this->product_type)) {
            $inventoryQuery->where('product_type', $this->product_type);
        }

        if ($this->inv_status == '2') {
            $inventoryQuery->where('available_quantity', '>', 0);
        } elseif ($this->inv_status == '3') {
            $inventoryQuery->where('available_quantity', '<', 0);
        }

        $inventory = $inventoryQuery->orderBy('id', 'ASC')->get();

        if ($inventory->isEmpty()) {
            return collect();
        }

        $allData = [];

        foreach ($inventory as $inv) {
            if ($inv->product_type == 'Lens') {
                // Safely handle total_lens_qty column
                $totalLens = property_exists($inv, 'total_lens_qty') ? $inv->total_lens_qty : 0;
                $available_quantity = $inv->available_quantity . ' (' . $totalLens . ')';
                $description = $inv->product_details . "\n" . ($inv->perbox ?? '');
            } else {
                $available_quantity = $inv->available_quantity;
                $description = $inv->product_details;
            }

            $receive_store = Store::find($inv->store_id);

            $row = [
                $inv->product_type,
                $inv->product_id,
                $inv->product_code,
                $description,
                $available_quantity,
                optional($receive_store)->store_name ?? 'N/A',
            ];

            $allData[] = $row;
        }

        return collect($allData);
    }

    public function headings(): array
    {
        return [
            "Product",
            "Product ID",
            "Product Code",
            "Description",
            "Available Quantity",
            "Store",
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
        ]);

        // Auto-size all columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Wrap text for Description column (D)
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
    }
}
