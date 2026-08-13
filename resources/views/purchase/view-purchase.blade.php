@extends('layouts.master')
@section('styles')
  
    
<style>
.ms-auto {
    margin-left: auto !important;
}
.spinner-grow {
    animation: spinner-grow .75s linear infinite;
    background-color: currentColor;
    border-radius: 50%;
    display: inline-block;
    height: 2rem;
    opacity: 0;
    vertical-align: -.125em;
    width: 2rem;
}
.spinner-border {
    animation: spinner-border .75s linear infinite;
    border: .25em solid;
    border-radius: 50%;
    border-right: .25em solid transparent;
    display: inline-block;
    height: 2rem;
    vertical-align: -.125em;
    width: 2rem;
}


.tooltip {
  position: relative;
  display: inline-block;
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
    width: 200px;
    background-color: black;
    color: #fff;
    /* text-align: center; */
    padding: 5px 0;
    border-radius: 6px;
    position: absolute;
    /* z-index: 1; */
    font-size: 10px !important;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
}
</style>
@endsection
@section('content')
@php
     $usr = Auth::guard()->user();
 @endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3><a href="{{route('admin.purchase-history')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i></a> Purchase Deatils</h3>
                        
                         @if ($usr->can('Purchase-History'))
                        <a href="{{route('admin.purchase-history')}}" class=" btn">
                            <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                            Purchase List
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        <!-- Row-->
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">Purchase Bill Number : {{$purchase->p_bill_no}}</h3>
					</div>
					<div class="card-body">
						
						<div class="row pt-4">
							<div class="col-lg-10 ">
								<address>
									Supplier : {{$purchase->supplier_name}}<br>
									Purchase Date : {{date("d-m-Y", strtotime($purchase->purchase_date))}}
								</address>
							</div>
							<div class="col-lg-2">
							    @php
                                    $encryptedId = base64_encode($purchase->p_bill_no);
                                    //dd($encryptedId);
                                @endphp
                                
                                <a href="{{ route('admin.purchase.edit', ['id' => $encryptedId]) }}" class="btn btn-success">
                                    <i class="si si-pencil"></i> Edit
                                </a>  
								<button type="button" class="btn btn-success"><i class="si si-printer"></i> Print</button>
							</div>    
							
						</div>
						<div class="table-responsive push">
							<table class="table table-bordered table-hover">
								<tr class=" ">
									<th class="text-center w-5">#</th>
									<th>Product</th>
									<th class="w-10" >Product Code</th>
									<th class="w-30" >Product Deatils</th>
									<th class="text-right w-5">Price</th>
									<th class="text-right w-5">Base Price</th>
									<th class="text-right w-15">GST</th>
									<th class="text-right w-10">Purchase Price</th>
									<th class="text-right w-5">Qty</th>
									<th class="text-right w-10">Total Purchase Price</th>
									<th class="text-right w-20">Retail Price</th>
								</tr>
								@foreach($purchaseproduct as $product) 
								
								<?php
								   if($product['product_type'] == 'Frame' || $product['product_type'] == 'Goggles')
                                   {
                                        $fields = [
                                            $product['product_details'] ?? null,
                                            $product['company_detail'] ?? null,
                                            $product['quality_detail'] ?? null,
                                            $product['color_details'] ?? null,
                                            $product['size_details'] ?? null,
                                            $product['Type_details'] ?? null,
                                            $product['gender_details'] ?? null,
                                            $product['shape_details'] ?? null,
                                            $product['material_detail'] ?? null,
                                            $product['bridge_Size'] ?? null,
                                        ];
                                        
                                        $filteredFields = array_filter($fields, function ($value) {
                                            return !empty($value); 
                                        });
                                        
                                        $product_details = implode(' - ', $filteredFields); 
                                   } 
                                   elseif($product['product_type'] == 'Glass')
                                   {
                                        $fields = [
                                            $product['product_details'] ?? null,
                                            $product['company_detail'] ?? null,
                                            $product['color_details'] ?? null,
                                            $product['material_detail'] ?? null,
                                            $product['coating_detail'] ?? null,
                                            $product['design_details'] ?? null,
                                            $product['index_detail'] ?? null,
                                            $product['quality_detail'] ?? null,
                                            !empty($product['sph_detail']) ? 'SPH : ' . $product['sph_detail'] : null,
                                            !empty($product['cyl_details']) ? 'CYL : ' . $product['cyl_details'] : null,
                                            !empty($product['additional_detail']) ? 'Additional : ' . $product['additional_detail'] : null,
                                            !empty($product['axis_detail']) ? 'Axis : ' . $product['axis_detail'] : null,
                                        ];
                                        
                                        $filteredFields = array_filter($fields, function ($value) {
                                            return !empty($value); 
                                        });
                                        
                                        $product_details = implode(' - ', $filteredFields); 
                                   }
                                   elseif($product['product_type'] == 'Lens')
                                   {
                                        $fields = [
                                            $product['product_details']  ?? null,
                                            $product['company_detail'] ?? null,
                                            $product['quality_detail'] ?? null,
                                            $product['Type_details'] ?? null,
                                            $product['color_details'] ?? null,
                                            $product['Number_detail'] ?? null,
                                            $product['ct_detail'] ?? null,
                                            $product['material_detail'] ?? null,
                                            !empty($product['sph_detail']) ? 'SPH : ' . $product['sph_detail'] : null,
                                            !empty($product['cyl_details']) ? 'CYL : ' . $product['cyl_details'] : null,
                                            !empty($product['additional_detail']) ? 'Additional : ' . $product['additional_detail'] : null,
                                            !empty($product['axis_detail']) ? 'Axis : ' . $product['axis_detail'] : null,
                                            'Pieces Per Box '.$product['perbox_detail'] ?? null,
                                            $product['bc_detail'] ?? null,
                                            $product['diameter_detail'] ?? null,
                                            $product['powertype_details'] ?? null,
                                        ];
                                        
                                        $filteredFields = array_filter($fields, function ($value) {
                                            return !empty($value); 
                                        });
                                        
                                        $product_details = implode(' - ', $filteredFields); 
                                   }
                                   elseif($product['product_type'] == 'Solution')
                                   {
                                        $fields = [
                                            $product['product_details'] ?? null,
                                            $product['company_detail'] ?? null,
                                            $product['quality_detail'] ?? null,
                                            $product['variant_detail'] ?? null,
                                            $product['Type_details'] ?? null,
                                            $product['packing_detail'] ?? null
                                        ];
                                        
                                        $filteredFields = array_filter($fields, function ($value) {
                                            return !empty($value); 
                                        });
                                        
                                        $product_details = implode(' - ', $filteredFields); 
                                   }
                                   elseif($product['product_type'] == 'Other')
                                   {
                                        $fields = [
                                            $product['product_details'] ?? null,
                                            $product['company_detail'] ?? null,
                                            $product['Type_details'] ?? null,
                                            $product['color_details'] ?? null,
                                            $product['shape_details'] ?? null,
                                            $product['material_detail'] ?? null,
                                            $product['packing_detail'] ?? null,
                                            $product['size_details'] ?? null,
                                        ];
                                        
                                        $filteredFields = array_filter($fields, function ($value) {
                                            return !empty($value); 
                                        });
                                        
                                        $product_details = implode(' - ', $filteredFields); 
                                   }
								?>
								<tr>
									<td class="text-center">{{ $loop->iteration }}</td>
									<td>{{ $product['product_type'] }}</td>
									<td>{{ $product['product_code'] }}</td>
									<td>
									    <p class="font-w600 mb-1">{{ $product_details }}</p>
										<div class="text-muted">HSN Code : {{ $product['hsn_code'] }}</div>
									</td>
									<td class="text-right">{{ $product['product_price'] }}</td>
									<td class="text-right">{{ $product['product_base_price'] }}</td>
									<td class="text-right">{{ $product['gst_amt'] }} | {{ $product['gst'] }} %</td>
									<td class="text-right">{{ $product['product_purchase_price'] }}</td>
									<td class="text-right">{{ $product['qty'] }}</td>
									<td class="text-right">{{ $product['total_purchase_price'] }}</td>
									<td class="text-right">{{ $product['product_retail_price'] }}</td>
								</tr>
								@endforeach

								<tr>
									<td colspan="10" class="font-w600 text-right">Total Qty</td>
									<td class="text-right">{{$purchase->total_qty}}</td>
								</tr>
								<tr>
									<td colspan="10" class="font-w600 text-right">Total Unit Amount</td>
									<td class="text-right">Rs {{$purchase->total_unit_amount}}</td>
								</tr>
								<tr>
									<td colspan="10" class="font-w600 text-right">Total Base Price</td>
									<td class="text-right">Rs {{$purchase->total_base_amount}}</td>
								</tr>
								<tr>
									<td colspan="10" class="font-w600 text-right">Total GST Amount</td>
									<td class="text-right">Rs {{$purchase->total_gst_amount}}</td>
								</tr>
								<tr>
									<td colspan="10" class="font-w600 text-right">Total Purchase</td>
									<td class="text-right">Rs {{$purchase->total_p_amount}}</td>
								</tr>
								<tr>
									<td colspan="10" class="font-w600 text-right">Round Off : (+/-)</td>
									<td class="text-right">Rs {{$purchase->round_off}}</td>
								</tr>
									<tr>
									<td colspan="10" class="font-w600 text-right">Total Net Purchase</td>
									<td class="text-right">Rs {{$purchase->net_purchase_amount}}</td>
								</tr>

							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End row-->
    </div>
</section>

@endsection

@section('scripts')

@endsection
