<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
class NpsController extends Controller
{
    public function index(Request $request)
    {
        
        
        $setting['page_title'] = 'NPS Dashboard';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];

     
    
    

        // ────────────────────────────────────────────────
        // Date handling - more robust
        // ────────────────────────────────────────────────
        $startDate = $request->input('date_from')
            ? Carbon::parse($request->input('date_from'))->startOfDay()->format('Y-m-d')
            : now()->startOfDay()->format('Y-m-d');

        $endDate = $request->input('date_to')
            ? Carbon::parse($request->input('date_to'))->endOfDay()->format('Y-m-d')
            : now()->endOfDay()->format('Y-m-d');

        $storeId = $request->input('store_id'); // will be used if sent

        $selectedStore = null;
        if ($storeId) {
            $selectedStore = DB::table('tbl_store')->where('id', $storeId)->first();
        }

        // ────────────────────────────────────────────────
        // Main query with filters
        // ────────────────────────────────────────────────
        $query = DB::table('nps_responses')
            ->join('nps_survey_master', 'nps_responses.survey_id', '=', 'nps_survey_master.survey_id')
            ->join('visit_transactions', 'nps_survey_master.visit_id', '=', 'visit_transactions.visit_id')
            ->leftJoin('tbl_customer', 'visit_transactions.customer_id', '=', 'tbl_customer.customer_id')
            ->leftJoin('tbl_store', 'visit_transactions.store_id', '=', 'tbl_store.id')
            ->select(
                'nps_responses.*',
                'tbl_customer.cust_name',
                'tbl_customer.contact_no',
                'tbl_store.store_name',
                'visit_transactions.store_id as vt_store_id'
            )
            ->whereBetween('nps_responses.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

      
        if ($storeId) {
            $query->where('visit_transactions.store_id', $storeId);
        }

        $responsesFlat = $query->get();
        
   

        $responses = $responsesFlat->map(function($row) {
            $row->created_at = $row->created_at ? Carbon::parse($row->created_at) : null;
            $row->survey = (object)[
                'visit' => (object)[
                    'customer' => (object)[
                        'customer_name' => $row->cust_name,
                        'mobile_no' => $row->contact_no,
                        'is_vip' => $row->is_vip ?? '',
                        'tag' => $row->tag ?? ''
                    ],
                    'store' => (object)[
                        'store_name' => $row->store_name
                    ],
                    'staff' => (object)[
                        'staff_name' => $row->staff_name ?? ''
                    ],
                    'store_id' => $row->vt_store_id
                ]
            ];
            return $row;
        });

        $totalResponses = $responses->count();
        $promoters = $responses->where('nps_score', '>=', 9)->count();
        $detractors = $responses->where('nps_score', '<=', 6)->count();
        $poorNpsCount = $responses->where('overall_score', '<', 79)->count();
        $promoter90PlusCount = $responses->where('overall_score', '>=', 90)->count();

        $overall = [
            'total_responses' => $totalResponses,
            'promoters' => $promoters,
            'detractors' => $detractors,
            'poor_nps_count' => $poorNpsCount,
            'promoter_90plus_count' => $promoter90PlusCount,
        ];

        if ($totalResponses > 0) {
            $overall['percent_promoters'] = ($promoters / $totalResponses) * 100;
            $overall['percent_detractors'] = ($detractors / $totalResponses) * 100;
            $overall['nps_score'] = $overall['percent_promoters'] - $overall['percent_detractors'];
            $overall['percent_poor'] = ($poorNpsCount / $totalResponses) * 100;
            $overall['percent_promoter_90plus'] = ($promoter90PlusCount / $totalResponses) * 100;
            $overall['avg_overall_score'] = $responses->avg('overall_score') ?? 0;

            // Question-specific averages (Ratings out of 5)
            $overall['avg_store_experience'] = $responses->avg('store_experience') ?? 0;
            $overall['avg_staff_behaviour'] = $responses->avg('staff_behaviour') ?? 0;
            $overall['avg_product_explanation'] = $responses->avg('product_explanation') ?? 0;
            $overall['avg_store_ambience'] = $responses->avg('store_ambience') ?? 0;
            $overall['avg_eye_test_experience'] = $responses->where('eye_test_experience', '>', 0)->avg('eye_test_experience') ?? 0;
            $overall['avg_billing_clarity'] = $responses->avg('billing_clarity') ?? 0;
        } else {
            $overall['percent_promoters'] = 0;
            $overall['percent_detractors'] = 0;
            $overall['nps_score'] = 0;
            $overall['percent_poor'] = 0;
            $overall['percent_promoter_90plus'] = 0;
            $overall['avg_overall_score'] = 0;
            $overall['avg_store_experience'] = 0;
            $overall['avg_staff_behaviour'] = 0;
            $overall['avg_product_explanation'] = 0;
            $overall['avg_store_ambience'] = 0;
            $overall['avg_eye_test_experience'] = 0;
            $overall['avg_billing_clarity'] = 0;
        }

        $questionRatings = [
            'Store Experience'    => $overall['avg_store_experience'],
            'Staff Behaviour'     => $overall['avg_staff_behaviour'],
            'Product Explanation' => $overall['avg_product_explanation'],
            'Store Ambience'      => $overall['avg_store_ambience'],
            'Eye Test Experience' => $overall['avg_eye_test_experience'],
            'Billing Clarity'     => $overall['avg_billing_clarity'],
        ];

$stats = $responses->groupBy(function($r) {
    return $r->survey->visit->store_id ?? 'Unknown';
})->map(function ($storeResponses, $storeId) {
    $first = $storeResponses->first();                     // ← NEW LINE

    $total = $storeResponses->count();
    $p = $storeResponses->where('nps_score', '>=', 9)->count();
    $d = $storeResponses->where('nps_score', '<=', 6)->count();
    $pp = $total > 0 ? ($p / $total) * 100 : 0;
    $pd = $total > 0 ? ($d / $total) * 100 : 0;
   
    return (object)[
        'store_id'   => $storeId,
        'store_name' => $first->survey->visit->store->store_name ?? 'Unknown Store',   // ← NEW LINE
        'total_responses' => $total,
        'promoters' => $p,
        'detractors' => $d,
        'percent_promoters' => $pp,
        'percent_detractors' => $pd,
        'nps_score' => $pp - $pd
    ];
})->values();

        $staffStats = $responses->groupBy(function($r) {
            return $r->survey->visit->staff->staff_name ?? 'Unknown';
        })->map(function ($staffResponses, $staffName) {
            $total = $staffResponses->count();
            $p = $staffResponses->where('nps_score', '>=', 9)->count();
            $d = $staffResponses->where('nps_score', '<=', 6)->count();
            $pp = $total > 0 ? ($p / $total) * 100 : 0;
            $pd = $total > 0 ? ($d / $total) * 100 : 0;

            return (object)[
                'staff_name' => $staffName ?? '',
                'total_responses' => $total,
                'nps_score' => $pp - $pd
            ];
        })->values();

        $eyeTestOfferedPercent = 0;
        $frameCleaningOfferedPercent = 0;
        if ($totalResponses > 0) {
            $eyeTestOfferedPercent = ($responses->where('eye_test_offered', true)->count() / $totalResponses) * 100;
            $frameCleaningOfferedPercent = ($responses->whereIn('old_frame_cleaning', ['Offered & Done', 'Offered & Declined'])->count() / $totalResponses) * 100;
        }

        $topComplaints = $responses->whereNotNull('improvement_needed')
            ->where('improvement_needed', '!=', '')
            ->sortByDesc('created_at')
            ->take(5);

        $pendingCasesFlat = DB::table('nps_action_log as nps_action_logs')
            ->join('nps_responses', 'nps_action_logs.response_id', '=', 'nps_responses.response_id')
            ->join('nps_survey_master', 'nps_responses.survey_id', '=', 'nps_survey_master.survey_id')
            ->join('visit_transactions', 'nps_survey_master.visit_id', '=', 'visit_transactions.visit_id')
            ->leftJoin('tbl_customer', 'visit_transactions.customer_id', '=', 'tbl_customer.customer_id')
            ->leftJoin('tbl_store', 'visit_transactions.store_id', '=', 'tbl_store.store_id')
            ->where('nps_action_logs.action_status', 'Open')
            ->where('nps_action_logs.created_at', '<=', now()->subHours(24))
            ->select(
                'nps_action_logs.*',
                'nps_responses.nps_score',
                'tbl_customer.cust_name',
                'tbl_customer.contact_no',
                'tbl_store.store_name'
            )
            ->orderBy('nps_action_logs.created_at', 'asc')
            ->get();


    //pending Message
    
    $pendingCount = DB::table('tbl_sales')
    ->join('tbl_store', 'tbl_sales.store_id', '=', 'tbl_store.id')
    ->count();

        $pendingCases = $pendingCasesFlat->map(function($row) {
            $row->created_at = Carbon::parse($row->created_at);
            $row->response = (object)[
                'nps_score' => $row->nps_score,
                'survey' => (object)[
                    'visit' => (object)[
                        'customer' => (object)[
                            'customer_name' => $row->cust_name,
                            'mobile_no' => $row->contact_no
                        ],
                        'store' => (object)[
                            'store_name' => $row->store_name
                        ]
                    ]
                ]
            ];
            return $row;
        });

        $categoryCounts = $responses->groupBy('detailed_category')->map->count();
        $totalCategories = $responses->count();

        $chartData = [
            'Excellent' => $totalCategories > 0 ? round((($categoryCounts['Excellent'] ?? 0) / $totalCategories) * 100, 1) : 0,
            'Good'      => $totalCategories > 0 ? round((($categoryCounts['Good'] ?? 0) / $totalCategories) * 100, 1) : 0,
            'Average'   => $totalCategories > 0 ? round((($categoryCounts['Average'] ?? 0) / $totalCategories) * 100, 1) : 0,
            'Poor'      => $totalCategories > 0 ? round((($categoryCounts['Poor'] ?? 0) / $totalCategories) * 100, 1) : 0,
        ];

        $recentResponses = $responses->sortByDesc('created_at')->take(20);

        $page_title = "NPS Dashboard";

        $viewData = compact(
            'stats',
            'overall',
            'staffStats',
            'eyeTestOfferedPercent',
            'frameCleaningOfferedPercent',
            'topComplaints',
            'pendingCases',
            'chartData',
            'recentResponses',
            'startDate',
            'endDate',
            'page_title',
            'selectedStore',
            'storeId',
            'questionRatings',
            'pendingCount'
        );

        if ($request->ajax()) {
            // Prepare storePieData and questionStats for the AJAX response
            $storePieData = $stats->map(function($s) {
                return [
                    'store_id' => $s->store_id,
                    'store_name' => $s->store_name,
                    'value' => $s->total_responses
                ];
            });

            $questionStats = $responses->groupBy(function($r) {
                return $r->survey->visit->store_id ?? 'Unknown';
            })->map(function ($storeResponses) {
                $total = $storeResponses->count();
                if ($total == 0) return null;

                return [
                    'eye_test_offered_percent' => ($storeResponses->where('eye_test_offered', true)->count() / $total) * 100,
                    'frame_cleaning_percent' => ($storeResponses->whereIn('old_frame_cleaning', ['Offered & Done', 'Offered & Declined'])->count() / $total) * 100,
                    'promoters_percent' => ($storeResponses->where('nps_score', '>=', 9)->count() / $total) * 100,
                    'detractors_percent' => ($storeResponses->where('nps_score', '<=', 6)->count() / $total) * 100,
                ];
            });

            $html = view('layouts.dashboard_content', $viewData)->render();
            return response()->json([
                'html'          => $html,
                'chartData'     => $chartData,
                'storePieData'  => $storePieData,
                'questionStats' => $questionStats
            ]);
        }

        // Full page render (initial load)
        return view('layouts.nps', $viewData)->with($setting);
    }
    
//     public function pendingMessages()
// {
//     $setting['page_title'] = 'Pending Message';
//     $setting['breadcrumbs'] = [
//         ['link' => url("/"), 'name' => 'Home'],
//         ['name' => $setting['page_title']],
//     ];

//     $pending = DB::table('tbl_sales')
//         ->join('tbl_store', 'tbl_sales.store_id', '=', 'tbl_store.id')
//         ->select(
//             'tbl_sales.sale_date',
//             'tbl_sales.order_no',
//             'tbl_sales.cust_name',
//             'tbl_sales.contact_no',
//             'tbl_store.store_name'
//         )
//         ->get();

//     return view('layouts.pending_message', compact('pending'))->with($setting);
// }
        
        
public function markPendingAsSent(Request $request)
{
    $targetDate = Carbon::today()->subDays(2)->format('Y-m-d');

    $sales = DB::table('tbl_sales')
        ->where('whatsapp_followup_sent', 0)
        ->get();

    if ($sales->isEmpty()) {
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No pending sales found for followup.'
            ], 200);
        }

        return redirect()->back()->with('error', 'No pending sales found for followup.');
    }

    $sentCount = 0;
    $failedCount = 0;

    foreach ($sales as $sale) {
        $this->sendFollowupForSale($sale, $sentCount, $failedCount);
    }

    $pendingCount = DB::table('tbl_sales')
        ->join('tbl_store', 'tbl_sales.store_id', '=', 'tbl_store.id')
        ->count();

    if ($request->ajax() || $request->expectsJson()) {
        return response()->json([
            'status' => 'success',
            'message' => "WhatsApp followup completed. Sent: {$sentCount}, Failed: {$failedCount}",
            'sentCount' => $sentCount,
            'failedCount' => $failedCount,
            'pendingCount' => $pendingCount,
        ]);
    }

    return redirect()->back()->with(
        'success',
        "WhatsApp followup completed. Sent: {$sentCount}, Failed: {$failedCount}"
    );
}

    private function sendFollowupForSale($sale, &$sentCount, &$failedCount)
    {
        $mobile = preg_replace('/\D+/', '', (string) $sale->contact_no);

        if (strlen($mobile) == 10) {
            $mobile = '91' . $mobile;
        }

        if (strlen($mobile) < 8) {
            Log::error("Invalid mobile for Sale ID {$sale->sale_id}: {$sale->contact_no}");
            $failedCount++;
            return;
        }

        $payloadArray = [
            "apiKey" => env('AISENSY_API_KEY', 'YOUR_DEFAULT_API_KEY'),
            "campaignName" => "login_code_api",
            "destination" => $mobile,
            "userName" => "Hindustan Colas Private Limited",
            "templateParams" => [
                (string) $sale->sale_id
            ],
            "source" => "Sales Followup Manual",
            "media" => new \stdClass(),
            "buttons" => [
                [
                    "type" => "button",
                    "sub_type" => "url",
                    "index" => 0,
                    "parameters" => [
                        [
                            "type" => "text",
                            "text" => "TESTCODE20"
                        ]
                    ]
                ]
            ],
            "carouselCards" => [],
            "location" => new \stdClass(),
            "attributes" => new \stdClass(),
            "paramsFallbackValue" => [
                "FirstName" => $sale->cust_name ?? "Customer"
            ]
        ];

        $jsonPayload = json_encode($payloadArray, JSON_UNESCAPED_UNICODE);
        $url = env('AISENSY_URL', 'https://backend.aisensy.com/campaign/t1/api/v2');

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $result = curl_exec($ch);
        $curlErr = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        Log::info('WhatsApp followup request', [
            'sale_id' => $sale->sale_id,
            'destination' => $mobile,
            'http_code' => $httpCode,
            'payload' => $payloadArray,
            'raw_response' => $result,
            'curl_error' => $curlErr,
        ]);

        if ($curlErr) {
            Log::error("CURL error for Sale ID {$sale->sale_id}: {$curlErr}");
            $failedCount++;
            return;
        }

        $response = json_decode($result, true);

        $successFlag = false;

        if (is_array($response) && array_key_exists('success', $response)) {
            $val = $response['success'];
            if ($val === true || $val === 'true' || $val === 1 || $val === '1') {
                $successFlag = true;
            }
        } elseif ($httpCode >= 200 && $httpCode < 300 && !empty($response)) {
            $successFlag = true;
        }

        if ($successFlag) {
            DB::table('tbl_sales')
                ->where('sale_id', $sale->sale_id)
                ->update(['whatsapp_followup_sent' => 1]);

            $sentCount++;
            Log::info("Followup sent successfully for Sale ID: {$sale->sale_id}");
        } else {
            $failedCount++;
            Log::error("Failed to send followup for Sale ID: {$sale->sale_id}. Response: " . ($result ?: 'empty'));
        }
    }
    
