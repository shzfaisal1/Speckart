@extends('layouts.master')
@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
.report-card {
      background: #fff;
      border: 1px solid #dee2e6;
      border-radius: 10px;
      text-align: center;
      padding: 30px 15px;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .report-card:hover {
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transform: translateY(-3px);
    }
    .report-card i {
      font-size: 40px;
      color: #00484a;
      margin-bottom: 15px;
    }
    .report-title {
      font-weight: 600;
      font-size: 16px;
      color: #333;
    }
</style>  

@endsection
@section('content')
@php
     $usr = Auth::guard()->user();
 @endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Reports</h3>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 col-sm-6">
                    <a href="{{route('admin.inventory-report')}}">
                        <div class="report-card">
                          <i class="bi bi-x-diamond-fill"></i>
                          <div class="report-title">Inventory Report</div>
                        </div>
                    </a>    
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{route('admin.stock-transfer-report')}}">
                        <div class="report-card">
                          <i class="bi bi-file-earmark-text-fill"></i>
                          <div class="report-title">Transfer Stock Report</div>
                        </div>
                    </a>    
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{route('admin.purchase-report')}}">
                        <div class="report-card">
                          <i class="bi bi-cart-check-fill"></i>
                          <div class="report-title">Purchase Report</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{route('admin.purchase-return-report')}}">
                        <div class="report-card">
                          <i class="bi bi-cart-check-fill"></i>
                          <div class="report-title">Purchase Return Report</div>
                        </div>
                    </a>    
                </div>
            </div> 
            <div class="row mb-3">
                <div class="col-md-3 col-sm-6">
                    <a href="{{route('admin.sales-report')}}">
                        <div class="report-card">
                          <i class="bi bi-file-earmark-text-fill"></i>
                          <div class="report-title">Sales Report</div>
                        </div>
                    </a>
                </div>
                 <div class="col-md-3 col-sm-6">
                    <a href="{{route('admin.pending-order-report')}}">
                        <div class="report-card">
                          <i class="bi bi-file-earmark-text-fill"></i>
                          <div class="report-title">Pending Order Report</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{route('admin.sales-return-report')}}">
                        <div class="report-card">
                          <i class="bi bi-file-earmark-text-fill"></i>
                          <div class="report-title">Sales Return Report</div>
                        </div>
                    </a>    
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{route('admin.GST-input-report')}}">
                        <div class="report-card">
                          <i class="bi bi-file-earmark-text-fill"></i>
                          <div class="report-title">GST Input Report</div>
                        </div>
                    </a>    
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 col-sm-6">
                    <a href="{{route('admin.GST-out-report')}}">
                        <div class="report-card">
                          <i class="bi bi-file-earmark-text-fill"></i>
                          <div class="report-title">GST Output Report</div>
                        </div>
                    </a>    
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{route('admin.payment-report')}}">
                        <div class="report-card">
                          <i class="bi bi-bank2"></i>
                          <div class="report-title">Payment Report</div>
                        </div>
                    </a>    
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{route('admin.loss-report')}}">
                        <div class="report-card">
                          <i class="bi bi-building"></i>
                          <div class="report-title">Loss or Damage  Report</div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="report-card">
                      <i class="bi bi-bullseye"></i>
                      <div class="report-title">Eye Test Report</div>
                    </div>
                </div>
                
            </div> 
            <div class="row mb-3"> 
                
                <div class="col-md-3 col-sm-6">
                    <div class="report-card">
                      <i class="bi bi-book-fill"></i>
                      <div class="report-title">Expenses Report</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')



@endsection
