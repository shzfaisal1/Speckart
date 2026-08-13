@extends('layouts.master')

@section('styles')
<style>

.is-invalid{
    border:1px solid red !important;
}

</style>
@endsection


@section('content')
<section class="domestic-orders mt-0">
<div class="container-fluid">
<div class="card">

<div class="domestic-orders-header">
    <h3>Mystry Audit Entry</h3>
</div>
<hr/>

<form id="auditForm" method="POST" enctype="multipart/form-data">
@csrf

{{-- ================= BASIC DETAILS ================= --}}
<div class="row">

{{-- FIXED AUDIT DATE --}}
<div class="col-md-3">
<label>Audit Date *</label>
    <input type="text" class="form-control" id="audit_date_display"  readonly>
    <input type="hidden" id="mystry_audit_date"  name="mystry_audit_date">
</div> 

<div class="col-md-3">
<label>Select Store *</label>
<select class="form-control select" id="store_id" name="store_id">
<option value="">Select Store</option>
@foreach(DB::table('tbl_store')->where('status',1)->get() as $store)
<option value="{{$store->id}}">
  {{$store->store_name}} / ({{$store->store_id}})
</option>
@endforeach
</select>
   <span class="error text-danger" id="store_idError"></span>
</div>

<div class="col-md-3">
<label>Auditor *</label>
<select class="form-control select" id="Auditor_id" name="Auditor_id">
<option value="">Select Auditor</option>
@foreach(DB::table('users')->where('status',1)->get() as $user)
<option value="{{$user->id}}">
{{$user->name}} ({{$user->staff_id}})
</option>
@endforeach
</select>
<span class="error text-danger" id="Auditor_idError"></span>
</div>

</div>

<hr/>

{{-- ================= CHECKPOINTS ================= --}}
@foreach($mystry_adit as $item)

<div class="row mb-4 border p-3">
  <div class="col-md-12">
   <h4>{{ $item->title }}</h4>

@foreach(['A','B','C','D','E'] as $cp)
@php
$name = "Checkpoint_".$cp;
$mark = "Checkpoint_".$cp."_Mark";
@endphp

@if(!empty($item->$name))

<div class="row mb-3">
    <div class="col-md-6">
        <label> {{ $item->$name }} <small class="text-muted">(Max: {{ $item->$mark }})</small> </label>
    </div>

<div class="col-md-3">

{{-- MARK INPUT --}}
<input type="number"
class="form-control audit-mark mb-2"
name="audit[{{$item->id}}][{{$name}}]"
data-max="{{$item->$mark}}"
min="0"
max="{{$item->$mark}}"
placeholder="Enter Marks"
required>
</div>

<div class="col-md-3">

{{-- PHOTO INPUT --}}
<input type="file"
class="form-control audit-photo"
name="audit_photo[{{$item->id}}][{{$name}}]"
accept="image/*" 
capture="environment" onchange="previewImage(this)">


</div>

</div>

@endif
@endforeach

</div>
</div>

@endforeach

<span class="error text-danger" id="checkpointError"></span>


{{-- FINAL SCORE --}}
<div class="row" style="display:none">
<div class="col-md-3">
<label>FINAL SCORE</label>
<input type="text" class="form-control" id="final_score"
name="final_score" readonly>
</div>
</div>


{{-- RESULT --}}
<div class="row" style="display:none">
<div class="col-md-6">

<label>Audit Result *</label><br>

<label><input type="radio" name="audit_result"
value="Benchmark (100+)"> Benchmark (100+)</label>

<label><input type="radio" name="audit_result"
value="Acceptable (85–99)"> Acceptable (85–99)</label>

<label><input type="radio" name="audit_result"
value="Warning (70–84)"> Warning (70–84)</label>

<label><input type="radio" name="audit_result"
value="Fail (<70)"> Fail (&lt;70)</label>

<span class="error text-danger" id="audit_resultError"></span>

</div>
</div>

<hr/>

<button type="submit" class="btn btn-primary">Submit</button>

</form>
</div>
</div>
</section>
@endsection



@section('scripts')
<script>

$(document).ready(function(){

/* ================= SELECT2 ================= */
$('.select').select2({width:'100%'});


/* ================= FIX CURRENT DATETIME ================= */

let now = moment();

$('#audit_date_display')
.val(now.format('MMMM D, YYYY h:mm A'));

$('#mystry_audit_date')
.val(now.format('YYYY-MM-DD HH:mm:ss'));


/* ================= RESULT CALCULATION ================= */

function calculateResult(score){

let result="";

if(score>=100) result="Benchmark (100+)";
else if(score>=85) result="Acceptable (85–99)";
else if(score>=70) result="Warning (70–84)";
else result="Fail (<70)";

$('input[name="audit_result"][value="'+result+'"]')
.prop('checked',true);
}


function calculateScore(){

let total=0;
let valid=true;

$('.audit-mark').each(function(){

let val=parseFloat($(this).val())||0;
let max=parseFloat($(this).data('max'));

if(val>max){
$(this).addClass('is-invalid');
valid=false;
}else{
$(this).removeClass('is-invalid');
}

total+=val;
});

$('#final_score').val(total);
calculateResult(total);

return valid;
}


/* LIVE SCORE */
$(document).on('input','.audit-mark',function(){
calculateScore();
});


/* ================= FORM SUBMIT ================= */

$("#auditForm").submit(function(e){

e.preventDefault();

$(".error").text('');
$(".is-invalid").removeClass("is-invalid");

let valid=true;


/* STORE VALIDATION */
if($("#store_id").val()==""){
$("#store_idError").text("Store required");
valid=false;
}


/* AUDITOR VALIDATION */
if($("#Auditor_id").val()==""){
$("#Auditor_idError").text("Auditor required");
valid=false;
}


/* MARK VALIDATION */
if(!calculateScore()){
$("#checkpointError")
.text("Marks cannot exceed checkpoint limit.");
valid=false;
}




if(!valid){
$("#checkpointError")
.text("All checkpoints require marks.");
return;
}


/* AJAX SUBMIT */
let formData=new FormData(this);

$.ajax({
type:"POST",
url:"{{ route('admin.mystry-audit-add') }}",
data:formData,
processData:false,
contentType:false,
dataType:"JSON",

success:function(res){

if(res.status){

$.toaster({
priority:'success',
title:res.message
});

window.location.href=
"{{ route('admin.mystry-audit-entry') }}";
}
}
});

});

});

navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
function previewImage(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            console.log(e.target.result); // preview or debug
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection