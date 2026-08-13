<style>
    .lens-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.small-input {
    width: 40px;
}

.lens-box {
    margin-bottom: 10px;
}
</style>
<!--Barcode Modal -->
<div class="modal fade" data-backdrop="static" id="barcodeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="barcodeModalLabel">Avilable Barcode List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        <table class="table table-bordered" id="barcode-table">
          <thead>
            <tr>
              <th style="color:#000">#</th>    
              <th style="color:#000">Barcode</th>
              <th style="color:#000">Product Type</th>
              <th style="color:#000">Product Code</th>
              <th style="color:#000">Product Deatils</th>
              <th style="color:#000">Price</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      
    </div>
  </div>
</div>

<!--Redeem Loyalty Points Modal -->
<div class="modal fade" id="redeemModal" data-backdrop="static" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="redeemModalLabel">Redeem Loyalty Points</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="redeemForm">
          <div class="row">
            <label for="availablePoints">Loyalty Points Balance</label>
            <input type="text" class="form-control" id="availablePoints"  readonly>
          </div><br>
          <div class="row">
            <label  for="redeemPoints">Loyalty Points Redeem</label>
            <input type="text" class="form-control" id="redeemPoints" placeholder="0">
          </div><br>
          <div class="row">
            <label for="redeemPoints">Loyalty Points Redeem Amount (Rs )</label>
            <input type="text" class="form-control" id="redeemPointsAmount" placeholder="0.00" readonly>
          </div><br>
          <div class="row">
            <div class="col-md-8">
                <label for="redeemPoints">One Time Password (OTP) </label>
                 <input type="text" class="form-control"  id="rotp" maxlength="4" oninput="numOnly(this.id);">
            </div>
            <div class="col-md-4">
                  <button type="button" class="btn btn-success" id="sendOtpredeem" style="margin-top: 30px;">Send Otp</button>
                
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
             <p style="color:red">This OTP will be sent to Customer mobile number.</p>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="confirmRedeem">Redeem</button>
      </div>
    </div>
  </div>
</div>

<!--Coupon Apply Modal -->
<div class="modal fade" id="couponModal" data-backdrop="static" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="redeemModalLabel">Apply Discount Coupon</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="couponForm">
          <div class="row">
            <label for="availablePoints">Discount Coupon</label>
            <input type="text" class="form-control" id="DiscountCoupon" >
          </div><br>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="confirmCoupon">Apply</button>
      </div>

    </div>
  </div>
</div>

<!--Cart Discount Modal -->
<div class="modal fade" id="cartModal" data-backdrop="static" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="redeemModalLabel">Apply Cart Discount</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>

     <div class="modal-body">
        <form id="cartForm">
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
                    <input type="text" class="form-control" name="modalCartmobile" id="modalCartmobile">
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
        </form>
     </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="confirmCart">Apply</button>
      </div>

    </div>
  </div>
</div>

<!--Main Product Modal -->

