<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class SendWhatsAppFollowup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:followup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp follow-up messages 2 days after sale';

    /**
     * Aisensy API URL (loaded from env)
     *
     * @var string
     */
    protected $aisensyUrl;

    /**
     * Aisensy API Key (loaded from env)
     *
     * @var string
     */
    protected $aisensyApiKey;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        // Define credentials in one place (this file). Prefer storing them in .env
        $this->aisensyUrl = env('AISENSY_URL', 'https://backend.aisensy.com/campaign/t1/api/v2');
        $this->aisensyApiKey = env('AISENSY_API_KEY', ''); // don't hardcode key here for production
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
 public function handle()
{
    // Target sales from 2 days ago (adjust column name if your table uses something else)
    $targetDate = Carbon::today()->subDays(2)->format('Y-m-d');

    $this->info("Fetching sales for Date: {$targetDate}");

    // NOTE: if your sales table uses a different date column (e.g., sale_date), change whereDate column accordingly.
    $sales = DB::table('tbl_sales')
        ->whereDate('sale_date', $targetDate) 
        ->where('whatsapp_followup_sent', 0)
        ->get();

    if ($sales->isEmpty()) {
        $this->info("No pending sales found for followup.");
        return 0;
    }

    foreach ($sales as $sale) {
        $this->info("Sending followup for Sale ID: {$sale->sale_id} to {$sale->contact_no}");

        // normalize mobile: remove non-digit chars and add country code if needed
        $mobile = preg_replace('/\D+/', '', (string)$sale->contact_no);
        if (strlen($mobile) == 10) {
            $mobile = '91' . $mobile;
        }

        if (strlen($mobile) < 8) {
            $this->error("Invalid mobile for Sale ID {$sale->sale_id}: {$sale->contact_no}");
            continue;
        }

        // Prepare payload (replace destination and templateParams per sale)
        $payloadArray = [
            "apiKey" => $this->aisensyApiKey ?: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY4M2Q0YTEyNGZlNTE1NWJmYmEwMDZmYyIsIm5hbWUiOiJIaW5kdXN0YW4gQ29sYXMgUHJpdmF0ZSBMaW1pdGVkIiwiYXBwTmFtZSI6IkFpU2Vuc3kiLCJjbGllbnRJZCI6IjY4M2IwODEzOTcyOTk0MGI0ZDQ0NDAxNiIsImFjdGl2ZVBsYW4iOiJGUkVFX0ZPUkVWRVIiLCJpYXQiOjE3NDg4NDcxMjJ9.UGEdNNxWo_8jQ-J2GyOdsncGXIV2jsmygPPx49SyFDM",
            "campaignName" => "login_code_api",
            "destination" => $mobile,
            "userName" => "Hindustan Colas Private Limited",
            "templateParams" => [
                (string)$sale->sale_id
            ],
            "source" => "Sales Followup Cron",
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

        // Use class property URL if set, otherwise fallback to default
        $url = $this->aisensyUrl ?: 'https://backend.aisensy.com/campaign/t1/api/v2';

        // Curl call (kept same as your snippet, per request)
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

        // Log response for debugging
        Log::info('WhatsApp followup request', [
            'sale_id' => $sale->sale_id,
            'destination' => $mobile,
            'http_code' => $httpCode,
            'payload' => $payloadArray,
            'raw_response' => $result,
            'curl_error' => $curlErr,
        ]);

        if ($curlErr) {
            $this->error("CURL error for Sale ID {$sale->sale_id}: {$curlErr}");
            continue;
        }

        $response = json_decode($result, true);
        Log::info('WhatsApp followup decoded response', ['sale_id' => $sale->sale_id, 'response' => $response]);

        // Determine success same as earlier logic
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
            $this->info("Followup sent successfully for Sale ID: {$sale->sale_id}");
        } else {
            $this->error("Failed to send followup for Sale ID: {$sale->sale_id}. Response: " . ($result ?: 'empty'));
        }

        // small sleep if you want to avoid rate limits (optional)
        // usleep(200000); // 200ms
    }

    $this->info("WhatsApp followup task completed.");
    return 0;
}

    /**
     * Send WhatsApp using curl (Aisensy API).
     *
     * @param string $mobile
     * @param string|null $userName
     * @param mixed $saleId
     * @return bool
     */
   
}