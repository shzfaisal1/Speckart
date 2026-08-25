<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShippingChargesSampleExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * Return sample data collection.
     */
    public function collection()
    {
        return collect([
            [
                'pincode'          => '400001',
                'amount'           => '60.00',
                'is_cod_available' => '1',
                'status'           => '1',
            ],
            [
                'pincode'          => '110001',
                'amount'           => '0.00',
                'is_cod_available' => '0',
                'status'           => '1',
            ],
            [
                'pincode'          => '560001',
                'amount'           => '0.00',
                'is_cod_available' => '1',
                'status'           => '1',
            ],
            [
                'pincode'          => '700001',
                'amount'           => '75.00',
                'is_cod_available' => '1',
                'status'           => '1',
            ],
            [
                'pincode'          => '999999',
                'amount'           => '100.00',
                'is_cod_available' => '0',
                'status'           => '0',
            ],
        ]);
    }

    /**
     * Table headings.
     */
    public function headings(): array
    {
        return [
            'pincode',
            'amount',
            'is_cod_available',
            'status',
        ];
    }

    /**
     * Worksheet styling.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF07484A'], // Speckart brand color
                ],
            ],
        ];
    }
}