<div class="modal fade" id="productModal" data-backdrop="static" tabindex="-2" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Product Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" id="branddiv">
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="brand_type" value="1" checked>
                              <label class="form-check-label">Other Brand</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="brand_type" value="0">
                              <label class="form-check-label">In House Brand</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="inhousepackage" style="display:none">
                    <div class="row" style="margin-top: 10px;">
                         <div class="col-md-6">
                         <label for="">Select Lens <span class="text-danger">*</span></label>
                            <div class="d-flex">
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="lenstype" id="inlineRadio4" value="Single Vision">
                                  <label class="form-check-label" for="inlineRadio4">Single Vision</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="lenstype" id="inlineRadio5" value="Bifocal/Progressive">
                                  <label class="form-check-label" for="inlineRadio5">Bifocal/Progressive</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="lenstype" id="inlineRadio6" value="Zero Power">
                                  <label class="form-check-label" for="inlineRadio6">Zero Power</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="lenstype" id="inlineRadio7" value="Reading Power">
                                  <label class="form-check-label" for="inlineRadio7">Reading Power</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="lenspackage"></div>
                    <div id="glasscoating"></div>
                </div>
                <div class="row" style="margin-top:10px">
                    <div class="col-md-3">
                        <label>Product</label>
                        <input type="text" id="modal_product_type" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="codediv" style="display:none">
                        <label>Product Code</label>
                        <input type="text" id="modal_product_code" class="form-control" readonly>
                         <div class="suggestion-box list-group" style="display:none; position:absolute; z-index:1000;"></div>
                    </div>
                    <div class="col-md-3" id="iddiv" style="display:none">
                        <label>Product ID</label>
                        <input type="text" id="modal_product_id" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="companydiv">
                        <label>Company </label>
                        <input type="text" id="modal_company" class="form-control" readonly>
                    </div>
                </div> 
                <div class="row" style="margin-top:10px">
                    <div class="col-md-3" id="qualitydiv" style="display:none">
                        <label>Quality </label>
                        <input type="text" id="modal_quality" class="form-control" readonly>
                    </div>
                     <div class="col-md-6">
                        <label>Product Detail</label>
                        <textarea type="text" id="modal_product_details" class="form-control" readonly></textarea>
                    </div>
                </div> 
                <div class="row">
                    <div class="col-md-3" id="materialediv" style="display:none">
                        <label>Material </label>
                        <input type="text" id="modal_product_material" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="colordiv" style="display:none">
                        <label>Color</label>
                        <input type="text" id="modal_product_color" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="designdiv" style="display:none">
                        <label>Design </label>
                        <input type="text" id="modal_product_design" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="coatingdiv" style="display:none">
                        <label>Coating </label>
                        <input type="text" id="modal_product_coating" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="indexdiv" style="display:none">
                        <label>Index  </label>
                        <input type="text" id="modal_product_index" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="numberdiv" style="display:none">
                        <label>Number   </label>
                        <input type="text" id="modal_product_number" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="ctdiv" style="display:none">
                        <label>CT (Center Thickness)  </label>
                        <input type="text" id="modal_product_ct" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="typediv" style="display:none">
                        <label>Type   </label>
                        <input type="text" id="modal_product_typesss" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="validitydiv" style="display:none">
                        <label>Validity In Days   </label>
                        <input type="text" id="modal_product_validity" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="shapediv" style="display:none">
                        <label>Shape   </label>
                        <input type="text" id="modal_product_shape" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="sizediv" style="display:none">
                        <label>Size   </label>
                        <input type="text" id="modal_product_size" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="variantdiv" style="display:none">
                        <label>Variant   </label>
                        <input type="text" id="modal_product_variant" class="form-control" readonly>
                    </div>
                     <div class="col-md-3" id="packingtypediv" style="display:none">
                        <label>Packing Type   </label>
                        <input type="text" id="modal_packing_type" class="form-control" readonly>
                    </div>
                </div>
                <!----- GLASS------------------------------->
                <div class="row" id="Prescriptionglassdiv">
                    <!-- Single Vision -->
                    <div class="col-md-12">
                        <h5>Eyewear Prescription</h5>
                        <div class="table-responsive">
                            <table align="left" id="gl-eyewear-powers" style="float: none; font-size: 13px;">
                        	<tbody><tr>
                        		<td>
                        			<table align="left" id="gl-eyewear-right">
                        				<tbody><tr>
                        					<td></td>
                        					<td colspan="6" align="center" style="font-size: 15px; font-weight: bold; text-decoration: underline;">RIGHT EYE (OD)
                        					<i class="fa fa-clone fa-solid copy-right-to-left" 
                                               style="margin-left: 15px;font-weight: bolder;font-size: large; cursor: pointer; color:#ff7200;" 
                                               title="Copy To Left"></i>
                        					</td>
                        				</tr>
                        				<tr>
                        					<td></td>
                        					<td style="text-align:center">R-SPH</td>
                        					<td style="text-align:center">R-CYL</td>
                        					<td style="text-align:center">R-AXIS</td>
                        					<td style="text-align:center"><span class="mandatory">*</span>R-PD</td>
                        					<td style="text-align:center">R-VA</td>
                        					<td style="text-align: center; display: none;" class="hide-prism">R-PRISM</td>
                        				</tr>
                        				<tr>
                        					<td>Distance Vision (DV)</td>
                        					<td>
                        						<input type="text" name="GL_EYE_RS_D" id="GL_EYE_RS_D" class="search_input_function" style="width:45px;"  autocomplete="off">
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_RC_D" id="GL_EYE_RC_D" class="search_input_function" style="width:45px;">
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_RA_D" id="GL_EYE_RA_D" class="search_input_function" style="width:45px;">
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_RP_D" id="GL_EYE_RP_D" class="search_input_function"    style="width:45px;">
                        					</td>
                        					<td style="width:45px;"><input type="text" name="GL_EYE_RV_D" id="GL_EYE_RV_D" class="search_input_function"   style="width:45px;"></td>
                        					<td style="width: 45px; display: none;" class="hide-prism"><input type="text" name="GL_EYE_RPRISM_D" id="GL_EYE_RPRISM_D" class="search_input_function"  style="width:45px;"></td>
                        				</tr>
                        				<tr id="nearvisionright">
                        					<td>Near Vision (NV)</td>
                        					<td>
                        						<input type="text" name="GL_EYE_RS_N" id="GL_EYE_RS_N" class="search_input_function" style="width:45px;">
                        					</td>
                        					<td><input type="text" name="GL_EYE_RC_N" class="search_input_function" id="GL_EYE_RC_N" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_RA_N" class="search_input_function" id="GL_EYE_RA_N" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_RP_N" class="search_input_function" id="GL_EYE_RP_N" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_RV_N" class="search_input_function" id="GL_EYE_RV_N"  style="width:45px;"></td>
                        					<td class="hide-prism" style="display: none;"><input type="text" class="search_input_function" name="GL_EYE_RPRISM_N" id="GL_EYE_RPRISM_N" style="width:45px;" ></td>
                        				</tr>
                        				<tr id="addright">
                        					<td>Addition (ADD)</td>
                        					<td>
                        						<input type="text" name="GL_EYE_RADD" id="GL_EYE_RADD" class="search_input_function" style="width:45px;">
                        					</td>
                        				</tr>
                        				<tr class="hide-total-pd" id="pdright">
                        					<td>IPD (Total PD)</td>
                        					<td>
                        						<input type="text" name="GL_EYE_totalPD" class="search_input_function" id="GL_EYE_totalPD"  style="width:45px;">
                        					</td>
                        				</tr>
                        			</tbody></table>
                        		</td>
                        		<td style="vertical-align: top;">
                        			<table align="left" id="gl-eyewear-left">
                        				<tbody><tr>
                        					<td colspan="6" align="center" style="font-size: 15px; font-weight: bold; text-decoration: underline;">
                        					    <i class="fa fa-clone copy-left-to-right"
                                                   style="margin-right: 15px;font-weight: bolder;font-size: large; cursor: pointer; color:#ff7200;"
                                                   title="Copy To Right"></i>LEFT EYE (OS)
                        					   </td>
                        				</tr>
                        				<tr>
                        					<td style="text-align:center">L-SPH</td>
                        					<td style="text-align:center">L-CYL</td>
                        					<td style="text-align:center">L-AXIS</td>
                        					<td style="text-align:center"><span class="mandatory">*</span>L-PD</td>
                        					<td style="text-align:center">L-VA</td>
                        					<td style="text-align: center; display: none;" class="hide-prism">L-PRISM</td>
                        				</tr>
                        				<tr>
                        					<td>
                        						<input type="text" name="GL_EYE_LS_D" id="GL_EYE_LS_D" class="search_input_function" style="width:45px;" >
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_LC_D" id="GL_EYE_LC_D" class="search_input_function" style="width:45px;">
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_LA_D" id="GL_EYE_LA_D" class="search_input_function"  style="width:45px;">
                        					</td>
                        					<td style="width:45px;">
                        						<input type="text" name="GL_EYE_LP_D" id="GL_EYE_LP_D" class="search_input_function" style="width:45px;" >
                        					</td>
                        					<td style="width:45px;"><input type="text" name="GL_EYE_LV_D" class="search_input_function"  id="GL_EYE_LV_D" style="width:45px;" ></td>
                        					<td style="width: 45px; display: none;" class="hide-prism"><input type="text" class="search_input_function" name="GL_EYE_LPRISM_D" id="GL_EYE_LPRISM_D" value="" style="width:45px;" ></td>
                        				</tr>
                        				<tr id="nearvisionleft">
                        					<td>
                        						<input type="text" name="GL_EYE_LS_N" id="GL_EYE_LS_N" class="search_input_function" style="width:45px;">
                        					</td>
                        					<td><input type="text" name="GL_EYE_LC_N" id="GL_EYE_LC_N" class="search_input_function" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_LA_N" id="GL_EYE_LA_N" class="search_input_function" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_LP_N" id="GL_EYE_LP_N" class="search_input_function" style="width:45px;"></td>
                        					<td><input type="text" name="GL_EYE_LV_N" id="GL_EYE_LV_N" class="search_input_function" style="width:45px;"></td>
                        					<td class="hide-prism" style="display: none;"><input type="text" name="GL_EYE_LPRISM_N" class="search_input_function" id="GL_EYE_LPRISM_N" style="width:45px;"></td>
                        				</tr>
                        				<tr id="addleft">
                        					<td>
                        						<input type="text" name="GL_EYE_LADD" id="GL_EYE_LADD" class="search_input_function" value="" style="width:45px;">
                        					</td>
                        				</tr>
                        			</tbody></table>
                        		</td>
                        	</tr>
                        </tbody></table>
                            
                        </div>
                    </div>
        
                    <!-- Bifocal/Progressive -->
                    <div class="col-md-12" id="wparameter">
                        <div>
                            <h5>Wearing Parameters</h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="frametypeglass" id="inlineRadio88" value="Full frame" checked>
                                      <label class="form-check-label" for="inlineRadio88">Full frame</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="frametypeglass" id="inlineRadio99" value="Half frame">
                                      <label class="form-check-label" for="inlineRadio99">Half frame</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="frametypeglass" id="inlineRadio100" value="Rimless  frame">
                                      <label class="form-check-label" for="inlineRadio100">Rimless frame</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                 <div class="col-md-3">
                                    <label>Fitting Height </label>
                                    <input type="text" id="modal_FH" placeholder="Enter  Height " class="form-control">
                                </div>
                                <div class="col-md-3" id="framesizea" style="display:none">
                                    <label>A Size </label>
                                    <input type="text" id="modal_asize" placeholder="Enter  A Size " class="form-control">
                                </div>
                                 <div class="col-md-3" id="framesizeb" style="display:none">
                                    <label>B Size </label>
                                    <input type="text" id="modal_bsize" placeholder="Enter  B Size " class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>DBL</label>
                                    <input type="text" id="modal_dbl" placeholder="Enter DBL" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>ED</label>
                                    <input type="text" id="modal_ED" placeholder="Enter ED" class="form-control">
                                </div>
                            </div>   
                        </div>
                        <br>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="modal_rightleft" value="Right" checked>
                                <label class="form-check-label">Right</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="modal_rightleft"  value="Left" checked>
                                <label class="form-check-label">Left</label>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6" style="margin-top:20px">
                        <div>
                            <div class="d-grid gap-2 mb-3">
                                <button class="btn btn-primary" type="button" id="PrescriptionBtn"> Select Prescription</button>
                                <button class="btn btn-primary" type="button" id="clearPrescriptionBtn"> Clear Prescription</button>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-3">
                        <div>
                            <label>Patient  Name</label>
                            <input type="text" id="modal_patient_name" placeholder="Enter Patient Name" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                         <div>
                            <label>Doctor / Optometrist Name</label>
                            <input type="text" id="modal_doctor_name" placeholder="Enter Doctor Name" class="form-control">
                        </div>
                    </div>    
                     
                    <div class="row" id="SelectBox" style="margin-top:10px">
                         <!-- Right -->
                        <div class="col-md-6 lens-box">
                            <div class="lens-row">
                                <span>Right :</span>
                                <button type="button" class="button checkLensFromBarcode" id="checkRightLensFromBarcode">Select Inventory</button>
                    
                                <span>No of Boxes :</span>
                                <input type="text" class="small-input" id="lensRightNoOfBoxes">
                    
                                <span>Total Pieces :</span>
                                <input type="text" class="small-input" id="lensRightTotalPieces">
                            </div>

                        </div>
                    
                        <!-- Left -->
                        <div class="col-md-6 lens-box">
                            <div class="lens-row">
                                <span>Left :</span>
                                <button type="button" class="button checkLensFromBarcode" id="checkleftLensFromBarcode">Select Inventory</button>
                    
                                <span>No of Boxes :</span>
                                <input type="text" class="small-input" id="lensLeftNoOfBoxes">
                    
                                <span>Total Pieces :</span>
                                <input type="text" class="small-input" id="lensLeftTotalPieces">
                            </div>
       
                        </div>
                        <input type="hidden" id="lensSide">
                        <input type="hidden" id="modal_lens_bids">
                    </div>

                        
                    <div class="col-md-12">
                        
                        <div>
                            <div class="mb-1">Lens Type:</div>
                            @foreach(['Constant Use', 'Reading Wear', 'Distance Wear', 'Single Vision', 'Progressive', 'Bifocal', 'Trifocal'] as $type)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="modal_glassWearingType" name="glassWearingType[]" value="{{ $type }}">
                                    <label class="form-check-label">{{ $type }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-4">
                            <div class="mb-1">Prescription Notes:</div>
                            <input class="form-control" type="text" id="modal_prescription_notes">
                    </div>
 
                    <div class="col-md-4" id="counteye">
                            <div class="mb-1">Count In Eye Testing Records ?:</div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="count_eye_test" id="inlineRadio1111" value="1" checked>
                              <label class="form-check-label" for="inlineRadio1111">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" name="count_eye_test" id="inlineRadio9999" value="0">
                              <label class="form-check-label" for="inlineRadio9999">No</label>
                            </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3" id="qtydiv" style="display:none">
                        <label>Quantity </label>
                        <input type="text" id="modal_quantity" value="1" class="form-control" readonly>
                    </div>
                    <div class="col-md-3" id="hsncodediv" style="display:none">
                        <label>HSN/SAC Code </label>
                        <input type="text" id="modal_hsncode"  class="form-control">
                    </div>
                    <div class="col-md-3" id="gstdiv" style="display:none">
                        <label>GST % </label>
                        <input type="number" id="modal_gst"  class="form-control">
                    </div>
                    <div class="col-md-3" id="taxdiv" style="display:none">
                        <label>Tax Rule</label>
                        <input type="text" id="modal_tax_rule"  class="form-control" readonly>
                    </div>
                </div>
                <div class="row mt-3">
                
                    <div class="col-md-3" id="purchasediv" style="display:none">
                        <label>Purchase Price </label>
                        <input type="text" id="modal_purchase_price"  class="form-control">
                    </div>
                     <div class="col-md-3">
                        <label>Retail Price </label>
                        <input type="text" id="modal_retail_price"  class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Discount  </label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="modal_discount" value="0" style="border-color:rgba(67, 87, 133, .2);border-radius:8px 0 0 8px;" readonly>
                            <input type="text" class="form-control" id="modal_discount_amount" value="0.00" style="border-color:rgba(67, 87, 133, .2);border-radius:0 8px 8px 0;" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Base Price </label>
                        <input type="text" id="modal_base_price"  class="form-control" readonly>
                    </div>
                </div>    
                <div class="row mt-3">    
                    <div class="col-md-3" id="gstamtdiv" style="display:none">
                        <label>GST Amount </label>
                        <input type="text" id="modal_gst_amount"  class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                        <label>Total Sales Price </label>
                        <input type="text" id="modal_total_sale"  class="form-control" readonly>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary addmodalbtn">Add</button>
                    </div>
                </div>
            </div>    
        </div>
    </div>
</div> 


<div class="modal fade" id="LensBarcodeModal" data-backdrop="static" tabindex="-1" role="dialog" >
    <div class="modal-dialog lg" role="document">
        <div class="modal-content" >
            <div class="modal-header" style="background: cornsilk;">
                <h5 class="modal-title" id="modalTitle">Contact Lens Inventory List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="LensBarcodeTable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Barcode</th>
                      <th>Product Code</th>
                      <th>Description</th>
                      <th>Available Pieces</th>
                      <th>Purchase Price</th>
                      <th>Sales Price</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td colspan="7" class="text-center">Loading...</td></tr>
                  </tbody>
                </table>
            </div>
            <div class="modal-footer" style="background: cornsilk;">
            <button type="button" class="btn btn-secondary" id="selectLensBarcode">Select</button>    
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </div>
    </div>
</div> 


<div class="modal fade" id="PrescriptionModal" data-backdrop="static" tabindex="-1" role="dialog" >
    <div class="modal-dialog full-scrren" role="document">
        <div class="modal-content" >
            <div class="modal-header" style="background: cornsilk;">
                <h5 class="modal-title" id="modalTitle">Prescription Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="prescriptionTable">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Patient Name</th>
                      <th>Prescription</th>
                      <th>optometrist</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                  </tbody>
                </table>
            </div>
            <div class="modal-footer" style="background: cornsilk;">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </div>
    </div>
</div>  




<!--Credit Apply Modal -->
<div class="modal fade" id="creditModal" data-backdrop="static" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Customer Account Credit </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="couponForm">
          <div class="row">
            <label>Wallet Balnce </label>
            <input type="text" class="form-control" id="wallet_bal" readonly >
          </div><br>    
          <div class="row">
            <label>Amount </label>
            <input type="text" class="form-control" id="credit_amount" >
          </div><br>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="confirmCredit">Apply</button>
      </div>

    </div>
  </div>
</div>




