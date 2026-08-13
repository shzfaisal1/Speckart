@extends('layouts.master')
@section('content')
<section class="domestic-orders mt-2">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="domestic-orders-header">
                    <h3>Change Password</h3>
                    
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 mb-3">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.quickdaak-password-update') }}">
                            @csrf
                            <div class="form-row">
                                <div class="col">
                                    <label for="">Current Password</label>
                                    <input type="text" class="form-control"  placeholder="Enter current password" name="curr_password" id="curr_password" required>
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="">New Password</label>
                                    <input type="text" class="form-control"  placeholder="Enter new password" name="new_password" id="new_password" required>
                                </div>
                            </div>
                            <br>
                            <div class="form-row">
                                <div class="col">
                                    <label for="">Confirm  New Password</label>
                                    <input type="text" class="form-control"  placeholder="Enter confirm new password" name="con_password" id="con_password" required>
                                    <span id='message'></span>
                                </div>
                                
                            </div>
                            <div class="form-row">
                                <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3">
                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                    Update
                                </button>
                            </div>
                        </form>    
                </div>
            </div>
                
            </div>
        </div> 
    </div>
</section>







@endsection
