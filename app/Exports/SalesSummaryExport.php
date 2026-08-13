<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesSummaryExport implements FromCollection, WithHeadings, WithStyles, ShouldQueue
{
    protected $productType, $sale_person, $sale_type, $search_by, $search_text;
    protected $storeId, $dateFrom, $dateTo, $price_from, $price_to, $gst_no, $sort_by;

    public function __construct(
        $productType,
        $sale_person,
        $sale_type,
        $search_by,
        $search_text,
        $storeId,
        $dateFrom,
        $dateTo,
        $price_from,
        $price_to,
        $gst_no,
        $sort_by
    ) {
        $this->productType = $productType;
        $this->sale_person = $sale_person;
        $this->sale_type = $sale_type;
        $this->search_by = $search_by;
        $this->search_text = $search_text;
        $this->storeId = $storeId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->price_from = $price_from;
        $this->price_to = $price_to;
        $this->gst_no = $gst_no;
        $this->sort_by = $sort_by;
    }

    public function collection()
    {
        $query = DB::table('tbl_sales_product as sd')
            ->join('tbl_sales as s', 's.sale_id', '=', 'sd.sale_id')
            ->leftJoin('tbl_store as st', 'st.id', '=', 'sd.store_id')
            ->select(
                'st.store_name',
                's.sale_date',
                's.order_no',
                's.cust_name',
                's.contact_no',
                's.email_id',
                's.cust_address',
                's.membership_id',
                'sd.product_type',
                'sd.product_code',
                'sd.product_id',
                'sd.product_deatils',
                'sd.purchase_price',
                'sd.base_price',
                'sd.retail_price',
                'sd.hsn_code',
                'sd.gst',
                'sd.gst_amount',
                'sd.discount_amt',
                'sd.qty',
                'sd.sale_price',
                's.gst_no',
                's.sale_person'
            );

        /* ================= FILTERS ================= */

        if ($this->storeId != 0) {
            $query->where('sd.store_id', $this->storeId);
        }

        if (!empty($this->productType)) {
            $query->where('sd.product_type', $this->productType);
        }

        if (!empty($this->sale_person)) {
            $query->where('s.sale_person', $this->sale_person);
        }

        if (!empty($this->dateFrom) && !empty($this->dateTo)) {
            $query->whereBetween('s.sale_date', [$this->dateFrom, $this->dateTo]);
        }

        if (!empty($this->price_from) && !empty($this->price_to)) {
            $query->whereBetween('sd.sale_price', [$this->price_from, $this->price_to]);
        }

        if (!empty($this->gst_no)) {
            $query->where('s.gst_no', $this->gst_no);
        }

        /* Optional Search */
        if (!empty($this->search_by) && !empty($this->search_text)) {
            $query->where($this->search_by, 'LIKE', '%' . $this->search_text . '%');
        }


        /* Sorting */
        if (!empty($this->sort_by)) {
            $query->orderBy('s.sale_date', $this->sort_by);
        } else {
            $query->orderBy('s.sale_date', 'desc');
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            return collect([]);
        }

        $data = [];

        foreach ($orders as $row) {

            $data[] = [
                $row->store_name ?? 'N/A',
                Carbon::parse($row->sale_date)->format('d-m-Y'),
                $row->order_no,
                $row->cust_name,
                $row->contact_no,
                $row->email_id,
                $row->cust_address,
                $row->membership_id,
                $row->product_type,
                $row->product_code,
                $row->product_id,
                $row->product_deatils,
                number_format($row->purchase_price, 2),
                number_format($row->base_price, 2),
                number_format($row->retail_price, 2),
                $row->hsn_code,
                $row->gst,
                number_format($row->gst_amount, 2),
                number_format($row->discount_amt, 2),
                $row->qty,
                number_format($row->sale_price, 2),
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            "Store Name",
            "Sale Date",
            "Order No",
            "Customer Name",
            "Contact No",
            "Email ID",
            "Address",
            "Membership ID",
            "Product Type",
            "Product Code",
            "Product ID",
            "Description",
            "Purchase Price",
            "Base Price",
            "Retail Price",
            "HSN Code",
            "GST %",
            "GST Amount",
            "Discount Price",
            "Qty",
            "Total Sale Price",
        ];
    }

    public function styles(Worksheet $sheet)
    {
        /* Header Style */
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        /* Auto column width */
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        /* Wrap text for Description column */
        $sheet->getStyle('L')->getAlignment()->setWrapText(true);

        return [];
    }
}