@extends('layouts.master')
@section('styles')
<style>
/* Spinner when input has `loading` class */
input.loading {
    background-image: url('https://i.imgur.com/6RMhx.gif'); /* or any spinner gif you like */
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px 20px;
}
</style>  
@endsection
@section('content')
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Product and Inventory Settings</h3>
                        
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row">
               <div class="col-lg-12" >
                   <form id="productForm" method="POST" method="POST" enctype="multipart/form-data">
                       @csrf
                       
                        
                        <div class="table-responsive">
							<table class="table card-table table-vcenter text-nowrap" >
								<thead  class="bg-success text-white">
									<tr>
										<th width="20%">Product Type</th>
										<th width="80%">Parameters </th>
									</tr>
								</thead>
								<tbody>
                                    <tr>
                                    @php
                                    $Frame = DB::table("tbl_product_code_setting")->where("product_type", "Frame")->first();
                                    @endphp
                                    <td><input class="form-control" value="Frame" name="product_type[]" readonly></td>
                                    <td>
                                        <div class="row">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->product_code == '0') checked @endif name="fproduct_code" id="fproduct_code" value="0">
                                                <label class="form-check-label" for="fproduct_code">Product Code</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->product_name == '0') checked @endif name="fproduct_name" id="fproduct_name" value="0">
                                                <label class="form-check-label" for="fproduct_name">Name</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->company_name == '0') checked @endif name="fcompany_name" id="fcompany_name" value="0">
                                                <label class="form-check-label" for="fcompany_name">Company</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->quality == '0') checked @endif name="fquality" id="fquality" value="0">
                                                <label class="form-check-label" for="fquality">Quality</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->color == '0') checked @endif name="fcolor" id="fcolor" value="0">
                                                <label class="form-check-label" for="fcolor">Color</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->size == '0') checked @endif name="fsize" id="fsize" value="0">
                                                <label class="form-check-label" for="fsize">Size</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->type == '0') checked @endif name="ftype" id="ftype" value="0">
                                                <label class="form-check-label" for="ftype">Type</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->gender == '0') checked @endif name="fgender" id="fgender" value="0">
                                                <label class="form-check-label" for="fgender">Gender</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->Shape == '0') checked @endif name="fShape" id="fShape" value="0">
                                                <label class="form-check-label" for="fShape">Shape</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->Material == '0') checked @endif name="fMaterial" id="fMaterial" value="0">
                                                <label class="form-check-label" for="fMaterial">Material</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->Temple_Detail == '0') checked @endif name="fTemple_Detail" id="fTemple_Detail" value="0">
                                                <label class="form-check-label" for="fTemple_Detail">Temple Detail</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->Bridge_Size == '0') checked @endif name="fBridge_Size" id="fBridge_Size" value="0">
                                                <label class="form-check-label" for="fBridge_Size">Bridge Size</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Frame->Description == '0') checked @endif name="fDescription" id="fDescription" value="0">
                                                <label class="form-check-label" for="fDescription">Description</label>
                                            </div>
                                        </div>
                                    </td>
                                    </tr>
                                    <tr>
                                    @php
                                    $Goggles = DB::table("tbl_product_code_setting")->where("product_type", "Goggles")->first();
                                    @endphp
                                    <td><input class="form-control" value="Goggles" name="product_type[]" readonly></td>
                                    <td>
                                        <div class="row">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->product_code == '0') checked @endif name="gproduct_code" id="gproduct_code" value="0">
                                                <label class="form-check-label" for="gproduct_code">Product Code</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->product_name == '0') checked @endif name="gproduct_name" id="gproduct_name" value="0">
                                                <label class="form-check-label" for="gproduct_name">Name</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->company_name == '0') checked @endif name="gcompany_name" id="gcompany_name" value="0">
                                                <label class="form-check-label" for="gcompany_name">Company</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->quality == '0') checked @endif name="gquality" id="gquality" value="0">
                                                <label class="form-check-label" for="gquality">Quality</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->color == '0') checked @endif name="gcolor" id="gcolor" value="0">
                                                <label class="form-check-label" for="gcolor">Color</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->size == '0') checked @endif name="gsize" id="gsize" value="0">
                                                <label class="form-check-label" for="gsize">Size</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->type == '0') checked @endif name="gtype" id="gtype" value="0">
                                                <label class="form-check-label" for="gtype">Type</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->gender == '0') checked @endif name="ggender" id="ggender" value="0">
                                                <label class="form-check-label" for="ggender">Gender</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->Shape == '0') checked @endif name="gShape" id="gShape" value="0">
                                                <label class="form-check-label" for="gShape">Shape</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->Material == '0') checked @endif name="gMaterial" id="gMaterial" value="0">
                                                <label class="form-check-label" for="gMaterial">Material</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->Temple_Detail == '0') checked @endif name="gTemple_Detail" id="gTemple_Detail" value="0">
                                                <label class="form-check-label" for="gTemple_Detail">Temple Detail</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->Bridge_Size == '0') checked @endif name="gBridge_Size" id="gBridge_Size" value="0">
                                                <label class="form-check-label" for="gBridge_Size">Bridge Size</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Goggles->Description == '0') checked @endif name="gDescription" id="gDescription" value="0">
                                                <label class="form-check-label" for="gDescription">Description</label>
                                            </div>
                                        </div>
                                    </td>
                                    </tr>
                                    <tr>
                                    @php
                                    $Glass = DB::table("tbl_product_code_setting")->where("product_type", "Glass")->first();
                                    @endphp
                                    <td><input class="form-control" value="Glass" name="product_type[]" readonly></td>
                                        <td>
                                            <div class="row">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->product_code == '0') checked @endif name="ggproduct_code" id="ggproduct_code" value="0">
                                                    <label class="form-check-label" for="ggproduct_code">Product Code</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->product_name == '0') checked @endif name="ggproduct_name" id="ggproduct_name" value="0">
                                                    <label class="form-check-label" for="ggproduct_name">Details</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->company_name == '0') checked @endif name="ggcompany_name" id="ggcompany_name" value="0">
                                                    <label class="form-check-label" for="ggcompany_name">Company</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->quality == '0') checked @endif name="ggquality" id="ggquality" value="0">
                                                    <label class="form-check-label" for="ggquality">Quality</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->color == '0') checked @endif name="ggcolor" id="ggcolor" value="0">
                                                    <label class="form-check-label" for="ggcolor">Color</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->Material == '0') checked @endif name="ggMaterial" id="ggMaterial" value="0">
                                                    <label class="form-check-label" for="gMaterial">Material</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->Coating == '0') checked @endif name="ggCoating" id="ggCoating" value="0">
                                                    <label class="form-check-label" for="ggCoating">Coating</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->Design == '0') checked @endif name="ggDesign" id="ggDesign" value="0">
                                                    <label class="form-check-label" for="ggDesign">Design</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->Product_Index == '0') checked @endif name="ggProduct_Index" id="ggProduct_Index" value="0">
                                                    <label class="form-check-label" for="ggProduct_Index">Index </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->Description == '0') checked @endif name="ggDescription" id="ggDescription" value="0">
                                                    <label class="form-check-label" for="ggDescription">Description</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->Numbers == '0') checked @endif name="ggNumbers" id="ggNumbers" value="0">
                                                    <label class="form-check-label" for="ggNumbers">Numbers</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Glass->Product_Range == '0') checked @endif name="ggRange" id="ggRange" value="0">
                                                    <label class="form-check-label" for="ggRange">Range</label>
                                                </div>
                                                
                                                
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    @php
                                    $Lens = DB::table("tbl_product_code_setting")->where("product_type", "Lens")->first();
                                    @endphp
                                    <td><input class="form-control" value="Lens" name="product_type[]" readonly></td>
                                        <td>
                                            <div class="row">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->product_code == '0') checked @endif name="lproduct_code" id="lproduct_code" value="0">
                                                    <label class="form-check-label" for="lproduct_code">Product Code</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->product_name == '0') checked @endif name="lproduct_name" id="lproduct_name" value="0">
                                                    <label class="form-check-label" for="lproduct_name">Product Name</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->company_name == '0') checked @endif name="lcompany_name" id="lcompany_name" value="0">
                                                    <label class="form-check-label" for="lcompany_name">Company</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->quality == '0') checked @endif name="lquality" id="lquality" value="0">
                                                    <label class="form-check-label" for="lquality">Quality</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->color == '0') checked @endif name="lcolor" id="lcolor" value="0">
                                                    <label class="form-check-label" for="lcolor">Color</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->Numbers == '0') checked @endif name="lNumbers" id="lNumbers" value="0">
                                                    <label class="form-check-label" for="lNumbers">Numbers</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->CT == '0') checked @endif name="lCT" id="lCT" value="0">
                                                    <label class="form-check-label" for="lCT">CT (Center Thickness)</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->type == '0') checked @endif name="ltype" id="ltype" value="0">
                                                    <label class="form-check-label" for="ltype">Type </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->Material == '0') checked @endif name="lMaterial" id="lMaterial" value="0">
                                                    <label class="form-check-label" for="lMaterial">Materials</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->Modality == '0') checked @endif name="lModality" id="lModality" value="0">
                                                    <label class="form-check-label" for="lModality">Modality </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->Validity_In_Days == '0') checked @endif name="lValidity_In_Days" id="lValidity_In_Days" value="0">
                                                    <label class="form-check-label" for="lValidity_In_Days">Validity In Days </label>
                                                </div>
                                                
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->WC == '0') checked @endif name="lWC" id="lWC" value="0">
                                                    <label class="form-check-label" for="lWC">WC (Water Content) </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->Dk_t == '0') checked @endif name="lDk_t" id="lDk_t" value="0">
                                                    <label class="form-check-label" for="lDk_t">Dk/t (Permeability)  </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->Description == '0') checked @endif name="lDescription" id="lDescription" value="0">
                                                    <label class="form-check-label" for="lDescription">Description </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->Prescription_Parameters == '0') checked @endif name="lPrescription_Parameters" id="lPrescription_Parameters" value="0">
                                                    <label class="form-check-label" for="lPrescription_Parameters">Prescription Parameters </label>
                                                </div>
                                            </div>    
                                             <div class="row mt-2"><strong>Inventory Management</strong></div>   
                                             <div class="row">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->SPH == '0') checked @endif name="lSPH" id="lSPH" value="0">
                                                    <label class="form-check-label" for="lSPH">SPH </label>
                                                </div>
                                                 <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->CYL == '0') checked @endif name="lCYL" id="lCYL" value="0">
                                                    <label class="form-check-label" for="lCYL">CYL </label>
                                                </div>
                                                 <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->AXIS == '0') checked @endif name="lAXIS" id="lAXIS" value="0">
                                                    <label class="form-check-label" for="lAXIS">AXIS </label>
                                                </div>
                                                 <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->ADDITIONAL == '0') checked @endif name="lADDITIONAL" id="lADDITIONAL" value="0">
                                                    <label class="form-check-label" for="lADDITIONAL">ADD </label>
                                                </div>
                                                 <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->BC == '0') checked @endif name="lBC" id="lBC" value="0">
                                                    <label class="form-check-label" for="lBC">BC </label>
                                                </div>
                                                 <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->DIA == '0') checked @endif name="lDIA" id="lDIA" value="0">
                                                    <label class="form-check-label" for="lDIA">DIA </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" @if($Lens->POWER_TYPE == '0') checked @endif name="lPOWER_TYPE" id="lPOWER_TYPE" value="0">
                                                    <label class="form-check-label" for="lPOWER_TYPE">POWER TYPE	 </label>
                                                </div>
                                            </div>    
                    
                                        </td>
                                    </tr>
                                    <tr>
                                    @php
                                    $Solution = DB::table("tbl_product_code_setting")->where("product_type", "Solution")->first();
                                    @endphp
                                    <td><input class="form-control" value="Solution" name="product_type[]" readonly></td>
                                    <td>
                                        <div class="row">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Solution->product_code == '0') checked @endif name="sproduct_code" id="sproduct_code" value="0">
                                                <label class="form-check-label" for="sproduct_code">Product Code</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Solution->product_name == '0') checked @endif name="sproduct_name" id="sproduct_name" value="0">
                                                <label class="form-check-label" for="sproduct_name">Name</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Solution->company_name == '0') checked @endif name="scompany_name" id="scompany_name" value="0">
                                                <label class="form-check-label" for="scompany_name">Company</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Solution->quality == '0') checked @endif name="squality" id="squality" value="0">
                                                <label class="form-check-label" for="squality">Quality</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Solution->Variant == '0') checked @endif name="sVariant" id="sVariant" value="0">
                                                <label class="form-check-label" for="sVariant">Variant</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Solution->Packing_Type == '0') checked @endif name="sPacking_Type" id="sPacking_Type" value="0">
                                                <label class="form-check-label" for="sPacking_Type">Packing Type</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Solution->color == '0') checked @endif name="sColor" id="sColor" value="0">
                                                <label class="form-check-label" for="sColor">Color</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Solution->Description == '0') checked @endif name="sDescription" id="sDescription" value="0">
                                                <label class="form-check-label" for="sDescription">Description</label>
                                            </div>
                                        </div>
                                    </td>
                                    </tr>
									<tr>
                                    @php
                                    $Other = DB::table("tbl_product_code_setting")->where("product_type", "Other")->first();
                                    @endphp
                                    <td><input class="form-control" value="Other" name="product_type[]" readonly></td>
                                    <td>
                                        <div class="row">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Other->product_code == '0') checked @endif name="oproduct_code" id="oproduct_code" value="0">
                                                <label class="form-check-label" for="oproduct_code">Product Code</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Other->product_name == '0') checked @endif name="oproduct_name" id="oproduct_name" value="0">
                                                <label class="form-check-label" for="oproduct_name">Name</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Other->company_name == '0') checked @endif name="ocompany_name" id="ocompany_name" value="0">
                                                <label class="form-check-label" for="ocompany_name">Company</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Other->quality == '0') checked @endif name="oquality" id="oquality" value="0">
                                                <label class="form-check-label" for="oquality">Quality</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Other->type == '0') checked @endif name="otype" id="otype" value="0">
                                                <label class="form-check-label" for="otype">Type </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Other->Shape == '0') checked @endif name="oShape" id="oShape" value="0">
                                                <label class="form-check-label" for="oShape">Shape</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Other->color == '0') checked @endif name="oColor" id="oColor" value="0">
                                                <label class="form-check-label" for="oColor">Color</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Other->size == '0') checked @endif name="osize" id="osize" value="0">
                                                <label class="form-check-label" for="osize">Size </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Other->Description == '0') checked @endif name="oDescription" id="oDescription" value="0">
                                                <label class="form-check-label" for="oDescription">Description</label>
                                            </div>
                                        </div>
                                    </td>
                                    </tr>
                                    <tr>
                                    @php
                                    $Chargeable = DB::table("tbl_product_code_setting")->where("product_type", "Non Chargeable")->first();
                                    @endphp
                                    <td><input class="form-control" value="Non Chargeable" name="product_type[]" readonly></td>
                                    <td>
                                        <div class="row">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Chargeable->product_code == '0') checked @endif name="cproduct_code" id="cproduct_code" value="0">
                                                <label class="form-check-label" for="cproduct_code">Product Code</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Chargeable->product_name == '0') checked @endif name="cproduct_name" id="cproduct_name" value="0">
                                                <label class="form-check-label" for="cproduct_name">Name</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Chargeable->company_name == '0') checked @endif name="ccompany_name" id="ccompany_name" value="0">
                                                <label class="form-check-label" for="ccompany_name">Company</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Chargeable->quality == '0') checked @endif name="cquality" id="cquality" value="0">
                                                <label class="form-check-label" for="oquality">Quality</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Chargeable->type == '0') checked @endif name="ctype" id="ctype" value="0">
                                                <label class="form-check-label" for="ctype">Type </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Chargeable->Material == '0') checked @endif name="cMaterial" id="cMaterial" value="0">
                                                <label class="form-check-label" for="cMaterial">Material</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Chargeable->color == '0') checked @endif name="cColor" id="cColor" value="0">
                                                <label class="form-check-label" for="cColor">Color</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Chargeable->size == '0') checked @endif name="csize" id="csize" value="0">
                                                <label class="form-check-label" for="csize">Size </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" @if($Chargeable->Description == '0') checked @endif name="cDescription" id="cDescription" value="0">
                                                <label class="form-check-label" for="cDescription">Description</label>
                                            </div>
                                        </div>
                                    </td>
                                    </tr>
									
								</tbody>
							</table>
						</div>
                        <div class="button-row d-flex mt-4">
                            <button class="btn btn-gradient js-btn-next" type="submit" title="Next">Update
                            </button>
                        </div>
                    </form>    
                   
               </div>

            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    $("#productForm").submit(function(e)
{
    e.preventDefault(); 
    
    let isValid = true;
    let class_name = '';

    document.querySelectorAll(".error").forEach(el => el.textContent = "");
    document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));


    let form = $("#productForm")[0];
    let data = new FormData(form);

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.productsetting-update') }}",
        data: data,
        dataType: "JSON",
        processData: false,
        contentType: false,
        success: function(response) {
        if ($.isEmptyObject(response.error)) {
            $.toaster({
                priority: 'success',
                title: response.success,
                message: ''
            });
            location.reload();
        } else {
            document.querySelectorAll(".error").forEach(el => el.textContent = "");
            document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));

            $.each(response.error, function(index, value) {
                if (value.includes("product_code")) {
                    $("#product_codeError").text(value);
                    $("#product_code").addClass("is-invalid");
                }
                if (value.includes("product_name")) {
                    $("#product_nameError").text(value);
                    $("#product_name").addClass("is-invalid");
                }
            });
        }
    }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("AJAX Error: " + textStatus + " - " + errorThrown);
    });
});
</script>
@endsection
