<ul class="side-menu">
    <li>
        <a class="side-menu__item @if($page_title == 'Dashboard') active @endif" href="{{route('index')}}">
            <img src="{{ asset('/assets/images/Quickdaak-Icons/Dashboard.png') }}" alt="user-img" class="avatar-xl mb-1">
            <span class="side-menu__label">Dashboard</span></a>
    </li>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <img src="{{ asset('/assets/images/Quickdaak-Icons/orders.png') }}" alt="user-img" class="avatar-xl mb-1">
            <span class="side-menu__label">Orders</span>
            <i class="angle fa fa-angle-right"></i>
        </a>
        <ul class="slide-menu">
            <li><a href="{{route('client.create-domestic-order')}}" class="slide-item"> Add Order</a></li>
            <li><a href="{{route('client.orders',['domestic'])}}" class="slide-item">Orders</a></li>
        </ul>
    </li>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <img src="{{ asset('/assets/images/Quickdaak-Icons/shipments.png') }}" alt="user-img" class="avatar-xl mb-1">
            <span class="side-menu__label">Shipments</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
            <li><a href="{{route('client.ready-to-ship')}}" class="slide-item">Ready To Ship</a></li>
            <li><a href="{{route('client.pickups-manifests')}}" class="slide-item">Pickups & Manifests</a></li>
            <li><a href="{{route('client.order-intransit')}}" class="slide-item"> In Transit</a></li>
            <li><a href="{{route('client.order-delivered')}}" class="slide-item"> Delivered</a></li>
            <li><a href="{{route('client.rto-order')}}" class="slide-item"> RTO Shipments</a></li>
            <li><a href="{{route('client.all-shipment-list')}}" class="slide-item">All Shipments</a></li>
        </ul>
    </li>
    <li>
        <a class="side-menu__item @if($page_title == 'Return Order') active @endif" href="{{ route('client.return-order',['all']) }}">
            <img src="{{ asset('/assets/images/Quickdaak-Icons/Pickup-request.png') }}" alt="user-img" class="avatar-xl mb-1">
            <span class="side-menu__label">Returns</span>
        </a>
    </li> 
    <li>
        <a class="side-menu__item @if($page_title == 'NDR') active @endif" href="#">
            <img src="{{ asset('/assets/images/Quickdaak-Icons/NDR.png') }}" alt="user-img" class="avatar-xl mb-1">
            <span class="side-menu__label">NDR/Exceptions</span>
        </a>
    </li>
    {{--<li>
        <a class="side-menu__item @if($page_title == 'Pickup Request') active @endif" href="{{route('client.pickup-request-list')}}">
            <i class="side-menu__icon fe fe-monitor"></i>
            <span class="side-menu__label">Pickup Request</span>
        </a>
    </li>
   <li>
        <a class="side-menu__item @if($page_title == 'Manifest List') active @endif" href="{{route('client.manifest-list')}}">
            <i class="side-menu__icon fe fe-monitor"></i>
            <span class="side-menu__label">Manifest List</span>
        </a>
    </li>--}}
    <li>
        <a class="side-menu__item @if($page_title == 'Warehouse') active @endif" href="{{ url(config('app.vendor_path').'/warehouses')}}">
            <img src="{{ asset('/assets/images/Quickdaak-Icons/Warehouses.png') }}" alt="user-img" class="avatar-xl mb-1">
            <span class="side-menu__label">Warehouse</span>
        </a>
    </li>
    {{--<li>
        <a class="side-menu__item @if($page_title == 'Weight Discrepancies') active @endif" href="#">
            <i class="side-menu__icon fe fe-monitor"></i>
            <span class="side-menu__label">Weight Discrepancies</span>
        </a>
    </li>--}}
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <img src="{{ asset('/assets/images/Quickdaak-Icons/Tools.png') }}" alt="user-img" class="avatar-xl mb-1">
            <span class="side-menu__label">Information Center</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
            <li><a href="{{route('client.rate-calculator')}}" class="slide-item">Rate Calculator</a></li>
            <li><a href="{{route('client.rate-card')}}" class="slide-item">Rate Card</a></li>
        </ul>
    </li>
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <img src="{{ asset('/assets/images/Quickdaak-Icons/billing.png') }}" alt="user-img" class="avatar-xl mb-1">
            <span class="side-menu__label">Billing</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
            <li><a href="#" class="slide-item">Shipping Charges</a></li>
            <li><a href="#" class="slide-item"> COD Remittance</a></li>
            <li><a href="{{route('client.wallet-history')}}" class="slide-item"> Wallet Histroy</a></li>
            <li><a href="{{route('client.passbook-history')}}" class="slide-item"> Passbook</a></li>
            <li><a href="#" class="slide-item"> Deduction</a></li>
            <li><a href="{{route('client.recharge-history')}}" class="slide-item"> Recharge</a></li>
            <li><a href="#" class="slide-item"> Weight Reconciliation</a></li>
        </ul>
    </li>
    <li>
        <a class="side-menu__item @if($page_title == 'Reports') active @endif" href="{{route('client.seller-report')}}">
            <img src="{{ asset('/assets/images/Quickdaak-Icons/reports.png') }}" alt="user-img" class="avatar-xl mb-1">
            <span class="side-menu__label">Reports</span>
        </a>
    </li>
   
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <img src="{{ asset('/assets/images/Quickdaak-Icons/settings.png') }}" alt="user-img" class="avatar-xl mb-1">
            <span class="side-menu__label">Setting</span>
            <i class="angle fa fa-angle-right"></i>
        </a>
        <ul class="slide-menu">
            <li><a href="{{ route('client.channels.index') }}" class="slide-item">Channel List</a></li>
            <li><a href="{{ route('client.label.setting') }}" class="slide-item"> Label Setting</a></li>
			<li><a href="{{ route('client.courier-priority') }}" class="slide-item">Courier Priority</a></li>
        </ul>
    </li>
     <li>
        <a class="side-menu__item @if($page_title == 'Support Tickets') active @endif" href="{{route('client.support-tickets')}}">
            <img src="{{ asset('/assets/images/Quickdaak-Icons/support.png') }}" alt="user-img" class="avatar-xl mb-1">
            <span class="side-menu__label">Support Tickets</span>
        </a>
    </li>
</ul>
