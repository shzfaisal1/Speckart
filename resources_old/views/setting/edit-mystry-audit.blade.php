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

<h3>Mystry Audit Edit</h3>
<hr/>

<form id="auditForm" method="POST">
@csrf

<input type="hidden" name="audit_id"
value="{{ $mystryaudit->mystry_audit_id }}">

{{-- ================= STORE + AUDITOR ================= --}}
<div class="row">

<div class="col-md-3">
<label>Select Store *</label>
<select class="form-control select" id="store_id" name="store_id">

<option value="">Select Store</option>

@foreach(DB::table('tbl_store')->where('status',1)->get() as $store)
<option value="{{$store->id}}"
{{ $store->id==$mystryaudit->store_id?'selected':'' }}>
{{$store->store_name}} / ({{$store->store_id}})
</option>
@endforeach

</select>
<span class="text-danger" id="store_idError"></span>
</div>


<div class="col-md-3">
<label>Auditor *</label>

<select class="form-control select" id="Auditor_id" name="Auditor_id">
<option value="">Select Auditor</option>

@foreach(DB::table('users')->where('status',1)->get() as $user)
<option value="{{$user->id}}"
{{ $user->id==$mystryaudit->auditor_id?'selected':'' }}>
{{$user->name}} ({{$user->staff_id}})
</option>
@endforeach

</select>

<span class="text-danger" id="Auditor_idError"></span>
</div>

</div>

<hr>


{{-- ================= CHECKPOINTS ================= --}}
@foreach($mystry_adit as $item)

<div class="border p-3 mt-3">
<h4>{{ $item->title }}</h4>

@php
$checkpoints = ['A','B','C','D','E'];
@endphp

@foreach($checkpoints as $cp)

@php
$name = "Checkpoint_".$cp;
$mark = "Checkpoint_".$cp."_Mark";

/* get existing mark */
$existingMark = $auditDetails[$item->id][$name] ?? '';
@endphp

@if(!empty($item->$name))

<div class="row mb-2">

<div class="col-md-8">
<label>
{{ $item->$name }}
<small>(Max: {{ $item->$mark }})</small>
</label>
</div>

<div class="col-md-4">
<input type="number"
class="form-control audit-mark"
name="audit[{{$item->id}}][{{$name}}]"
value="{{$existingMark}}"
data-max="{{$item->$mark}}"
min="0"
max="{{$item->$mark}}">
</div>

</div>

@endif
@endforeach

</div>

@endforeach

<span class="text-danger" id="checkpointError"></span>


{{-- ================= FINAL SCORE ================= --}}
<div class="row mt-3">
<div class="col-md-3">
<label>FINAL SCORE</label>
<input type="text"
id="final_score"
name="final_score"
class="form-control"
value="{{ $mystryaudit->final_score }}"
readonly>
</div>
</div>


{{-- ================= RESULT ================= --}}
<div class="row mt-3">
<div class="col-md-6">

<label>Audit Result</label><br>

@php
$results=[
"Benchmark (100+)",
"Acceptable (85–99)",
"Warning (70–84)",
"Fail (70)"
];
@endphp

@foreach($results as $res)
<label class="me-3">
<input type="radio"
name="audit_result"
value="{{$res}}"
{{ $mystryaudit->audit_result==$res?'checked':'' }}>
{{$res}}
</label>
@endforeach

</div>
</div>

<hr>

<button class="btn btn-primary">Update Audit</button>

</form>
</div>
</div>
</section>
@endsection



@section('scripts')
<script>

$(document).ready(function(){

$('.select').select2({width:'100%'});


/* ================= SCORE CALCULATION ================= */

function calculateResult(score){

let result="";

if(score>=100) result="Benchmark (100+)";
else if(score>=85) result="Acceptable (85–99)";
else if(score>=70) result="Warning (70–84)";
else result="Fail (70)";

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


/* LIVE CALCULATION */
$(document).on('input','.audit-mark',calculateScore);

/* AUTO LOAD SCORE ON EDIT */
calculateScore();


/* ================= SUBMIT ================= */

$("#auditForm").submit(function(e){

e.preventDefault();

let valid=true;

$("#store_idError,#Auditor_idError,#checkpointError").text('');

if(!$("#store_id").val()){
$("#store_idError").text("Store required");
valid=false;
}

if(!$("#Auditor_id").val()){
$("#Auditor_idError").text("Auditor required");
valid=false;
}

if(!calculateScore()){
$("#checkpointError")
.text("Marks cannot exceed checkpoint limit.");
valid=false;
}

if(!valid) return;

let formData=new FormData(this);

$.ajax({
type:"POST",
url:"{{ route('admin.mystry-audit-update') }}",
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
"{{ route('admin.mystry-audit-history') }}";
}
}
});

});

});
</script>
@endsection
