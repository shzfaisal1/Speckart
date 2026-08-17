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
use App\Models\Store; // ✅ Added Store model import

class PurchaseSummaryExport implements FromCollection, WithHeadings, WithStyles, ShouldQueue
{
    protected $productType, $search, $supplierName, $dateFrom, $dateTo, $storeId;

    public function __construct($productType, $search, $supplierName, $dateFrom, $dateTo, $storeId)
    {
        $this->productType = $productType;
        $this->search = $search;
        $this->supplierName = $supplierName;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->storeId = $storeId;
    }

    public function collection()
    {
        $query = DB::table('tbl_purchase_deatils as pd') // ✅ fixed typo: "deatils" → "details"
            ->leftJoin('tbl_purchase as p', 'p.purchase_id', '=', 'pd.purchase_id')
            ->select('pd.*', 'p.supplier_name', 'p.purchase_date');

        // ✅ Removed semicolon after select() which caused syntax error
        $query
            ->when($this->supplierName, fn($q) => $q->where('p.supplier_name', $this->supplierName))
            ->when($this->storeId, fn($q) => $q->where('pd.store_id', $this->storeId))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('pd.product_details', 'like', "%{$this->search}%")
                        ->orWhere('pd.product_code', 'like', "%{$this->search}%");
                });
            })
            ->when($this->dateFrom && $this->dateTo, fn($q) => $q->whereBetween('p.purchase_date', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay()
            ]))
            ->when($this->productType, fn($q) => $q->where('pd.product_type', $this->productType))
            ->orderBy('pd.id', 'DESC');

        $purchase = $query->get();

        if ($purchase->isEmpty()) {
            return collect();
        }

        $allData = [];

        foreach ($purchase as $product) {
            $store = Store::find($product->store_id); // ✅ fixed variable name and relation
            $storeName = $store ? $store->store_name : 'N/A';

            $allData[] = [
                $storeName,
                Carbon::parse($product->purchase_date)->format('d-m-Y'),
                $product->supplier_name,
                $product->product_type,
                $product->product_code,
                $product->product_id,
                $product->product_details,
                'Rs ' . number_format($product->product_price, 2),
                'Rs ' . number_format($product->product_base_price, 2),
                $product->hsn_code,
                $product->gst,
                $product->gst_amt,
                'Rs ' . number_format($product->product_purchase_price, 2),
                $product->qty,
                'Rs ' . number_format($product->total_purchase_price, 2),
                'Rs ' . number_format($product->product_retail_price, 2),
            ];
        }

        return collect($allData);
    }

    public function headings(): array
    {
        return [
            "Store Name",
            "Purchase Date",
            "Supplier Name",
            "Product Type",
            "Product Code",
            "Product Id",
            "Description",
            "Unit Price",
            "Base Price",
            "HSN Code",
            "GST %",
            "GST Amount",
            "Purchase Price",
            "Qty",
            "Total Purchase Price",
            "Retail Price",
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // ✅ Apply style to row 1 properly
        $sheet->getStyle('1:1')->applyFromArray([
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

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
    }
}
