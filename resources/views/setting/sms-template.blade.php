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
                        <h3>SMS Template Settings</h3>
                        
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row">
               <div class="col-lg-12" >
                   <form id="smsForm" method="POST" method="POST" enctype="multipart/form-data">
                       @csrf
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap">
                            <thead class="bg-success text-white">
                            <tr>
                                <th>Title</th>
                                <th>SMS Status</th>
                                <th>Template</th>
                                <th>Template ID</th>
                                <th>Entity ID</th>
                                <th>Sender ID</th>
                                <th>URL</th>
                                <th>Message Type</th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @php $sms_template = DB::table('tbl_sms_template')->get(); @endphp
                            
                            @foreach($sms_template as $i => $sms)
                            <tr class="sms-row" data-index="{{$i}}">
                                <td>
                                    {{$sms->title}}
                                    <input type="hidden" name="sms_id[{{$i}}]" value="{{$sms->id}}">
                                </td>
                            
                                {{-- SEND STATUS --}}
                                <td>
                                    <div class="form-check">
                                        <input type="radio"
                                               class="form-check-input send-status"
                                               name="send_status[{{$i}}]"
                                               value="0"
                                               {{ $sms->send_status == '0' ? 'checked' : '' }}>
                                        Enable
                                    </div>
                            
                                    <div class="form-check">
                                        <input type="radio"
                                               class="form-check-input send-status"
                                               name="send_status[{{$i}}]"
                                               value="1"
                                               {{ $sms->send_status == '1' ? 'checked' : '' }}>
                                        Disable
                                    </div>
                                </td>
                            
                                {{-- CONTROLLED FIELDS --}}
                                <td>
                                    <textarea name="Template[{{$i}}]"
                                              class="form-control controlled">{{$sms->Template}}</textarea>
                                </td>
                            
                                <td>
                                    <input type="text"
                                           name="Template_id[{{$i}}]"
                                           class="form-control controlled"
                                           value="{{$sms->Template_id}}">
                                </td>
                            
                                <td>
                                    <input type="text"
                                           name="entity_id[{{$i}}]"
                                           class="form-control controlled"
                                           value="{{$sms->entity_id}}">
                                </td>
                            
                                <td>
                                    <input type="text"
                                           name="sender_id[{{$i}}]"
                                           class="form-control controlled"
                                           value="{{$sms->sender_id}}">
                                </td>
                            
                                <td>
                                    <textarea name="sms_url[{{$i}}]"
                                              class="form-control controlled">{{$sms->sms_url}}</textarea>
                                </td>
                            
                                {{-- MESSAGE TYPE --}}
                                <td>
                                    <div class="form-check">
                                        <input type="radio"
                                               class="form-check-input controlled-radio"
                                               name="message_type[{{$i}}]"
                                               value="0"
                                               {{ $sms->message_type == '0' ? 'checked' : '' }}>
                                        Text
                                    </div>
                            
                                    <div class="form-check">
                                        <input type="radio"
                                               class="form-check-input controlled-radio"
                                               name="message_type[{{$i}}]"
                                               value="1"
                                               {{ $sms->message_type == '1' ? 'checked' : '' }}>
                                        Unicode
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                            </table>
                            </div>
                        <div class="button-row d-flex mt-4">
                            <button class="btn btn-success" type="submit" title="Next">Update
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
document.addEventListener('DOMContentLoaded', function () {

    function toggleReadonly(row) {
        const isDisabled = row.querySelector('.send-status[value="1"]').checked;

        // Inputs & textareas → readonly
        row.querySelectorAll('.controlled').forEach(el => {
            el.readOnly = isDisabled;
        });

        // Radio buttons → disabled
        row.querySelectorAll('.controlled-radio').forEach(el => {
            el.disabled = isDisabled;
        });
    }

    // Initial load
    document.querySelectorAll('.sms-row').forEach(row => toggleReadonly(row));

    // On change
    document.querySelectorAll('.send-status').forEach(radio => {
        radio.addEventListener('change', function () {
            toggleReadonly(this.closest('.sms-row'));
        });
    });

});


$("#smsForm").submit(function(e) {
    e.preventDefault(); 

    let form = $("#smsForm")[0];
    let data = new FormData(form);

    $(".error").text("");
    $(".is-invalid").removeClass("is-invalid");

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.sms-template-update') }}",
        data: data,
        dataType: "json",
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
                $.each(response.error, function(key, value){
                    let field = $('[name="'+key+'"]');
                    field.addClass('is-invalid');
                    field.closest('td').find('.error').text(value[0]);
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error: " + textStatus + " - " + errorThrown);
        }
    });
});
</script>

@endsection
