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

class GSTinputSummaryExport implements FromCollection, WithHeadings, WithStyles, ShouldQueue
{
    protected $storeId, $date_type, $dateFrom, $dateTo, $sort_by;

    public function __construct($storeId, $date_type, $dateFrom, $dateTo, $sort_by)
    {
        $this->storeid = $storeId;
        $this->datetype = $date_type;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->sortby = $sort_by;
    }

    public function collection()
    {
        $query = DB::table('tbl_purchase as p')
            ->join('tbl_purchase_deatils as pd', 'pd.purchase_id', '=', 'p.purchase_id')
            ->where('p.is_Deleted', '0')
            ->select(
                'p.*',
                'pd.id as pid',
                'pd.hsn_code',
                'pd.gst_amt',
                'pd.gst',
                'pd.product_base_price',
                'pd.product_purchase_price',
                'pd.qty',
                'pd.total_purchase_price'
            );

        
        if ($this->datetype != '') 
        {
            if ($this->datetype == 0)
            {
                $query->whereBetween('p.purchase_date', [$this->dateFrom, $this->dateTo]);
            }
            else
            {
                $query->whereBetween('p.created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59']);
            }
        
        }
        else
        {
            $query->whereBetween('p.purchase_date', [$this->dateFrom, $this->dateTo]);
        }
        
        if ($this->storeid != '') 
        {
            $query->where('p.store_id', $this->storeid);
        }
        
         /* Sorting */
        if (!empty($this->sortby)) {
            $query->orderBy('pid', $this->sortby);
        } else {
            $query->orderBy('pid', 'desc');
        }

        $gstinput = $query->get();
        

        if ($gstinput->isEmpty()) {
            return collect();
        }

        $allData = [];

        foreach ($gstinput as $ginput) 
        {
            $tbl_suppliers =  DB::table("tbl_suppliers")->where('supplier_company', $ginput->supplier_name)->first();

            $allData[] = [
                $ginput->p_bill_no,
                date('d-m-Y h:i A', strtotime($ginput->created_at)),
                date('d-m-Y', strtotime($ginput->purchase_date)),
                $ginput->supplier_name,
                $tbl_suppliers->gst_no ?? '',
                $tbl_suppliers->state ?? '',
                $ginput->qty,
                $ginput->hsn_code,
                $ginput->product_base_price,
                $ginput->gst,
                number_format($ginput->gst_amt / 2, 2),
                number_format($ginput->gst_amt / 2, 2),
                '',
                number_format($ginput->gst_amt, 2),
                number_format($ginput->total_purchase_price, 2),
            ];
        }

        return collect($allData);
    }

    public function headings(): array
    {
        return [
            "Bill No",
            "Created Datetime",
            "Purchase Date",
            "Supplier Name",
            "GST No",
            "State",
            "Qty",
            "HSN/SAC",
            "Base Value ",
            "GST %",
            "SGST",
            "CGST",
            "IGST",
            "Total GST ",
            "Total Purchase",
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
