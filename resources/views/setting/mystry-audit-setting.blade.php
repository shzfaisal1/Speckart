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
                        <h3>Mystry Audit Setting</h3>
                    </div>
                </div>
            </div>
            <hr/>
            <form id="auditSettingForm">

@csrf

<table class="table table-bordered table-striped">
<thead class="table-dark text-center">
<tr>
    <th width="20%">Title</th>
    <th width="35%">Checkpoint</th>
    <th width="15%">Marks</th>
    <th width="30%">Non-Negotiable Question</th>
</tr>
</thead>

<tbody>

@foreach($auditSetting as $row)

{{-- ================= TITLE ROW ================= --}}
<tr style="background:#f1f1f1">
    <td colspan="4">
        <input type="text"
               class="form-control fw-bold"
               name="audit[{{$row->id}}][title]"
               value="{{ $row->title }}"
               placeholder="Enter Title">
    </td>
</tr>

{{-- ================= CHECKPOINT A ================= --}}
<tr>
    <td class="text-center">A</td>

    <td>
        <input type="text"
               class="form-control"
               name="audit[{{$row->id}}][Checkpoint_A]"
               value="{{ $row->Checkpoint_A }}"
               placeholder="Checkpoint A">
    </td>

    <td>
        <input type="number"
               class="form-control"
               name="audit[{{$row->id}}][Checkpoint_A_Mark]"
               value="{{ $row->Checkpoint_A_Mark }}">
    </td>

    <td class="text-center">

        <label class="me-3">
            <input type="radio"
                   name="audit[{{$row->id}}][Checkpoint_A_answer]"
                   value="Yes"
                   {{ $row->Checkpoint_A_answer=='Yes'?'checked':'' }}>
            Yes
        </label>

        <label>
            <input type="radio"
                   name="audit[{{$row->id}}][Checkpoint_A_answer]"
                   value="No"
                   {{ $row->Checkpoint_A_answer=='No'?'checked':'' }}>
            No
        </label>

    </td>
</tr>

{{-- ================= CHECKPOINT B ================= --}}
<tr>
    <td class="text-center">B</td>

    <td>
        <input type="text"
               class="form-control"
               name="audit[{{$row->id}}][Checkpoint_B]"
               value="{{ $row->Checkpoint_B }}">
    </td>

    <td>
        <input type="number"
               class="form-control"
               name="audit[{{$row->id}}][Checkpoint_B_Mark]"
               value="{{ $row->Checkpoint_B_Mark }}">
    </td>

    <td class="text-center">

        <label class="me-3">
            <input type="radio"
                   name="audit[{{$row->id}}][Checkpoint_B_answer]"
                   value="Yes"
                   {{ $row->Checkpoint_B_answer=='Yes'?'checked':'' }}>
            Yes
        </label>

        <label>
            <input type="radio"
                   name="audit[{{$row->id}}][Checkpoint_B_answer]"
                   value="No"
                   {{ $row->Checkpoint_B_answer=='No'?'checked':'' }}>
            No
        </label>

    </td>
</tr>

{{-- ================= CHECKPOINT C ================= --}}
<tr>
    <td class="text-center">C</td>

    <td>
        <input type="text"
               class="form-control"
               name="audit[{{$row->id}}][Checkpoint_C]"
               value="{{ $row->Checkpoint_C }}">
    </td>

    <td>
        <input type="number"
               class="form-control"
               name="audit[{{$row->id}}][Checkpoint_C_Mark]"
               value="{{ $row->Checkpoint_C_Mark }}">
    </td>

    <td class="text-center">

        <label class="me-3">
            <input type="radio"
                   name="audit[{{$row->id}}][Checkpoint_C_answer]"
                   value="Yes"
                   {{ $row->Checkpoint_C_answer=='Yes'?'checked':'' }}>
            Yes
        </label>

        <label>
            <input type="radio"
                   name="audit[{{$row->id}}][Checkpoint_C_answer]"
                   value="No"
                   {{ $row->Checkpoint_C_answer=='No'?'checked':'' }}>
            No
        </label>

    </td>
</tr>

{{-- ================= CHECKPOINT D ================= --}}
<tr>
    <td class="text-center">D</td>

    <td>
        <input type="text"
               class="form-control"
               name="audit[{{$row->id}}][Checkpoint_D]"
               value="{{ $row->Checkpoint_D }}">
    </td>

    <td>
        <input type="number"
               class="form-control"
               name="audit[{{$row->id}}][Checkpoint_D_Mark]"
               value="{{ $row->Checkpoint_D_Mark }}">
    </td>

    <td class="text-center">

        <label class="me-3">
            <input type="radio"
                   name="audit[{{$row->id}}][Checkpoint_D_answer]"
                   value="Yes"
                   {{ $row->Checkpoint_D_answer=='Yes'?'checked':'' }}>
            Yes
        </label>

        <label>
            <input type="radio"
                   name="audit[{{$row->id}}][Checkpoint_D_answer]"
                   value="No"
                   {{ $row->Checkpoint_D_answer=='No'?'checked':'' }}>
            No
        </label>

    </td>
</tr>

{{-- ================= CHECKPOINT E ================= --}}
<tr>
    <td class="text-center">E</td>

    <td>
        <input type="text"
               class="form-control"
               name="audit[{{$row->id}}][Checkpoint_E]"
               value="{{ $row->Checkpoint_E }}">
    </td>

    <td>
        <input type="number"
               class="form-control"
               name="audit[{{$row->id}}][Checkpoint_E_Mark]"
               value="{{ $row->Checkpoint_E_Mark }}">
    </td>

    <td class="text-center">

        <label class="me-3">
            <input type="radio"
                   name="audit[{{$row->id}}][Checkpoint_E_answer]"
                   value="Yes"
                   {{ $row->Checkpoint_E_answer=='Yes'?'checked':'' }}>
            Yes
        </label>

        <label>
            <input type="radio"
                   name="audit[{{$row->id}}][Checkpoint_E_answer]"
                   value="No"
                   {{ $row->Checkpoint_E_answer=='No'?'checked':'' }}>
            No
        </label>

    </td>
</tr>

@endforeach

</tbody>
</table>

<div class="text-end">
    <button type="submit" class="btn btn-primary">
        Update Setting
    </button>
</div>

</form> 
        </div>
    </div>
</section>
@endsection

@section('scripts')

<script>
$("#auditSettingForm").submit(function(e){
    e.preventDefault();

    $.ajax({
        url: "{{ route('admin.mystryaudit-update') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(res){
            alert(res.message);
        }
    });
});
</script>

@endsection

