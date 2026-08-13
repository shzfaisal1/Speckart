<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use DB;
use Illuminate\Http\Request;

class DynamicImport implements ToCollection
{
    protected $carrierId;
    public function __construct($carrier_id)
    {
        $this->carrierId = $carrier_id;
    }
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            return;
        }
    //dd($rows);
        $carrierId = $this->carrierId;
        $chunkSize = 500;
        
        // Cache all existing zones: [name => id]
        $zoneNameToId = DB::table('city_state_zone')->pluck('id', 'name')->toArray();
        $metrocity = ['Navi mumbai_maharashtra','Delhi_delhi','Hyderabad_telangana','Bangalore_karnataka','Kolkata_west bengal','chennai_tamil nadu','Mumbai_maharashtra'];
        $e_zone_location = ["Kumily_kerala","alappuzha_kerala","Kothanalloor_kerala","Shoranur_kerala", "Iritty_kerala","Kozhenchery_kerala","Thamarassery_kerala", "Kasaragod_kerala","Thiruvananthapuram_kerala",
                    "Kollam_kerala",    "Cochin_kerala",    "Cherthala_kerala",    "Thenmala_kerala",    "Manjeshwar_kerala",    "Kallachi_kerala",    "Koyilandy_kerala",    "Kuttikol_kerala",    "Muthalamada South_kerala",
                    "Vaikom_kerala",    "Mananthavadi_kerala",    "Kalanjoor_kerala",    "Alakode_kerala",    "Karunagappally_kerala",    "Payyanur_kerala",    "Sankaramangalam_kerala",    "Malappuram_kerala",    "Palai_kerala",
                    "Mukkoottuthara_kerala",    "Ramankary_kerala",    "Venjaramoodu_kerala",    "Guruvayoor_kerala",    "Parakkadavu_kerala",    "chavara_kerala",    "Irinjalakuda_kerala",    "Kanhangad_kerala",    "mundakayam_kerala",
                    "Tharish_kerala",    "Nenmara_kerala",    "Palappilly_kerala",    "Pathanamthitta_kerala",    "Kozhikode_kerala",    "Muvattupuzha_kerala",    "Kottayam_kerala",    "Anjumoorthy_kerala",    "panickankudy_kerala",
                    "vellarimala_kerala",    "Perumbavoor_kerala",    "Nedumangad_kerala",    "Punalur_kerala",    "chengannur_kerala",    "Seethathode_kerala",    "Attingal_kerala",    "Kovilkadavu_kerala",    "Chingavanam_kerala",
                    "veliyanadu_kerala",    "Peravoor_kerala",    "Parappa_kerala",    "Vellarada_kerala",    "rajamudy_kerala",    "Chalakudy_kerala",    "Kalpetta_kerala",    "Munnar_kerala",    "Ranni_kerala",    "Kulamavu_kerala",
                    "Peruvanthanam_kerala",    "Kodungallur_kerala",    "Tiruvalla_kerala",    "cherpulassery_kerala",    "Kuthuparamba_kerala",    "Thodupuzha_kerala",    "Kannur_kerala",    "Chittar_kerala",    "Rosemala_kerala",
                    "Nuchiyad_kerala",    "Neyyattinkara_kerala",    "Palani_kerala",    "Vaduvanchal_kerala",    "kochal_kerala",    "Angamaly_kerala",    "Parimadam_kerala",    "Arimbur_kerala",    "vattoli_kerala",    "Karukachal_kerala",
                    "Nallepilly_kerala",    "Adoor_kerala",    "Karimba_kerala",    "Kottarakkara_kerala",    "Kuthumkal_kerala",    "Nilambur_kerala",    "pala_kerala",    "Tirurangadi_kerala",    "Thrissur_kerala",    "adimali_kerala",
                    "cheemeni_kerala",    "Ellapatti_kerala",    "Elamakkara_kerala",    "Edappal_kerala",    "Pulpatta_kerala",    "kiliyanthara_kerala",    "Kattappana_kerala",    "perambra_kerala",    "Parippally_kerala",
                    "changaroth_kerala",    "Pandikkad_kerala",    "Pazhayannur_kerala",    "koothattukulam_kerala",    "Kunnamkulam_kerala",    "haripad_kerala",    "Kothamangalam_kerala",    "Manjapra_kerala",    "Kallikkad_kerala",
                    "changanacherry_kerala",    "Palakkad_kerala",    "Taliparamba_kerala",    "Vellayur_kerala",    "Kuthiathode_kerala",    "Perinthalmanna_kerala",    "Kunnathunad_kerala",    "Thekkuthode_kerala",    "Kayamkulam_kerala",
                    "Valanchery_kerala",    "Chandiroor_kerala",    "Manthuka_kerala",    "iddukki_kerala",    "Naliyani_kerala",    "Kazhakkoottam_kerala",    "kochi_kerala",    "edavanna_kerala",    "Kilimanoor_kerala",    "Aluva_kerala",
                    "Tirur_kerala",    "Areacode_kerala",    "North Paravur_kerala",    "Vadakara_kerala",    "brahmamangalam_kerala",    "agali_kerala",    "Vithura_kerala",    "Pangodu_kerala",    "Sultan Bathery_kerala",
                    "arakkuparamba_kerala",    "Nedumkandam_kerala",    "Edathala_kerala",    "Akaloor_kerala",    "Ayoor_kerala",    "chempu_kerala",    "Nochad_kerala",    "Thalassery_kerala",    "Kulathupuzha_kerala",
                    "Perunad_kerala",    "Moolamattom_kerala",    "kuttiady_kerala",    "Thachanattukara_kerala",    "nileshwar_kerala","Daman_daman & diu","Rampur Rv_andaman & nicobar","Kadamtala_andaman & nicobar",
                    "Long Island Rv_andaman & nicobar","Kimois_andaman & nicobar","Laxmipur_andaman & nicobar","Kanchangarh_andaman & nicobar","Port Blair_andaman & nicobar","Kapanga_andaman & nicobar","Changua_andaman & nicobar",
                    "Govind Nagar_andaman & nicobar","Betapur_andaman & nicobar","Sitapur_andaman & nicobar","Ram Krishna Pur_andaman & nicobar","Rangat_andaman & nicobar","Koe_andaman & nicobar","Silvassa-DNH_dadra and nagar haveli",
                    "Silvassa_dadra and nagar haveli","Vapi_dadra and nagar haveli"
];

        
        foreach ($rows->chunk($chunkSize) as $chunk) {
            $headers = $chunk->first()->slice(1)->toArray();
            $records = [];
        
            foreach ($chunk->slice(1) as $row) {
                $pickupName = $row[0];
                $pickupId = $zoneNameToId[$pickupName] ?? null;
        
                if (!$pickupId) {
                    $pickupId = DB::table('city_state_zone')->insertGetId(['name' => $pickupName]);
                    $zoneNameToId[$pickupName] = $pickupId;
                }
        
                foreach ($headers as $index => $deliveryName) {
                    // dd($pickupName,$deliveryName);
                    $deliveryId = $zoneNameToId[$deliveryName] ?? null;
        
                    if (!$deliveryId) {
                        $deliveryId = DB::table('city_state_zone')->insertGetId(['name' => $deliveryName]);
                        $zoneNameToId[$deliveryName] = $deliveryId;
                    }
        
                    // Sort pickup and delivery for bidirectional consistency
                    $points = [$pickupId, $deliveryId];
                    sort($points);
                    [$normalizedPickup, $normalizedDelivery] = $points;
        
                    $value = $row[$index] ?? null;
                    
                    if (empty(array_diff([$pickupName,$deliveryName], $metrocity))) {
                        if($pickupName != $deliveryName){
                            $value = 'C';
                        }
                    }
                    
                    if (in_array($pickupName, $e_zone_location)) {
                            $value = 'E';
                    }
                    if (in_array($deliveryName, $e_zone_location)) {
                            $value = 'E';
                    }
                    
                    if($value == 'D1' || $value == 'D2'){
                        $value = 'D';
                    }
                    
                    if($value == 'C1' || $value == 'C2'){
                        $value = 'D';
                    }
                    if($value == 'F'){
                        $value = 'E';
                    }
                    
                    $records[] = [
                        'pickup_point' => $normalizedPickup,
                        'delivery_point' => $normalizedDelivery,
                        'value' => $value,
                        'carrier_id' => $carrierId
                    ];
                }
            }
        
            // Insert or update using upsert
            foreach (array_chunk($records, 1000) as $recordChunk) {
                DB::table('zone_pincode')->upsert(
                    $recordChunk,
                    ['pickup_point', 'delivery_point', 'carrier_id'], // Unique keys
                    ['value'] // Columns to update if record exists
                );
            }
        }

    }
}
