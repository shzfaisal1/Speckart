<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@php
    $usr = Auth::user();
@endphp
<aside class="app-sidebar comb-sidebar">
    <div class="app-sidebar__user">
        <div class="dropdown user-pro-body text-center">
            <div class="user-pic">
                <img src="{{asset('frontend/asset/img/logo/Specskart-logo.png')}}" class="avatar-xl mb-1">
                <p>Staff ID : {{ Auth::user()->staff_id }}</p>
            </div>
        </div>
    </div>
    @if ($usr->roles[0]->name == 'Franchise')
        @include('layouts.partials.sidebar-client')
    @else

    <ul class="side-menu">
        <li>
            <a class="side-menu__item @if($page_title == 'Dashboard') active @endif" href="{{route('index')}}">
                <img src="{{ asset('/assets/images/speckart-Icons/Dashboard.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">Dashboard</span>
            </a>
        </li>
               @if ($usr->can('Store-list') || $usr->can('Store-Edit') || $usr->can('Store-Create'))
        <li>
            <a class="side-menu__item @if($page_title == 'Store' || $page_title == 'Create Store' || $page_title == 'Update Store') active @endif"  href="{{route('admin.store-list')}}">
                <img src="{{ asset('/assets/images/speckart-Icons/Finance.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">Store</span>
            </a>
        </li>
        @endif
        
        
        @if ($usr->can('Master-Brand') || $usr->can('Master-Size') || $usr->can('Master-Shape') || $usr->can('Master-Color') || $usr->can('Master-Material')
        || $usr->can('Master-Type') || $usr->can('Master-Variants') || $usr->can('Master-Coating') || $usr->can('Master-Design') || $usr->can('Master-Index')
        || $usr->can('Master-CT') || $usr->can('Master-Validity') || $usr->can('Master-Number'))
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/Dashboard.png') }}" class="avatar-xl mb-1">
            <span class="side-menu__label">Master</span><i class="angle fa fa-angle-right"></i></a>
            <ul class="slide-menu">
                @if ($usr->can('Master-Brand'))
                <li><a href="{{route('admin.brand-master')}}" class="slide-item">Brand / Company</a></li>
                @endif
                @if ($usr->can('Master-Size'))
                <li><a href="{{route('admin.size-master')}}" class="slide-item">Size</a></li>
                @endif
                @if ($usr->can('Master-Shape'))
                <li><a href="{{route('admin.shape-master')}}" class="slide-item">Shape</a></li>
                @endif
                @if ($usr->can('Master-Color'))
                <li><a href="{{route('admin.color-master')}}" class="slide-item">Color</a></li>
                @endif
                @if ($usr->can('Master-Material'))
                <li><a href="{{route('admin.material-master')}}" class="slide-item">Material</a></li>
                @endif
                @if ($usr->can('Master-Type'))
                <li><a href="{{route('admin.type-master')}}" class="slide-item">Type</a></li>
                <li><a href="{{route('admin.product-types.index')}}" class="slide-item">Product Types</a></li>
                <li><a href="{{route('admin.collections.index')}}" class="slide-item">Collections</a></li>
                <li><a href="{{route('admin.categories.index')}}" class="slide-item">Categories</a></li>
                <li><a href="{{route('admin.subcategories.index')}}" class="slide-item">Subcategories</a></li>
                @endif
                @if ($usr->can('Master-Variants'))
                <li><a href="{{route('admin.variant-master')}}" class="slide-item">Variants</a></li>
                @endif
                @if ($usr->can('Master-Coating'))
                <li><a href="{{route('admin.coating-master')}}" class="slide-item">Coating</a></li>
                @endif
                @if ($usr->can('Master-Design'))
                <li><a href="{{route('admin.design-master')}}" class="slide-item">Design</a></li>
                @endif
                @if ($usr->can('Master-Index'))
                <li><a href="{{route('admin.index-master')}}" class="slide-item">Index</a></li>
                @endif
                @if ($usr->can('Master-CT'))
                <li><a href="{{route('admin.ct-master')}}" class="slide-item">CT (Center Thickness)</a></li>
                @endif
                @if ($usr->can('Master-Validity'))
                <li><a href="{{route('admin.validity-master')}}" class="slide-item">Validity In Days</a></li>
                @endif
                @if ($usr->can('Master-Number'))
                <li><a href="{{route('admin.variant-master')}}" class="slide-item">Number</a></li>
                @endif
            </ul>
        </li>   
        @endif
        
        @if ($usr->can('Product-Frame') || $usr->can('Product-Goggles') || $usr->can('Product-Glass') || $usr->can('Product-Contact-Lens') || $usr->can('Product-Solution')
        || $usr->can('Product-Other') || $usr->can('Product-Non-Chargeable') || $usr->can('Product-Import'))
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/Dashboard.png') }}" class="avatar-xl mb-1">
            <span class="side-menu__label">Products</span><i class="angle fa fa-angle-right"></i></a>
            <ul class="slide-menu">
                @if ($usr->can('Product-Frame'))
                <li><a href="{{ url(config('app.vendor_path').'/frameproduct')}}" class="slide-item">Frame</a></li>
                @endif
                @if ($usr->can('Product-Goggles'))
                <li><a href="{{ url(config('app.vendor_path').'/gogglesproduct')}}" class="slide-item">Goggles</a></li>
                @endif
                @if ($usr->can('Product-Glass'))
                <li><a href="{{ url(config('app.vendor_path').'/glassproduct')}}" class="slide-item">Glass</a></li>
                @endif
                @if ($usr->can('Product-Contact-Lens'))
                <li><a href="{{ url(config('app.vendor_path').'/lensproduct')}}" class="slide-item">Contact Lens</a></li>
                @endif
                @if ($usr->can('Product-Solution'))
                <li><a href="{{ url(config('app.vendor_path').'/solutionproduct')}}" class="slide-item">Solution</a></li>
                @endif
                @if ($usr->can('Product-Other'))
                <li><a href="{{ url(config('app.vendor_path').'/otherproduct')}}" class="slide-item">Other</a></li>
                @endif
                @if ($usr->can('Product-Non-Chargeable'))
                <!--<li><a href="#" class="slide-item">Non-Chargeable</a></li>-->
                @endif
                @if ($usr->can('Product-Import'))
                <li><a href="{{ url(config('app.vendor_path').'/importproduct')}}" class="slide-item">Product Import</a></li>
                @endif
            </ul>
        </li>
        @endif

        @if ($usr->can('Product-Frame') || $usr->can('Product-Goggles') || $usr->can('Product-Glass'))
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/Dashboard.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">B2C Product Catalog</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                <li><a href="{{ route('admin.catalog.index') }}" class="slide-item">All Products</a></li>
                <li><a href="{{ route('admin.products.create', ['type' => 'Frame']) }}" class="slide-item">Add Product</a></li>
            </ul>
        </li>
        @endif

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/NDR.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">B2C Online Orders</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                <li><a href="{{ route('admin.b2c-orders.index') }}" class="slide-item">All Orders</a></li>
                {{-- <li><a href="{{ route('admin.b2c-orders.index', ['rx_status' => 'pending_review']) }}" class="slide-item">Pending Rx Verification</a></li> --}}
                {{-- <li><a href="{{ route('admin.b2c-orders.index', ['order_status' => 'processing']) }}" class="slide-item">In Lab / Processing</a></li> --}}
                {{-- <li><a href="{{ route('admin.b2c-orders.index', ['order_status' => 'shipped']) }}" class="slide-item">Shipped Orders</a></li> --}}
                <!-- <li><a href="{{ route('admin.b2c-customers.index') }}" class="slide-item">Registered Customers</a></li> -->
                <li><a href="{{ route('admin.home-eye-test.index') }}" class="slide-item">Home Eye Test Bookings</a></li>
                <li><a href="{{ route('admin.shipping-charges.index') }}" class="slide-item">Shipping Charges</a></li>
            </ul>
        </li>
        
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/Dashboard.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">Lens System</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                <!--<li><a href="{{route('admin.power-types.index')}}" class="slide-item">Power Type</a></li>-->
                <li><a href="{{route('admin.lens-packages.index')}}" class="slide-item">Lens Packages</a></li>
                <li><a href="{{route('admin.lens-benefits.index')}}" class="slide-item">Lens Benefits</a></li>
                <li><a href="{{route('admin.lens-tags.index')}}" class="slide-item">Lens Tags</a></li>
                <!--<li><a href="{{route('admin.coupons.index')}}" class="slide-item">Coupons</a></li>-->
                <!--@if ($usr->can('Master-Type'))-->
                <!--<li><a href="{{route('admin.product-types.index')}}" class="slide-item">Lens - Product Types</a></li>-->
                <!--<li><a href="{{route('admin.type-master')}}" class="slide-item">Lens - Frame Types</a></li>-->
                <!--@endif-->
                <!--@if ($usr->can('Master-Shape'))-->
                <!--<li><a href="{{route('admin.shape-master')}}" class="slide-item">Lens - Frame Shapes</a></li>-->
                <!--@endif-->
                <!--@if ($usr->can('Master-Brand'))-->
                <!--<li><a href="{{route('admin.brand-master')}}" class="slide-item">Lens - Brands</a></li>-->
                <!--@endif-->
            </ul>
        </li>
        
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/Dashboard.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">Offers & Promotions</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                <li><a href="{{ url(config('app.admin_path').'/offers/create') }}" class="slide-item">Create Offer</a></li>
                <li><a href="{{ url(config('app.admin_path').'/offers') }}" class="slide-item">Offer List</a></li>
                <li><a href="{{ url(config('app.admin_path').'/gift-vouchers') }}" class="slide-item">Gift Vouchers</a></li>
            </ul>
        </li>

        <!--<li class="slide">-->
        <!--    <a class="side-menu__item" data-toggle="slide" href="#">-->
        <!--        <img src="{{ asset('/assets/images/speckart-Icons/Collection.png') }}" class="avatar-xl mb-1">-->
        <!--        <span class="side-menu__label">Homepage Banners</span><i class="angle fa fa-angle-right"></i>-->
        <!--    </a>-->
        <!--    <ul class="slide-menu">-->
        <!--        <li><a href="{{ url(config('app.admin_path').'/banners') }}" class="slide-item">Manage Banners</a></li>-->
        <!--    </ul>-->
        <!--</li>-->
       
        
        @if ($usr->can('Purchase-Add') || $usr->can('Purchase-History')  || $usr->can('Purchase-Glass-Grid')  || $usr->can('Purchase-Barcode') ||
        $usr->can('Purchase-Additional-Discount') || $usr->can('Purchase-Return') || $usr->can('Purchase-Missing_Price') || $usr->can('Challan-Add') 
        || $usr->can('Challan-Pending') || $usr->can('Purchase-Pending') || $usr->can('Purchase-Edit-History') || $usr->can('Purchase-Generate-Barcode'))
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/Pickup-request.png') }}" class="avatar-xl mb-1">
            <span class="side-menu__label">Purchase</span><i class="angle fa fa-angle-right"></i></a>
            <ul class="slide-menu">
                @if ($usr->can('Purchase-Add'))
                <li><a href="{{route('admin.add-purchase')}}" class="slide-item">Add Purchase</a></li>
                @endif
                @if ($usr->can('Purchase-History'))
                <li><a href="{{route('admin.purchase-history')}}" class="slide-item">Purchase History</a></li>
                @endif
                @if ($usr->can('Purchase-Edit-History'))
                <li><a href="{{route('admin.purchase-edit-history')}}" class="slide-item">Purchase Edit History</a></li>
                @endif
                @if ($usr->can('Purchase-Return'))
                <li><a href="{{route('admin.purchase-return')}}" class="slide-item">Purchase Return</a></li>
                @endif
                 @if ($usr->can('Challan-Add'))
                <li><a href="{{route('admin.add-challan')}}" class="slide-item">Add Challan</a></li>
                @endif
                @if ($usr->can('Challan-Pending'))
                <li><a href="{{route('admin.pending-challan')}}" class="slide-item">Pending Challan</a></li>
                @endif
                
                @if ($usr->can('Purchase-Pending'))
                <li><a href="{{route('admin.pending-purchase')}}" class="slide-item">Purchase Pending</a></li>
                @endif
                
                <!-- @if ($usr->can('Purchase-Generate-Barcode'))
                <li><a href="{{route('admin.generate-barcode')}}" class="slide-item">Generate  Barcode</a></li>
                @endif-->
                @if ($usr->can('Purchase-Barcode'))
                <li><a href="{{route('admin.purchase-barcode')}}" class="slide-item">Pending Barcode</a></li>
                @endif
                @if ($usr->can('Purchase-Barcode'))
                <li><a href="{{route('admin.confirm-barcode')}}" class="slide-item">Confirm Barcode</a></li>
                @endif
                @if ($usr->can('Purchase-Glass-Grid'))
                <li><a href="{{route('admin.purchase-grid')}}" class="slide-item">Glass Grid Purchase</a></li>
                @endif
                @if ($usr->can('Purchase-Missing_Price'))
                <li><a href="{{route('admin.missing-purchase-price')}}" class="slide-item">Missing Purchase Price</a></li>
                @endif
                @if ($usr->can('Purchase-Additional-Discount'))
                <li><a href="{{route('admin.additional-discount')}}" class="slide-item">Additional Discount & Other Cost</a></li>
                @endif
            </ul>
        </li>
        @endif


        
        
        @if ($usr->can('Inventory-Level')|| $usr->can('Inventory-Transfer-Stock-To-Store') || $usr->can('Inventory-Transfer-Stock-Using-Barcode') 
        || $usr->can('Inventory-Transfer-Stock-History') || $usr->can('Inventory-Audit') || $usr->can('Inventory-Adjustment-History') 
        || $usr->can('Inventory-Glass-Grid-View-Report') || $usr->can('Inventory-Stock-Movement') || $usr->can('Inventory-Track-Barcode') 
        || $usr->can('Inventory-Recevied-Stock-From-Store'))
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/shipments.png') }}" class="avatar-xl mb-1">
            <span class="side-menu__label">Inventory</span><i class="angle fa fa-angle-right"></i></a>
            <ul class="slide-menu">

                @if ($usr->can('Inventory-Level'))
                <li><a href="{{route('admin.inventory-level')}}" class="slide-item">Inventory Level</a></li>
                @endif


                @if ($usr->can('Inventory-Transfer-Stock-Using-Barcode'))
                <li><a href="{{route('admin.barcode-transfer')}}" class="slide-item">Transfer Stock Using Barcode</a></li>
                @endif
                
                @if ($usr->can('Inventory-Transfer-Stock-To-Store'))
                <li><a href="{{route('admin.stock-transfer')}}" class="slide-item">Transfer Stock From Store</a></li>
                @endif
                
                @if ($usr->can('Inventory-Recevied-Stock-From-Store'))
                <li><a href="{{route('admin.stock-received-store')}}" class="slide-item">Recevied Stock From Store</a></li>
                @endif
               
                @if ($usr->can('Inventory-Audit'))
                <li><a href="{{route('admin.inventory-audit')}}" class="slide-item">Inventory Audit</a></li>
                @endif
                @if ($usr->can('Inventory-Stock-Movement'))
                <li><a href="{{route('admin.stock-movement')}}" class="slide-item">Stock Movement</a></li>
                @endif
                @if ($usr->can('Inventory-Adjustment-History'))
                <li><a href="{{route('admin.inventory-adjustment-history')}}" class="slide-item">Inventory Adjustment History</a></li>
                @endif
                <!--@if ($usr->can('Inventory-Glass-Grid-View-Report'))
                <li><a href="#" class="slide-item">Glass Grid View Report</a></li>
                @endif-->
                @if ($usr->can('Inventory-Track-Barcode'))
                <li><a href="{{route('admin.track-barcode')}}" class="slide-item">Track Barcode</a></li>
                @endif
            </ul>
        </li>
        @endif
        
        
        @if ($usr->can('Sales-Create-Order')  || $usr->can('Sales-History') || $usr->can('Sales-Return') || $usr->can('Sales-Return-History')
        || $usr->can('Sales-Daily-Statement')|| $usr->can('Sales-Pending-Courier') || $usr->can('Sales-Courier-History') || $usr->can('Sales-Order-Tracking')
        || $usr->can('Sales-Create-Bulk-Invoice') || $usr->can('Sales-Prescription-History') || $usr->can('Sales-Create-Inter-Store-Sales')
        || $usr->can('Sales-Pending-History') || $usr->can('Sales-Return-Request-List') || $usr->can('Sales-Gatepass'))
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/NDR.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">Sales</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                @if ($usr->can('Sales-Create-Order'))
                <li><a href="{{route('admin.create-new-order')}}" class="slide-item">Create New Order</a></li>
                @endif
                @if ($usr->can('Sales-Pending-History'))
                <li><a href="{{route('admin.sale-pending-history')}}" class="slide-item">Pending Order</a></li>
                @endif
                @if ($usr->can('Sales-History'))
                <li><a href="{{route('admin.sale-history')}}" class="slide-item">Sales History</a></li>
                @endif
                 @if ($usr->can('Sales-Handover-History'))
                <li><a href="{{route('admin.handover-history')}}" class="slide-item">Product Handover History</a></li>
                @endif
                @if ($usr->can('Sales-Return'))
                <li><a href="{{route('admin.sale-return')}}" class="slide-item"> Sales Return</a></li>
                @endif
                @if ($usr->can('Sales-Return-Request-List'))
                <li><a href="{{route('admin.sale-return-request-history')}}" class="slide-item"> Sales Return Request List</a></li>
                @endif
                @if ($usr->can('Sales-Return-History'))
                <li><a href="{{route('admin.sale-return-history')}}" class="slide-item">Sales Return History</a></li>
                @endif
                 @if ($usr->can('Sales-Gatepass'))
                <li><a href="{{route('admin.gatepass-history')}}" class="slide-item">Gatepass List</a></li>
                @endif
                @if ($usr->can('Sales-Daily-Statement'))
                <li><a href="{{route('admin.daily-statement')}}" class="slide-item">Daily Statement PDF</a></li>
                @endif
                @if ($usr->can('Sales-Pending-Courier'))
                <li><a href="{{route('admin.pending-courier')}}" class="slide-item">Pending Courier</a></li>
                @endif
                @if ($usr->can('Sales-Courier-History'))
                <li><a href="{{route('admin.courier-history')}}" class="slide-item"> Courier History</a></li>
                @endif
                @if ($usr->can('Sales-Order-Tracking'))
                <li><a href="{{route('admin.order-item-tracking')}}" class="slide-item">Order Item Tracking</a></li>
                @endif
                @if ($usr->can('Sales-Create-Bulk-Invoice'))
                <li><a href="{{route('admin.bulk-invoice')}}" class="slide-item"> Create Bulk Invoice</a></li>
                @endif
                <!-- @if ($usr->can('Sales-Prescription-History'))
                <li><a href="#" class="slide-item">Prescription Update History</a></li>
                @endif-->
                 @if ($usr->can('Sales-Create-Inter-Store-Sales'))
                <li><a href="{{route('admin.inter-store-sale')}}" class="slide-item"> Create Inter Store Sales</a></li>
                @endif
            </ul>
        </li>
        @endif
        
        
        @if ($usr->can('B2B-Create-Sales-Challan')  || $usr->can('B2B-Create-New-Invoice') || $usr->can('B2B-Sales-History') 
		|| $usr->can('B2B-Sales-Return') || $usr->can('B2B-Sales-Return-History'))
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/Seller.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">B2B Sales</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                @if ($usr->can('B2B-Create-Sales-Challan'))
                <li><a href="#" class="slide-item">B2B Sales Challan</a></li>
                @endif
                @if ($usr->can('B2B-Create-New-Invoice'))
                <li><a href="{{route('admin.create-bb-invoice')}}" class="slide-item">B2B Create New Invoice</a></li>
                @endif
                @if ($usr->can('B2B-Sales-History'))
                <li><a href="{{route('admin.bb-sales-history')}}" class="slide-item">B2B Sales History</a></li>
                @endif
                @if ($usr->can('B2B-Sales-Return'))
                <li><a href="#" class="slide-item">B2B Sales Return</a></li>
                @endif
                @if ($usr->can('B2B-Sales-Return-History'))
                <li><a href="#" class="slide-item">B2B Sales Return History</a></li>
                @endif
            </ul>
        </li>
        @endif

        
        @if ($usr->can('Customer-Add') || $usr->can('Customer-List') || $usr->can('Customer-Label')|| $usr->can('Customer-Birthday') || $usr->can('Customer-Anniversary') 
        || $usr->can('Customer-Discount-Coupons')|| $usr->can('Customer-Loyalty-Program'))
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/reports.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">Customer</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                @if ($usr->can('Customer-Add'))
                <li><a href="{{route('admin.customer-add')}}" class="slide-item">Add Customer</a></li>
                @endif
                @if ($usr->can('Customer-List'))
                <li><a href="{{route('admin.customer-list')}}" class="slide-item">Customer List</a></li>
                <li><a href="{{route('admin.b2c-customers.index')}}" class="slide-item">Online B2C Customers</a></li>
                @endif
                @if ($usr->can('Customer-Birthday'))
                <li><a href="{{route('admin.customer-birthday-list')}}" class="slide-item">Birthday List</a></li>
                @endif
                @if ($usr->can('Customer-Anniversary'))
                <li><a href="{{route('admin.customer-anniversary-list')}}" class="slide-item">Anniversary List</a></li>
                @endif
                @if ($usr->can('Customer-Discount-Coupons'))
                <li><a href="{{route('admin.discount-coupons')}}" class="slide-item">Discount Coupons</a></li>
                @endif
                @if ($usr->can('Customer-Loyalty-Program'))
                <li><a href="{{route('admin.loyalty-program')}}" class="slide-item">Loyalty Program</a></li>
                @endif
            </ul>
        </li>
        @endif
        
        
        @if ($usr->can('Eye-Generate-Token') || $usr->can('Eye-Pre-Test-Queue') || $usr->can('Eye-Record'))
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/settings.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">Eye Test</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
                @if ($usr->can('Eye-Generate-Token'))
               <li><a href="{{route('admin.generate-token')}}" class="slide-item">Generate Token</a></li>
                @endif
              
               @if ($usr->can('Eye-Pre-Test-Queue'))
               <li><a href="{{route('admin.pretest-queue')}}" class="slide-item">Pre Test Queue</a></li>
               @endif
               
                @if ($usr->can('Eye-Record'))
               <li><a href="{{route('admin.eye-test-record')}}" class="slide-item">Eye Test Record</a></li>
               @endif
               <li><a href="{{route('admin.home-eye-test.index')}}" class="slide-item"><i class="bi bi-house-door me-1"></i> Home Eye Test</a></li>
            </ul>
        </li>
        @endif
        
        @if ($usr->can('Reports'))
        <li>
            <a class="side-menu__item" href="{{route('admin.generate-reports')}}">
                <img src="{{ asset('/assets/images/speckart-Icons/Finance.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">Reports</span>
            </a>
        </li>
         @endif

         

        @if ($usr->can('Account-Expenses') || $usr->can('Account-Ledgers') || $usr->can('Account-Group-Ledgers') || $usr->can('Account-Vouchers')
        || $usr->can('Account-Payable') || $usr->can('Account-Receivable') || $usr->can('Account-Statement') || $usr->can('Account-Trading-Balance-Sheet'))
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
            <img src="{{ asset('/assets/images/speckart-Icons/settings.png') }}" class="avatar-xl mb-1">
            <span class="side-menu__label">Account</span><i class="angle fa fa-angle-right"></i></a>
            <ul class="slide-menu">
                @if ($usr->can('Account-Expenses'))
                <li><a href="{{route('admin.store-expenses')}}" class="slide-item">Expenses</a></li>
                @endif
                @if ($usr->can('Account-Payable'))
                <li><a href="#" class="slide-item">Account Payable</a></li>
                @endif
                @if ($usr->can('Account-Receivable'))
                <li><a href="{{route('admin.account-receivable')}}" class="slide-item">Account Receivable</a></li>
                @endif
                @if ($usr->can('Account-Statement'))
               <!-- <li><a href="#" class="slide-item">Account Statement</a></li>-->
                @endif
            </ul>
        </li>
        @endif
        
        @if ($usr->can('Feedback-dashboard-A') || $usr->can('Feedback-dashboard-B') || $usr->can('Feedback-walkout-customer'))
        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <img src="{{ asset('/assets/images/speckart-Icons/settings.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">Sales Feedback</span><i class="angle fa fa-angle-right"></i>
            </a>
            <ul class="slide-menu">
               @if ($usr->can('Feedback-dashboard-A')) 
               <li><a href="{{route('admin.feedback-dashboard-A')}}" class="slide-item">2 Days Later Dahboard</a></li>
               @endif
               @if ($usr->can('Feedback-dashboard-B')) 
               <li><a href="{{route('admin.feedback-dashboard-B')}}" class="slide-item">6 Month Later Dahboard</a></li>
               @endif

            </ul>
        </li>
        
        @endif


        @if ($usr->can('Setting-Master')) 
        <li>
            <a class="side-menu__item" href="{{route('admin.setting-master')}}">
                <img src="{{ asset('/assets/images/speckart-Icons/settings.png') }}" class="avatar-xl mb-1">
                <span class="side-menu__label">Setting</span>
            </a>
        </li> 
        
        @endif

    </ul>
  @endif
</aside>
