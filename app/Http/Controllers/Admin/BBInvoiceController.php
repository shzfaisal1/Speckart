<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Store;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use Hash;
use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;
use PDF;
use Carbon\Carbon;
use App\Models\product\Product;
use App\Models\Customer;
use App\Models\sale\Sale;
use App\Models\sale\SaleProduct;
use App\Models\sale\SalePayment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\OnError;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;



class BBInvoiceController extends Controller
{
    public $view_route = 'B2B';
    
    
    public function bbSalesHistory()
    {
        $setting['page_title'] = 'B2B Sales History';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/sales-history',$setting);
    }

    public function createBBInvoice()
    {
        $setting['page_title'] = 'Create New Invoice';
        $setting['breadcrumbs'] = [
            ['link' => url("/"), 'name' => 'Home'],
            ['name' => $setting['page_title']],
        ];
        return view($this->view_route.'/create-invoice',$setting);
    }
    
    
    
    public function getbbcustomer(Request $request)
    {
        $company_name = $request->company_name;
    
        $customers = DB::table('tbl_customer')
            ->where('company_name', $company_name)
            ->where('is_Deleted', 0)
            ->orderBy('customer_id', 'desc')
            ->get();
    
        $data = $customers->map(function ($p) {
    
            $state = State::find($p->state_id);
            $city  = City::find($p->city_id);
    
            return [
                'customer_id'    => $p->customer_id,
                'cust_unique_id' => $p->cust_unique_id,
                'cust_name'      => $p->cust_name,
                'email_id'       => $p->email_id,
                'gst_no'         => $p->gst_no,
                'company_name'   => $p->company_name,
                'contact_no'     => $p->contact_no,
                'cust_address'   => $p->cust_address,
                'pincode'        => $p->pincode,
                'city'           => $city->name,
                'state'          => $state->name,
                'city_id'        => $p->city_id,
                'state_id'       => $p->state_id,
            ];
        });
    
        return response()->json([
            'data' => $data
        ]);
    }

    
}    