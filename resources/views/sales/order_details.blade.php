
@php
// Unique products by product details to avoid duplicates
    $uniqueProducts = $saleproduct->unique(function ($item) {
        return $item['product_type'].'|'.
               $item['product_code'].'|'.
               $item['barcode_use'].'|'.
               $item->base_price . '|' .
               $item->discount_amt . '|' .
               $item->qty . '|' .
               $item->return_status . '|' .
               $item->no_of_glass . '|' .
               $item['product_deatils'];
    })->values();


@endphp
<div class="card-body" style="padding: 5px 10px;">
	<div class="row">
		<div class="col-md-3">
			<label for="">Sale Date <span class="text-danger">*</span></label>
			<input type="text" class="form-control" value="{{$sale->sale_date}}" readonly>
		</div>
		<div class="col-md-3">
			<label for="">Delivery  Date <span class="text-danger">*</span></label>
			<input type="text" class="form-control" value="{{$sale->delivery_date}}"  readonly>
		</div>
		 <div class="col-md-3">
			<label for="">Store <span class="text-danger">*</span></label>
			 <input type="text" class="form-control" value="{{$store->store_name}}" readonly>
		</div>
		<div class="col-md-3">
			<label for="" class="form-label">Order No: <span class="text-danger">*</span></label>
			<input type="text" class="form-control" value="{{$sale->order_no}}" name="order_no" id="order_no"  readonly>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-3">
			<label for="">Sales Person<span class="text-danger">*</span></label>
			<input type="text" class="form-control" value="{{$salePerson->name}}"  readonly>
			
		</div>
		<div class="col-md-3">
			<label for="">Tax Rule <span class="text-danger">*</span></label><br>
			<input type="text" class="form-control" value="{{$sale->tax_rule}}"  readonly>
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-md-12">
			 <div class="row">
				<div class="col-md-12">
				   <h5><strong>Customer Information</strong></h5> 
				</div> 
				<div class="col-md-3">
					<label for="" class="form-label">Mobile No: <span class="text-danger">*</span></label>
					<input type="text" class="form-control" value="{{$sale->contact_no}}" readonly>
				</div>
				<div class="col-md-3">
					<label for="" class="form-label">Full Name: <span class="text-danger">*</span></label>
					<input type="text" class="form-control" value="{{$sale->cust_name}}" readonly >
				</div>
				<div class="col-md-3">
					<label for="" class="form-label">Membership ID: </label>
					<input type="text" class="form-control" value="{{$sale->membership_id}}" readonly>
				</div>
				<div class="col-md-3">
					<label for="" class="form-label">Email Id: <span class="text-danger">*</span></label>
					<input type="text" class="form-control" value="{{$sale->email_id}}" readonly>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-3">
					<label for="" class="form-label">Customer Category:</label>
					<input type="text" class="form-control" value="{{$sale->cust_category}}" readonly>
				</div>
				<div class="col-md-3">
					<label for="" class="form-label">Gender : <span class="text-danger">*</span></label>
					<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio" id="inlineRadio8" value="Male" @if($customer->gender == 'Male') checked @endif>
					  <label class="form-check-label" for="inlineRadio8">Male</label>
					</div>
					<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio"  id="inlineRadio9" value="Female" @if($customer->gender == 'Female') checked @endif>
					  <label class="form-check-label" for="inlineRadio9">Female</label>
					</div>
					<div class="form-check form-check-inline">
					  <input class="form-check-input" type="radio"  id="inlineRadio10" value="Other" @if($customer->gender == 'Other') checked @endif>
					  <label class="form-check-label" for="inlineRadio10">Other</label>
					</div>
				</div>
				<div class="col-md-3">
					<label for="" class="form-label">Address: <span class="text-danger">*</span></label>
					<textarea type="text" class="form-control" readonly>{{$sale->cust_address}}</textarea>
				</div>
				
				<div class="col-md-3">
					<label for="" class="form-label">State: <span class="text-danger">*</span></label>
					<input type="text" class="form-control" value="{{$state->name}}" readonly>
				</div>
			</div>
			<br>
			<div class="row">
				
				<div class="col-md-3">
					<label for="" class="form-label">City: <span class="text-danger">*</span></label>
					<input type="text" class="form-control" value="{{$city->name}}" readonly>
				</div>
				
				<div class="col-md-3">
					<label for="" class="form-label">Pincode: <span class="text-danger">*</span></label>
					<input type="text" maxlength="7" class="form-control" value="{{$sale->pincode}}" readonly>
				</div>
				<div class="col-md-3">
					<label for="" class="form-label">Date of Birth: </label>
					 
					<input type="text" class="form-control" value="{{$customer->dob}}" readonly>
				</div>
				<div class="col-md-3">
					<label for="" class="form-label">Date of Anniversary: </label>
					<input type="text" class="form-control" value="{{$customer->doa}}" readonly>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-3">
					<label for="" class="form-label">GST No: </label>
					<input type="text" class="form-control"  readonly>
				</div>
				<div class="col-md-3">
					<label for="" class="form-label">Company Name: </label>
					<input type="text" class="form-control"  readonly>
				</div>
				<div class="col-md-3">
					<label for="" class="form-label">Customer Notes: </label>
					<textarea type="text"  class="form-control" readonly></textarea>
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="row" style="background: aliceblue;">
				<div class="form-group col-md-12">
					<table class="table datatables-basic w-100" id="saleTable">
						<thead>
							<tr>
								<th style="width: 3%;">#</th>
								<th style="width: 14%;">Barcode</th>
								<th style="width: 14%;">Product</th>
								<th>Product Description</th>
								<th style="width: 6%;">Qty</th>
								@if($sale->tax_rule !='Not Applicable')
								<th style="width: 9%;" class="tax-col">HSN/SAC Code</th>
								<th style="width: 7%;" class="tax-col">GST %</th>
								@endif
								<th style="width: 13%;">Item Discount</th>
								<th style="width: 9%;">Price</th>
							</tr>
						</thead>
						<tbody>
						  @foreach($saleproduct as $product) 
							<tr @if($product['return_status'] == 1) style="background-color:red" @endif</tr>
								<td>{{ $loop->iteration }}</td>
								<td>
									<input type="text" class="form-control" value="{{ $product['barcode_use'] }}" readonly>
								</td>
								<td>
								   
									<input type="text" class="form-control"  value="{{ $product['product_type'] }}">
								<td>
									<input type="text" class="form-control"  value="{{ $product['product_deatils'] }}" readonly>
									
								</td>
								<td>
									<input type="text" class="form-control"  value="{{ $product['qty'] }}"  readonly>
								</td>
								@if($sale->tax_rule !='Not Applicable')
								<td class="tax-col">
									<input type="text" class="form-control" value="{{ $product['hsn_code'] }}"  readonly>
								</td>
								<td class="tax-col">
									<input type="text" class="form-control"  value="{{ $product['gst'] }}"  readonly>
								</td>
								@endif
								
								@php
                                    $qtyMultiplier = $product['product_type'] === 'Glass' ? $product['qty'] : 1;
                                
                                    $discountAmt      = $product['discount_amt'] * $qtyMultiplier;
                                    $productDiscount  = $product['product_discount'] * $qtyMultiplier;
                                    $salePrice        = $product['sale_price'] * $qtyMultiplier;
                                @endphp
								
								<td>
                                        <div class="input-group mb-3">
                                            <input type="text"
                                                   class="form-control"
                                                   value="{{ $discountAmt }}"
                                                   style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;"
                                                   readonly>
                                    
                                            <input type="text"
                                                   class="form-control"
                                                   value="{{ $productDiscount }}"
                                                   style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;"
                                                   readonly>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <input type="text"
                                               class="form-control"
                                               value="{{ $salePrice }}"
                                               readonly>
                                    </td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				
				<div class="form-group col-md-12">
					<table class="table datatables-basic w-100">
						  <tbody>
							  <tr>
							<td colspan="10">
								<table class="table table-sm mb-0" width="100%">
									<tr>
										<!-- Left Quick Links -->
										<td colspan="4" style="width: 70%;">
											@if($sale->tax_rule !='Not Applicable')
											<div class="row" id="totalbasicdiv">
												<div class="col-md-4">
													<label for="">Total Basic Amount </label>
													<input type="text" class="form-control" value="{{$sale->total_basic_amount}}"   value="0.00" readonly>
												</div>
												<div class="col-md-4">
													<label for="">Total GST Amount </label>
													<input type="text" class="form-control" value="{{$sale->total_gst_amount}}"   value="0.00" readonly>
												</div>
											</div>
											<br>
											@endif
											
											 <h5>Payment Details</h5>  
											 <table class="tables datatables-basic w-100">
												  <thead>
													<tr>
													  <th style="color:#000">Payment Reference Number</th>
													  <th style="color:#000">Payment Mode</th>
													  <th style="color:#000">Payment Details</th>
													  <th style="color:#000">Amount</th>
													  <th style="color:#000">Payment Date</th>
													  <th style="color:#000">Created By</th>
													  <th style="color:#000">Created Date</th>
													  <th style="color:#000">Action</th>
													</tr>
												  </thead>
												  <tbody>
												    @php 
													  $salepayment = DB::table('tbl_sale_payment')->where('order_no', $sale->order_no)->where('pay_type','!=', 2)
														->orderBy('payment_id', 'ASC')
														->get();
													@endphp 
													@foreach($salepayment as $payment) 
													   @php
															$saleperson = DB::table('users')->find($payment->added_by);
														@endphp
													<tr>
														<td>{{$payment->pay_details}}</td>
														<td>{{$payment->pay_method}}</td>
														<td></td>
														<td>{{$payment->pay_amount}}</td>
														<td>{{$payment->pay_date}}</td>
														<td>{{$saleperson->name}}</td>
														<td>{{$payment->created_at}}</td>
														<td>
															<a class="tooltip pointer" data-toggle="modal" data-target="#edit-payamount-modal" data-backdrop="static" data-keyboard="false" style="text-decoration: none;">
																<img class="action-icon" src="{{asset('assets/images/icon/edit.png')}}">
																<span class="tooltip-text">Edit</span>
															</a>
															<a class="tooltip pointer payment-delete" data-id="{{$payment->payment_id}}">
																<img class="action-icon" src="{{asset('assets/images/icon/icon_block.webp')}}">
																<span class="tooltip-text">Delete</span>
															</a>
														</td>
													</tr>
													@endforeach
												  </tbody>
												</table>
												<br>
												
												<h5>Return Payment Details</h5>  
												 <table class="datatables-basic w-100">
													  <thead>
														<tr>
														  <th style="color:#000">Payment Reference Number</th>
														  <th style="color:#000">Payment Mode</th>
														  <th style="color:#000">Payment Details</th>
														  <th style="color:#000">Amount</th>
														  <th style="color:#000">Payment Date</th>
														  <th style="color:#000">Created By</th>
														  <th style="color:#000">Created Date</th>
														  <th style="color:#000">Action</th>
														</tr>
													  </thead>
													  <tbody>
														@php 
													  $salereturnpayment = DB::table('tbl_sale_payment')->where('order_no', $sale->order_no)->where('pay_type', 2)
														->orderBy('payment_id', 'ASC')
														->get();
													@endphp 
													@foreach($salereturnpayment as $payment) 
													   @php
															$saleperson = DB::table('users')->find($payment->added_by);
														@endphp
													<tr>
														<td>{{$payment->pay_details}}</td>
														<td>{{$payment->pay_method}}</td>
														<td></td>
														<td>{{$payment->pay_amount}}</td>
														<td>{{$payment->pay_date}}</td>
														<td>{{$saleperson->name}}</td>
														<td>{{$payment->created_at}}</td>
														<td>
															<a href="" class="tooltip">
																<img class="action-icon" src="{{asset('assets/images/icon/edit.png')}}">
																<span class="tooltip-text">Edit</span>
															</a>
															<a class="tooltip pointer payment-return-delete" data-id="{{$payment->payment_id}}">
																<img class="action-icon" src="{{asset('assets/images/icon/icon_block.webp')}}">
																<span class="tooltip-text">Delete</span>
															</a>
														</td>
													</tr>
													@endforeach
													  </tbody>
												</table>
											<br>
											<!-- Payment Details -->
											<div class="row">
												<div class="inline-block" style="margin-top: 25px;">
													<div style="float: left; margin-left: 15px;">
														<a class="pointer" data-toggle="modal" data-target="#customer-edit-modal" data-backdrop="static" data-keyboard="false" style="text-decoration: none;">
															<div class="quick-info" style="text-align: center;">
																<div style="padding-top: 10px;">
																	<i class="fa fa-address-card-o fa-4x" aria-hidden="true" style="margin-left: 10px;"></i>
																	<div class="text-bold" style="line-height: 18px; margin-top: 3px;">Edit<br>Customer</div>
																</div>
															</div>
														</a>
													</div>
													<div style="float: left; margin-left: 15px;">
														<a class="pointer" data-toggle="modal" data-target="#order-details-edit-modal" data-backdrop="static" data-keyboard="false" style="text-decoration: none;">
															<div class="quick-info" style="text-align: center;">
																<div style="padding-top: 10px;">
																	<i class="fa fa-edit fa-4x" aria-hidden="true" style="margin-left: 10px;"></i>
																	<div class="text-bold" style="line-height: 18px; margin-top: 3px;">Edit<br>Order Details</div>
																</div>
															</div>
														</a>
													</div>
													<div style="float: left;margin-left: 15px;">
														<a class="pointer" data-toggle="modal" data-target="#add-new-item-modal" data-backdrop="static" data-keyboard="false" style="text-decoration: none;">
															<div class="quick-info" style="text-align: center;">
																<div style="padding-top: 10px;">
																	<i class="fa fa-cart-plus fa-4x" aria-hidden="true" style="margin-left: 10px;"></i>
																	<div class="text-bold" style="line-height: 18px; margin-top: 3px;">Add<br>New Item</div>
																</div>
															</div>
														</a>
													</div>
													
													<div style="float: left;margin-left: 15px;">
														<a class="pointer" data-toggle="modal" data-target="#edit-remove-item-modal" data-backdrop="static" data-keyboard="false" style="text-decoration: none;">
															<div class="quick-info" style="text-align: center;">
																<div style="padding-top: 10px;">
																	<i class="fa fa-trash-o fa-4x" aria-hidden="true" style="margin-left: 10px;"></i>
																	<div class="text-bold" style="line-height: 18px; margin-top: 3px;">Remove<br>Items</div>
																</div>
															</div>
														</a>
													</div>
													<div style="float: left;margin-left: 15px;">
														<a class="pointer" style="text-decoration: none;"data-toggle="modal"   data-target="#edit-new-payment-modal" data-backdrop="static" data-keyboard="false" style="text-decoration: none;" >
															<div class="quick-info" style="text-align: center;">
																<div style="padding-top: 10px;">
																	<i class="fa fa-bank fa-4x" aria-hidden="true"></i>
																	<div class="text-bold" style="line-height: 18px; margin-top: 3px;">Add<br>New Payment</div>
																</div>
															</div>
														</a>
													</div>
													<div style="float: left;margin-left: 15px;">
														<a class="pointer" data-toggle="modal" data-target="#round-off-amount-edit-modal" data-backdrop="static" data-keyboard="false" style="text-decoration: none;">
															<div class="quick-info" style="text-align: center;">
																<div style="padding-top: 10px;">
																	<i class="fa fa-money fa-4x" aria-hidden="true"></i>
																	<div class="text-bold" style="line-height: 18px; margin-top: 3px;">Add/Edit<br><span class="round-off-label">Round Off</span></div>
																</div>
															</div>
														</a>
													</div>
												<!--	<div style="float: left;margin-left: 15px;">
														<a class="pointer" style="text-decoration: none;" onclick="showReturnPayment('Add', 0, '2031');">
															<div class="quick-info" style="text-align: center;">
																<div style="padding-top: 10px;">
																	<i class="fa fa-bank fa-4x" aria-hidden="true"></i>
																	<div class="text-bold" style="line-height: 18px; margin-top: 3px;">Add<br>Return Payment</div>
																</div>
															</div>
														</a>
												   </div>-->
												</div>
											</div>
										</td>
				
										<!-- Right Summary -->
										<td colspan="2">
											<table class="table table-borderless table-sm mb-0 w-100">
												<tr>
													<td class="text-end">Total item price : Rs</td>
													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->total_item_price}}"   value="0.00" readonly></td>
												</tr>
												<tr>
													<td class="text-end">Total discount : Rs</td>
													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->total_discount}}"  value="0.00" readonly></td>
												</tr>
												<tr>
													<td class="text-end">Fitting Fee : Rs</td>
													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->fitting_fee}}"  value="0.00"></td>
												</tr>
												<tr>
													<td class="text-end">Discount Coupon : Rs</td>
													<td>
														<input type="text" class="form-control form-control-sm text-end" value="{{$sale->coupon_amount}}" value="0.00" readonly>
													</td>
												</tr>
												<tr>
													<td class="text-end">Cart Discount : Rs</td>
													<td>
														<input type="text" class="form-control form-control-sm text-end" value="{{$sale->cart_discount}}" value="0.00" readonly>
														
													</td>
												</tr>
												<tr>
													<td class="text-end">Loyalty Points  : Rs</td>
													<td>
														<input type="text" class="form-control form-control-sm text-end" value="{{$sale->loyalty_point_amount}}"  value="0.00" readonly>
													</td>
												</tr>
												<tr>
													<td class="text-end">Round Off : Rs  (+/-)</td>
													<td>
														<input type="text" class="form-control form-control-sm text-end" value="{{$sale->roundoff}}"  value="0.00" readonly>
													</td>
												</tr>
												<tr>
													<td class="text-end">Total payable : Rs</td>
													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->total_payable}}"  value="0.00" readonly></td>
												</tr>
												<tr>
													<td class="text-end">Advance Amount</td>
													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->pay_amount}}"  value="0.00" readonly></td>
												</tr>
												<tr>
													<td class="text-end">Customer Credit</td>
													<td><input type="text" class="form-control form-control-sm text-end" value="{{$sale->customer_account}}"  value="0.00" readonly></td>
												</tr>
											</table>
							
											<div class="row">
												<div class="col-md-12">
													<label>Customer Has Taken External Warranty</label>
													<div class="d-flex">
														<div class="form-check form-check-inline">
															<input class="form-check-input" type="radio"  value="1"  @if($sale->extrnal_warranty == '1') checked @endif>
															<label class="form-check-label" >YES</label>
														</div>
														<div class="form-check form-check-inline">
															<input class="form-check-input" type="radio"  value="0" @if($sale->extrnal_warranty == '0') checked @endif>
															<label class="form-check-label" >NO</label>
														</div>
													</div>
												</div>
											</div>
										</td>
										
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
					</table>   
				</div>    

			</div>
		</div>
	</div>
	<hr/>
</div>


