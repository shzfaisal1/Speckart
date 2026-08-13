<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;


class PincodeUploadCSZ implements ToCollection, WithChunkReading
{
    protected $id;
    protected $city;
    protected $state;
    
    public function __construct($id)
    {
        $this->id = $id;
    }
    
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }

        $firstRow = $rows->shift(); // remove and get first row
        $combined = $firstRow[0] ?? ''; // e.g. "thane_maharashtra"
        // $checkNAme = DB::table('city_state_zone')->where('name',$combined)->first();
        // if(!$checkNAme){
            // return;
        // }
        // $this->id = $checkNAme->id;
        // dd($this->id);
        [$city, $state] = explode('_', strtolower($combined)) + [null, null];
        $this->city = $city;
        $this->state = $state;

        $data = [];

        foreach ($rows as $row) {
            $pincode = $row[0] ?? null;
            $pincodeValid = preg_match('/^[1-9][0-9]{5}$/', $pincode);
            if ($pincodeValid) {
                $data[] = [
                    'Pincode' => $pincode,
                    'City' => $this->city,
                    'StateName' => $this->state,
                    'city_state_zone' => $this->id,
                ];
            }
        }
        if (!empty($data)) {
            DB::table('tbl_pincode_master_test')->upsert($data, ['Pincode'], ['City', 'StateName', 'city_state_zone']);
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
