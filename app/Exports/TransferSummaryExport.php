<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransferSummaryExport implements FromCollection, WithHeadings, WithStyles, ShouldQueue
{
    protected $product_type, $search, $date_from, $date_to, $from_store, $to_store;

    public function __construct($product_type, $search, $date_from, $date_to, $from_store, $to_store)
    {
        $this->product_type = $product_type;
        $this->search = $search;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->from_store = $from_store;
        $this->to_store = $to_store;
    }

    public function collection()
    {
        $query = DB::table('tbl_transfer_stock as t')
            ->leftJoin('users as u', 'u.id', '=', 't.transfer_by')
            ->leftJoin('tbl_store as fs', 'fs.id', '=', 't.from_store')
            ->leftJoin('tbl_store as ts', 'ts.id', '=', 't.to_store')
            ->select(
                't.*',
                'u.name as transfer_by_name',
                'fs.store_name as from_store_name',
                'ts.store_name as to_store_name'
            )
            ->when($this->from_store, fn($q) => $q->where('t.from_store', $this->from_store))
            ->when($this->to_store, fn($q) => $q->where('t.to_store', $this->to_store))
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('t.product_details', 'like', "%{$this->search}%")
                  ->orWhere('t.product_code', 'like', "%{$this->search}%");
            }))
            ->when($this->date_from && $this->date_to, fn($q) => $q->whereBetween('t.created_at', [
                Carbon::parse($this->date_from)->startOfDay(),
                Carbon::parse($this->date_to)->endOfDay()
            ]))
            ->when($this->product_type, fn($q) => $q->where('t.product_type', $this->product_type))
            ->orderBy('t.transfer_id', 'DESC');

        $inventory = $query->get();

        if ($inventory->isEmpty()) {
            return collect();
        }

        $allData = [];

        foreach ($inventory as $stock) {
            $allData[] = [
                $stock->from_store_name,
                $stock->to_store_name,
                $stock->refrence_no,
                $stock->product_type,
                $stock->product_code,
                $stock->product_details,
                $stock->barcode_no ?? '-', // Fallback if null
                $stock->transfer_stock,
                'Rs '.number_format($stock->purchase_price, 2),
                'Rs '.number_format($stock->retail_price, 2),
                $stock->transfer_by_name,
                date('d M, Y', strtotime($stock->created_at)),
            ];
        }

        return collect($allData);
    }

    public function headings(): array
    {
        return [
            "From Store",
            "To Store",
            "Reference No",
            "Product Type",
            "Product Code",
            "Description",
            "Barcode",
            "Qty",
            "Purchase Price",
            "Retail Price",
            "Transfer By",
            "Transfer Date",
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
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

        // Wrap text for Description column (F)
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
    }
}
