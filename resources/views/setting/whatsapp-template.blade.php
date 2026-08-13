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
                        <h3>WhatsApp  Template Settings</h3>
                        
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row">
               <div class="col-lg-12" >
                   <form id="whatsappForm" method="POST" method="POST" enctype="multipart/form-data">
                       @csrf
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap">
                            <thead class="bg-success text-white">
                            <tr>
                                <th>Title</th>
                                <th>Whatsapp Status</th>
                                <th>Template</th>
                                <th>Send Method</th>
                            </tr>
                            </thead>
                            
                            <tbody>
                            @php $whatsapp_template = DB::table('tbl_whatsapp_template')->get(); @endphp
                            
                            @foreach($whatsapp_template as $i => $whatsapp)
                            <tr class="sms-row" data-index="{{$i}}">
                                <td>
                                    {{$whatsapp->title}}
                                    <input type="hidden" name="sms_id[{{$i}}]" value="{{$whatsapp->id}}">
                                </td>
                            
                                {{-- SEND STATUS --}}
                                <td>
                                    <div class="form-check">
                                        <input type="radio"
                                               class="form-check-input send-status"
                                               name="send_status[{{$i}}]"
                                               value="0"
                                               {{ $whatsapp->send_status == '0' ? 'checked' : '' }}>
                                        Enable
                                    </div>
                            
                                    <div class="form-check">
                                        <input type="radio"
                                               class="form-check-input send-status"
                                               name="send_status[{{$i}}]"
                                               value="1"
                                               {{ $whatsapp->send_status == '1' ? 'checked' : '' }}>
                                        Disable
                                    </div>
                                </td>
                                <td>
                                    <textarea name="Template[{{$i}}]"
                                              class="form-control controlled">{{$whatsapp->Template}}</textarea>
                                </td>
    
               
                                <td>
                                    <div class="form-check">
                                        <input type="radio"
                                               class="form-check-input controlled-radio"
                                               name="send_method[{{$i}}]"
                                               value="0"
                                               {{ $whatsapp->send_method == '0' ? 'checked' : '' }}>
                                        API
                                    </div>
                            
                                    <div class="form-check">
                                        <input type="radio"
                                               class="form-check-input controlled-radio"
                                               name="send_method[{{$i}}]"
                                               value="1"
                                               {{ $whatsapp->send_method == '1' ? 'checked' : '' }}>
                                        WEB
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


$("#whatsappForm").submit(function(e) {
    e.preventDefault(); 

    let form = $("#whatsappForm")[0];
    let data = new FormData(form);

    $(".error").text("");
    $(".is-invalid").removeClass("is-invalid");

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.whatsapp-template-update') }}",
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
