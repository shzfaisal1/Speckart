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

class LossSummaryExport implements FromCollection, WithHeadings, WithStyles, ShouldQueue
{
    protected $product_type, $search, $dateFrom, $dateTo, $storeid;

    public function __construct($product_type, $search, $dateFrom, $dateTo, $storeid)
    {
        $this->product_type = $product_type;
        $this->search = $search;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->storeid = $storeid;
    }

    public function collection()
    {
        $query = DB::table('tbl_barcode')
            ->where('store_id', $this->storeid)
            ->where('t_status', '0')
            ->where('loss_damage', '1')
            ->whereNull('lens_box');
        
        if (!$query)
        {
            if ($this->storeid > 0) {
                
                $query->where('transfer_store_id', $this->storeid)->where('loss_damage', '1')->whereNull('lens_box');
            }
        }
        
        if (!empty($this->product_type)) {
            $query->where('product_type', $this->product_type);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('product_details', 'like', "%{$this->search}%")
                  ->orWhere('product_code', 'like', "%{$this->search}%");
            });
        }

        if (!empty($this->dateFrom) && !empty($this->dateTo)) {
            $query->whereBetween('adj_date', [$this->dateFrom, $this->dateTo]);
        }

        $inventory = $query->select(
                'product_code',
                'product_type',
                DB::raw('MAX(product_details) AS product_details'),
                DB::raw('COUNT(*) AS total_count'),
                DB::raw('SUM(purchase_price) AS total_purchase'),
                DB::raw('MAX(store_id) AS store_id'),                // valid store_id
                DB::raw('MAX(perbox) AS perbox_detail'),       // lens box detail
                DB::raw('MAX(t_status) AS t_status')
            )
            ->groupBy('product_code', 'product_type', 'product_details')
            ->get();

        if ($inventory->isEmpty()) {
            return collect();
        }

        $allData = [];

        foreach ($inventory as $inv) 
        {
            if($inv->t_status == 0)
            {
                $store_name= Store::where('id', $inv->store_id)->first();
            }
            else
            {
                $store_name= Store::where('id', $inv->transfer_store_id)->first();
            }

            // Description with newline (Excel friendly)
            if ($inv->product_type === 'Lens') {
                $description = $inv->product_details . "\n" . "Box per piece: " . $inv->perbox_detail;
            } else {
                $description = $inv->product_details;
            }

            $allData[] = [
                $inv->product_type,
                $inv->product_code,
                $description,
                $inv->total_count,
                $inv->total_purchase,
                $store_name->store_name,
            ];
        }

        return collect($allData);
    }

    public function headings(): array
    {
        return [
            "Product",
            "Product Code",
            "Description",
            "Quantity",
            "Total Purchase",
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
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
        ]);

        // Auto-size all columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Wrap text in Description (Column C)
        $sheet->getStyle('C')->getAlignment()->setWrapText(true);
    }
}
