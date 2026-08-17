    <!------------ PAYMENT DETAILS MODAL --------> 
    <div class="modal fade" data-backdrop="static" id="PaymentModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                  <h5>Payments</h5>  
                 <table class="table table-bordered" id="salepaymentTable">
                      <thead>
                        <tr>
                          <th style="color:#000">Payment Reference Number</th>
                          <th style="color:#000">Payment Mode</th>
                          <th style="color:#000">Payment Details</th>
                          <th style="color:#000">Amount</th>
                          <th style="color:#000">Payment Date</th>
                          <th style="color:#000">Created By</th>
                          <th style="color:#000">Created Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr><td colspan="7" class="text-center">Loading...</td></tr>
                      </tbody>
                    </table>
                    <br>
                    
                    <h5>Return Payments</h5>  
                     <table class="table table-bordered" id="returnpaymentTable">
                          <thead>
                            <tr>
                              <th style="color:#000">Payment Reference Number</th>
                              <th style="color:#000">Payment Mode</th>
                              <th style="color:#000">Payment Details</th>
                              <th style="color:#000">Amount</th>
                              <th style="color:#000">Payment Date</th>
                              <th style="color:#000">Created By</th>
                              <th style="color:#000">Created Date</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr><td colspan="7" class="text-center">Loading...</td></tr>
                          </tbody>
                        </table>
                    
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Close</button>
                </div>
            </form>
          </div>
        </div>
      </div>
  
     <!------------ UPDATE PURCHASE PRICE MODAL --------> 
    <div class="modal fade" data-backdrop="static" id="PurchaseModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
                <h5 class="modal-title" id="modalTitlep"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                 <div class="row">
                     <div class="OverDiv modal-rules-section mt-none mlr-none text-danger" style="padding: 15px;">
                        <strong>Note :</strong>
                        <ol style="margin: 0;">
                            <li>Except for Contact Lens, all products purchase prices are entered as per piece.</li>
                            <li>For Contact Lens, if product sold as box then entered the purchase price as per the box or if product sold as piece then entered the purchase price as per the piece.</li>
                        </ol>
                    </div>
                 </div>
                 <form id="numberForm" method="POST" action="{{ route('admin.updateSalesPurchasePrice') }}">
                        @csrf
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <input type="hidden" name="oid" id="oid">
                        <table class="table table-bordered" id="purchaseTable">
                          <thead>
                            <tr>
                              <th style="color:#000">#</th>
                              <th style="color:#000">Product Code</th>
                              <th style="color:#000">Product Type</th>
                              <th style="color:#000">Description</th>
                              <th style="color:#000">Old Purchase Price Per Piece (Rs )</th>
                              <th style="color:#000">Qty</th>
                              <th style="color:#000">New Purchase Price Per Piece (Rs )</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr><td colspan="7" class="text-center">Loading...</td></tr>
                          </tbody>
                        </table>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Close</button>
                     <button class="btn btn-success" type="submit" title="Next">Update  Price</button>
                </div>
            </form>
                </div>
          </div>
        </div>
      
     </div>
 
    <!------------ ORDER READY REMINDER SMS --------> 
    <div class="modal fade" data-backdrop="static" id="readyreminderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitless"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                         <div id="sms_status_response"></div>
    
                </div>
            </div>
        </div>
    </div>

    <!------------ WHATSAPP TEMPLATE DETAILS MODAL --------> 
    <div class="modal fade" data-backdrop="static" id="whatsappModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitleWhatsapp"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
            <div class="modal-body">
             <table class="table table-bordered" id="whatsappTable">
                  <thead>
                    <tr>
                      <th style="color:#000">#</th>
                      <th style="color:#000">Message Type</th>
                      <th style="color:#000">Send Type</th>
                      <th style="color:#000">Action</th>

                    </tr>
                  </thead>
                  <tbody>
                    <tr><td colspan="4" class="text-center">Loading...</td></tr>
                  </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Close</button>
            </div>
        </form>
      </div>
    </div>
  </div>

    <!------------ REDEEM DETAILS MODAL --------> 
    <div class="modal fade" id="RedeemModal" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
    
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleRedeem"></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
    
                <div class="modal-body">
    
                    <div class="alert alert-danger" id="noteSection">
                        <strong>Note :</strong>
                        <ol class="mb-0">
                            <li>Ensure SMS & WhatsApp templates are configured.</li>
                            <li>If payable balance or loyalty points is 0, redemption is not allowed.</li>
                        </ol>
                    </div>
    
                    <div class="row mb-2">
                        <div class="col-md-6" id="pointsMessage"></div>
                        <div class="col-md-6 text-right">
                            <strong>Balance Payable : ₹ <span id="payableAmount"></span></strong>
                        </div>
                    </div>
    
                    <form id="redeemForm">
    
                        <input type="hidden" id="contact_no">
                         <input type="hidden" id="orderon">
    
                        <div class="row">
                            <div class="col-md-4">
                                <label>Loyalty Points Balance</label>
                                <input type="text" class="form-control" id="availablePoints" readonly>
                            </div>
    
                            <div class="col-md-4">
                                <label>Loyalty Points Redeem</label>
                                <input type="number" class="form-control" id="redeemPoints">
                            </div>
    
                            <div class="col-md-4">
                                <label>Redeem Amount (Rs)</label>
                                <input type="text" class="form-control" id="redeemPointsAmount" readonly>
                            </div>
                        </div>
    
                        <div id="otpWrapper" class="mt-3" style="display:none">
                            <div class="row">
                                <div class="col-md-8">
                                    <label>OTP</label>
                                    <input type="text" class="form-control" id="rotp" maxlength="4">
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-success mt-4" id="sendOtpredeem">
                                        Send OTP
                                    </button>
                                </div>
                                 <div id="otp-section"
                                       style="{{ old('rotp') || $errors->has('rotp') ? '' : 'display:none' }}">
                                    <div class="d-flex justify-content-between pt-2">
                                      <p id="timer" style="margin-top:20px;font-size:14px;">
                                        Resend OTP in <span id="countdown">60</span>s
                                      </p>
                                      <button id="resend-btn" class="btn btn-primary" onclick="resendOTP()" disabled
                                              style="font-size:12px;padding:4px 14px;height:auto;max-height:fit-content;margin-top: 30px">
                                        Resend OTP
                                      </button>
                                    </div>
                                  </div>
                            </div>
                            <p class="text-danger mt-2">OTP will be sent to customer mobile number.</p>
                        </div>
    
                        <button type="button" class="btn btn-success mt-3"
                                id="confirmRedeem">
                            Redeem
                        </button>
    
                    </form>
                </div>
    
            </div>
        </div>
    </div>

    <!------------ COUPON DETAILS MODAL --------------> 
    
    <div class="modal fade" id="CouponModal" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
    
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleCoupon"></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
    
                <div class="modal-body">
    
                    <div class="alert alert-danger" id="noteSection">
                        <strong>Note :</strong>
                        <ol class="mb-0">
                            <li>You have already used one discount coupon in this order. If you update new discount coupon then old coupon which was applied will be removed and replaced with the new one..</li>
                        </ol>
                    </div>
                    <div id='oldcoupon'>
                    </div>    
                    
    
                    <div class="row mb-2">
                        <div class="col-md-6 text-right">
                            <strong>Balance Payable : ₹ <span id="pending_amount"></span></strong>
                        </div>
                    </div>
    
                    <form id="couponForm">
    
                        <input type="hidden" id="contact_no">
                        <input type="hidden" id="orderon">
                        <input type="hidden" id="payableAmount">
    
                        <div class="row">
                            <div class="col-md-4">
                                 <label for="availablePoints">Discount Coupon</label>
                                 <input type="text" class="form-control" id="DiscountCoupon" >
                            </div>
    
                            
    
                            <div class="col-md-4">
                                <label>Discount Coupon Amount (Rs)</label>
                                <input type="text" class="form-control" id="coupondiscountAmount" readonly>
                                <input type="hidden" class="form-control" id="coupon_id" readonly>
                            </div>
                        </div>
    

                        <button type="button" class="btn btn-success mt-3" id="confirmCoupon">
                            Update Discount Coupon
                        </button>
    
                    </form>
                </div>
    
            </div>
        </div>
    </div>
     
    <!------------ CART DISCOUNT DETAILS MODAL --------> 
    
    <div class="modal fade" id="CartModal" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
    
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleCart"></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
    
                <div class="modal-body">
    
                    <div class="alert alert-danger" id="noteSection">
                        <strong>Note :</strong>
                        <ol class="mb-0">
                            <li>You have already apply cart discount in this order. If you update new cart discount then old cart discount which was applied will be removed and replaced with the new one.</li>
                        </ol>
                    </div>
                    <div id='oldcart'>
                    </div>    
                    <hr/>
    
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>Total Payable : ₹ <span id="payable-Amount"></span></strong>
                        </div>
                        <div class="col-md-6">
                            <strong>Balance Payable : ₹ <span id="pending-amount"></span></strong>
                        </div>
                    </div>
    
                    <form id="cartForm">
    
                        <input type="hidden" id="contact-no">
                        <input type="hidden" id="order-no">
                        <input type="hidden" id="total-payable">

                        <div class="container">
                        <table id="datatable2" class="table card-table table-vcenter text-nowrap" style="color: #000;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="color: #6b6f80;">Discount Approval Name	</th>
                                    <th style="color: #6b6f80;">Mobile</th>
                                    <th style="color: #6b6f80;">Email</th>
                                    <th style="color: #6b6f80;">Approved Discount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $users = DB::table('users')
                                        ->where('store_id', $usr->store_id)
                                        ->where('status', '1')
                                        ->where('approve_discount','>', '0')
                                        ->get();
                                @endphp
            
                                @foreach ($users as $user)
                                    <tr>
                                        <td><input type="radio" data-approve-discount="{{ $user->approve_discount }}" data-approve-mobile="{{ $user->phone }}" name="selected_user" value="{{ $user->id }}"></td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            {{ $user->approve_discount }} %
                                            <input type="hidden" id="approvedDiscountLimit" value="{{ $user->approve_discount }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                        <div class="row" id="cartdiv" style="display:none">
                            <input type="hidden" class="form-control" name="modalCartmobile" id="modalCartmobile">
                            <div class="col-md-4">
                                <label for="">Enter Discount</label>
                                <input type="text" class="form-control" name="modalCartDiscountAmount" id="modalCartDiscountAmount"   placeholder="0.00">
                                <span>OR</span>
                                <input type="text" class="form-control" name="modalCartDiscountPercentage" id="modalCartDiscountPercentage"  placeholder="0">%
                            </div>
                             <div class="col-md-8">
                                <label for="">Reason </label>
                                <textarea class="form-control" name="modalCartDiscountOTPReason" id="modalCartDiscountOTPReason"></textarea>
                            </div>
                             <div class="row">
                                <div class="col-md-8">
                                    <label for="cart">One Time Password (OTP) </label>
                                     <input type="text" class="form-control"  id="cotp" maxlength="4" oninput="numOnly(this.id);">
                                </div>
                                <div class="col-md-4">
                                      <button type="button" class="btn btn-success" id="sendOtpcart" style="margin-top: 30px;">Send Otp</button>
                                    
                                      <div id="otp-cart-section"
                                           style="{{ old('cotp') || $errors->has('cotp') ? '' : 'display:none' }}">
                                        <div class="d-flex justify-content-between pt-2">
                                          <p id="timercart" style="margin-top:20px;font-size:14px;">
                                            Resend OTP in <span id="countdowncart">60</span>s
                                          </p>
                                          <button id="resend-btn-cart" class="btn btn-primary" onclick="resendcartOTP()" disabled
                                                  style="font-size:12px;padding:4px 14px;height:auto;max-height:fit-content;margin-top: 30px">
                                            Resend OTP
                                          </button>
                                        </div>
                                      </div>
                                </div>
                                 <p style="color:red">This OTP will be sent to Mobile number.</p>
                              </div>
                        </div>
                    </div>
    

                        <button type="button" class="btn btn-success mt-3" id="confirmCart">
                            Update Cart Discount
                        </button>
    
                    </form>
                </div>
    
            </div>
        </div>
    </div>

    <!------------ DELETE ORDER DETAILS MODAL --------> 
    
    <div class="modal fade" data-backdrop="static" id="DeleteModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
            <h5 class="modal-title" id="modalTitleDelete"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
            <div class="modal-body">
                <div class="row">     
                    <div class="alert alert-danger" id="noteSection">
                        <strong>WARNING :</strong>
                        <ol class="mb-0">
                            <li> Everything related to this order will be deleted permanently from the system and there will be no records for this order. Also, this action is not reversible. If you want, take a printout or download report for this order before deleting.</li>
                            <li style="text-decoration: underline;">System will remove below transactions related to this order.</li>
                        </ol>
                    </div>
                    <table cellpadding="0" cellspacing="0" width="100%" border="0" style="border-collapse: collapse;">
                        <tbody><tr class="pageText">
                            <td width="25%">
                                1. All vouchers in account module<br>
                                5. Courier details<br>
                            </td>
                            <td valign="top" width="25%">
                                2. Loyalty points<br>
                                6. Discount coupons<br>
                            </td>
                            <td valign="top" width="25%">
                                3. Referral payment entry<br>
                                
                            </td>
                            <td valign="top" width="25%">
                                4. Item wise tracking details<br>
                            </td>
                        </tr>
                    </tbody></table>
                    
                    <div style="margin-top: 10px; color: var(--primary-color) !important; font-size: 14px; font-weight: bold;">NOTE: System will not remove any records related to purchases, challans, products codes, customer and prescription details created because of this sales order.</div>
                    <div style="margin-top: 10px;color: red !important;">Once your order is deleted, respective Inventory, Barcode, Discount coupons and redeemed loyalty points will be restored and you will be able to use them in the system.</div>
                    
                    <div style="margin-top: 10px; color: blue; font-size: 16px; font-weight: bold;">NOTE: System will not remove any records related to purchases, challans, products codes, customer and prescription details created because of this sales order.</div>
                    <div style="margin-top: 10px;">Once your order is deleted, respective Inventory, Barcode, Discount coupons and redeemed loyalty points will be restored and you will be able to use them in the system.</div>
                    <div style="margin-top: 10px; color: #000; font-size: 16px; font-weight: bold; text-decoration: underline;">Confirmation for payment records :</div>
                    <div class="inline-block pull-left" style="width: 37%; padding: 5px 0; color: #000;">Do you want to keep the payment records of this order?&nbsp;</div>
                    <div class="inline-block" style="color: #000; width: 54%;">
                         <div class="form-group">
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="keepPaymentRecords" id="inlineRadio1" value="0" checked>
                              <label class="form-check-label" for="inlineRadio1">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="keepPaymentRecords" id="inlineRadio2" value="1">
                              <label class="form-check-label" for="inlineRadio2">No</label>
                            </div>
                        </div>
                       
                    </div>
                    <div class="pull-left" style="text-align: left; color: #000; width: 99%;">- If you select Yes, the system will keep the payment records for this order.</div>
                    <div class="pull-left" style="text-align: left; color: #000; width: 99%;">- If you select No, the payment records will be permanently deleted.</div>
                </div> 
                
               @php
                    $tbl_sms = DB::table('tbl_sms')->where('id', 1)->first();
                    $delete_contactno = '';
                
                    if ($tbl_sms->secure_otp_option == 0) {
                        $users = DB::table('users')->where('id', 1)->first();
                        $delete_contactno = $users->phone ?? '';
                    }
                
                    if ($tbl_sms->secure_otp_option == 1) {
                        $tbl_store = DB::table('tbl_store')->first();
                        $delete_contactno = $tbl_store->contact_no ?? '';
                    }
                
                    if ($tbl_sms->secure_otp_option == 2) {
                        $delete_contactno = $tbl_sms->manually_mobile_no ?? '';
                    }
                
                    // Mask mobile number (e.g., 22xxxxxx23)
                    if ($delete_contactno) {
                        $delete_contactno =
                            substr($delete_contactno, 0, 2) .
                            'xxxxxx' .
                            substr($delete_contactno, -2);
                    }
                @endphp
                <div class="row">
                    <div class="col-md-3">
                        <label for="deleteorder">One Time Password (OTP) </label>
                         <input type="text" class="form-control"  id="dotp" maxlength="4" oninput="numOnly(this.id);">
                         <input type="hidden" class="form-control"  id="delete_contactno" value="{{$delete_contactno}}">
                         <p style="color:red">
                            This OTP will be sent to the mobile number {{ $delete_contactno }}.
                        </p>
                    </div>
                    <div class="col-md-4">
                          <button type="button" class="btn btn-success" id="sendOtpdeleteorder" style="margin-top: 30px;">Send Otp</button>
                          <div id="otp-delete-section"
                               style="{{ old('dotp') || $errors->has('dotp') ? '' : 'display:none' }}">
                            <div class="d-flex justify-content-between pt-2">
                              <p id="timerdelete" style="margin-top:20px;font-size:14px;">
                                Resend OTP in <span id="countdowndelete">60</span>s
                              </p>
                              <button id="resend-btn-delete" class="btn btn-primary" onclick="resendOTPdelete()" disabled
                                      style="font-size:12px;padding:4px 14px;height:auto;max-height:fit-content;margin-top: 30px">
                                Resend OTP
                              </button>
                            </div>
                          </div>
                    </div>
                    <div class="col-md-5">
                        <label for="">Comment  </label>
                        <textarea class="form-control" name="deletordercomment" id="deletordercomment"></textarea>
                    </div>
                     <input type="hidden" id="orderid">
              </div>
              <button type="button" class="btn btn-success mt-3" id="confirmDelete">
                            Delete Order
                        </button>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Close</button>
            </div>
      </div>
    </div>
    </div>
    
    <!------------ PRESCRIPTION  DETAILS MODAL --------> 
    
    <div class="modal fade" data-backdrop="static" id="PrescriptionModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
           <div class="modal-header">
                <h5 class="modal-title" id="modalTitlePrescription"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updatePrescriptionForm" method="POST" action="{{ route('admin.prescriptionupdate') }}">
                    @csrf
            <div class="modal-body">
                <div class="row">
                    <div style="color: red; font-weight: bold; font-size: 14px;">Important Note: When you update prescription details; system will update only sales records and prescription records, there will be no adjustment or effect to inventory records. Hence, if you are following power wise inventory we suggest that you do sales return and then add fresh new sale with correct prescription details.</div>
                </div>
                
                <div id="Prescriptionglassdiv">
                    <!-- Prescriptions will be appended here -->
                </div>
                
                <button type="submit" class="btn btn-success mt-3">
                            Update Prescription
                        </button>
            </div>
            
            </form>            
            <div class="modal-footer">
                <button type="button" class="btn btn-link text-muted" data-dismiss="modal">Close</button>
            </div>
         </div>
       </div>
    </div>