public function exportNpsData(Request $request)
{
    
    $startDate = $request->input('date_from')
        ? Carbon::parse($request->input('date_from'))->startOfDay()->format('Y-m-d')
        : now()->startOfDay()->format('Y-m-d');

    $endDate = $request->input('date_to')
        ? Carbon::parse($request->input('date_to'))->endOfDay()->format('Y-m-d')
        : now()->endOfDay()->format('Y-m-d');

    $storeId = $request->input('store_id');

    $query = DB::table('nps_responses')
        ->join('nps_survey_master', 'nps_responses.survey_id', '=', 'nps_survey_master.survey_id')
        ->join('visit_transactions', 'nps_survey_master.visit_id', '=', 'visit_transactions.visit_id')
        ->leftJoin('tbl_customer', 'visit_transactions.customer_id', '=', 'tbl_customer.customer_id')
        ->leftJoin('tbl_store', 'visit_transactions.store_id', '=', 'tbl_store.id')
        ->select(
            'nps_responses.*',
            'tbl_customer.cust_name',
            'tbl_customer.contact_no',
            'tbl_store.store_name',
            'visit_transactions.store_id as vt_store_id'
        )
        ->whereBetween('nps_responses.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

    if ($storeId) {
        $query->where('visit_transactions.store_id', $storeId);
    }

    $responsesFlat = $query->get();

    $responses = $responsesFlat->map(function ($row) {
        $row->created_at = $row->created_at ? Carbon::parse($row->created_at) : null;
        $row->survey = (object)[
            'visit' => (object)[
                'customer' => (object)[
                    'customer_name' => $row->cust_name,
                    'mobile_no' => $row->contact_no,
                    'is_vip' => $row->is_vip ?? '',
                    'tag' => $row->tag ?? ''
                ],
                'store' => (object)[
                    'store_name' => $row->store_name
                ],
                'staff' => (object)[
                    'staff_name' => $row->staff_name ?? ''
                ],
                'store_id' => $row->vt_store_id
            ]
        ];
        return $row;
    });

    $totalResponses = $responses->count();
    $promoters = $responses->where('nps_score', '>=', 9)->count();
    $detractors = $responses->where('nps_score', '<=', 6)->count();

    $overall = [
        'total_responses' => $totalResponses,
        'promoters' => $promoters,
        'detractors' => $detractors,
        'percent_promoters' => $totalResponses > 0 ? ($promoters / $totalResponses) * 100 : 0,
        'percent_detractors' => $totalResponses > 0 ? ($detractors / $totalResponses) * 100 : 0,
        'nps_score' => $totalResponses > 0
            ? (($promoters / $totalResponses) * 100) - (($detractors / $totalResponses) * 100)
            : 0,
        'avg_overall_score' => $responses->avg('overall_score') ?? 0,
        'avg_store_experience' => $responses->avg('store_experience') ?? 0,
        'avg_staff_behaviour' => $responses->avg('staff_behaviour') ?? 0,
        'avg_product_explanation' => $responses->avg('product_explanation') ?? 0,
        'avg_store_ambience' => $responses->avg('store_ambience') ?? 0,
        'avg_eye_test_experience' => $responses->where('eye_test_experience', '>', 0)->avg('eye_test_experience') ?? 0,
        'avg_billing_clarity' => $responses->avg('billing_clarity') ?? 0,
    ];

    $storeStats = $responses->groupBy(function ($r) {
        return $r->survey->visit->store_id ?? 'Unknown';
    })->map(function ($storeResponses, $storeId) {
        $first = $storeResponses->first();
        $total = $storeResponses->count();
        $p = $storeResponses->where('nps_score', '>=', 9)->count();
        $d = $storeResponses->where('nps_score', '<=', 6)->count();
        $pp = $total > 0 ? ($p / $total) * 100 : 0;
        $pd = $total > 0 ? ($d / $total) * 100 : 0;

        return [
            'store_id' => $storeId,
            'store_name' => $first->survey->visit->store->store_name ?? 'Unknown Store',
            'total_responses' => $total,
            'promoters' => $p,
            'detractors' => $d,
            'percent_promoters' => round($pp, 2),
            'percent_detractors' => round($pd, 2),
            'nps_score' => round($pp - $pd, 2),
        ];
    })->values();

    $questionRatings = [
        ['question' => 'Store Experience', 'rating' => round($overall['avg_store_experience'] * 2, 2)],
        ['question' => 'Staff Behaviour', 'rating' => round($overall['avg_staff_behaviour'] * 2, 2)],
        ['question' => 'Product Explanation', 'rating' => round($overall['avg_product_explanation'] * 2, 2)],
        ['question' => 'Store Ambience', 'rating' => round($overall['avg_store_ambience'] * 2, 2)],
        ['question' => 'Eye Test Experience', 'rating' => round($overall['avg_eye_test_experience'] * 2, 2)],
        ['question' => 'Billing Clarity', 'rating' => round($overall['avg_billing_clarity'] * 2, 2)],
    ];

    $formattedStart = Carbon::parse($startDate)->format('d-m-Y');
    $formattedEnd   = Carbon::parse($endDate)->format('d-m-Y');

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('NPS Export');

    $sheet->getColumnDimension('A')->setWidth(28);
    $sheet->getColumnDimension('B')->setWidth(22);
    $sheet->getColumnDimension('C')->setWidth(18);
    $sheet->getColumnDimension('D')->setWidth(18);
    $sheet->getColumnDimension('E')->setWidth(18);
    $sheet->getColumnDimension('F')->setWidth(18);
    $sheet->getColumnDimension('G')->setWidth(18);
    $sheet->getColumnDimension('H')->setWidth(18);

    $sheet->setCellValue('A1', 'NPS Dashboard Export');
    $sheet->mergeCells('A1:B1');
    $sheet->setCellValue('A2', 'From Date');
    $sheet->setCellValueExplicit('B2', $formattedStart, DataType::TYPE_STRING);
    $sheet->setCellValue('A3', 'To Date');
    $sheet->setCellValueExplicit('B3', $formattedEnd, DataType::TYPE_STRING);
    $sheet->setCellValue('A4', 'Store Filter');
    $sheet->setCellValue('B4', $storeId ?: 'All Stores');

    $sheet->setCellValue('A6', 'Overall Summary');
    $sheet->mergeCells('A6:B6');
    $sheet->setCellValue('A7', 'Total Responses');
    $sheet->setCellValue('B7', $overall['total_responses']);
    $sheet->setCellValue('A8', 'Promoters');
    $sheet->setCellValue('B8', $overall['promoters']);
    $sheet->setCellValue('A9', 'Detractors');
    $sheet->setCellValue('B9', $overall['detractors']);
    $sheet->setCellValue('A10', '% Promoters');
    $sheet->setCellValue('B10', number_format($overall['percent_promoters'], 2));
    $sheet->setCellValue('A11', '% Detractors');
    $sheet->setCellValue('B11', number_format($overall['percent_detractors'], 2));
    $sheet->setCellValue('A12', 'NPS Score');
    $sheet->setCellValue('B12', number_format($overall['nps_score'], 2));
    $sheet->setCellValue('A13', 'Average Overall Score (/10)');
    $sheet->setCellValue('B13', number_format($overall['avg_overall_score'] * 2, 2));

    $startRow = 15;
    $sheet->setCellValue("A{$startRow}", 'Store Ratings');
    $sheet->mergeCells("A{$startRow}:H{$startRow}");

    $headerRow = $startRow + 1;
    $sheet->fromArray([
        ['Store Name', 'Store ID', 'Total Responses', 'Promoters', 'Detractors', '% Promoters', '% Detractors', 'NPS Score']
    ], null, "A{$headerRow}");

    $row = $headerRow + 1;
    foreach ($storeStats as $store) {
        $sheet->fromArray([[
            $store['store_name'],
            $store['store_id'],
            $store['total_responses'],
            $store['promoters'],
            $store['detractors'],
            $store['percent_promoters'],
            $store['percent_detractors'],
            $store['nps_score'],
        ]], null, "A{$row}");
        $row++;
    }

    $qStart = $row + 2;
    $sheet->setCellValue("A{$qStart}", 'Question Ratings');
    $sheet->mergeCells("A{$qStart}:B{$qStart}");

    $qHeader = $qStart + 1;
    $sheet->fromArray([
        ['Question', 'Average Rating (/10)']
    ], null, "A{$qHeader}");

    $qRow = $qHeader + 1;
    foreach ($questionRatings as $q) {
        $sheet->fromArray([[$q['question'], $q['rating']]], null, "A{$qRow}");
        $qRow++;
    }

    $sheet->getStyle('A1:B1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A6:B6')->getFont()->setBold(true);
    $sheet->getStyle("A{$startRow}:H{$startRow}")->getFont()->setBold(true);
    $sheet->getStyle("A{$qStart}:B{$qStart}")->getFont()->setBold(true);

    $sheet->getStyle('A1:B4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getFont()->setBold(true);
    $sheet->getStyle("A{$qHeader}:B{$qHeader}")->getFont()->setBold(true);

    $sheet->getStyle("A1:B4")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle("A6:B13")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle("A{$headerRow}:H" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle("A{$qHeader}:B" . ($qRow - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    $sheet->getStyle("A1:B4")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EAF2FF');
    $sheet->getStyle("A6:B6")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDEBF7');
    $sheet->getStyle("A{$startRow}:H{$startRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDEBF7');
    $sheet->getStyle("A{$qStart}:B{$qStart}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDEBF7');

    $filename = 'nps_export_' . $startDate . '_to_' . $endDate . '.xlsx';

    return response()->streamDownload(function () use ($spreadsheet) {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);
}
}