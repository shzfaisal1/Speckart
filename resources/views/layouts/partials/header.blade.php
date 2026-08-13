@php
    $usr = Auth::user();
@endphp

<style>
    .avatar-md {
    width: 40px;
        height: 40px;
        line-height: 2rem;
        font-size: 1rem;
        margin-right: 5px;
    }
    
    .tooltip-custom {
    position: relative;
    display: inline-block;
}

.tooltip-custom .tooltip-text {
    visibility: hidden;
    background-color: #000;
    color: #fff;
    text-align: center;
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 12px;

    position: absolute;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);

    white-space: nowrap;
    z-index: 9999;

    opacity: 0;
    transition: opacity 0.3s;
}

.tooltip-custom:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}

.tooltip-img:hover::after{
    content: attr(data-title);
    position: absolute;
    background:#000;
    color:#fff;
    padding:6px 10px;
    border-radius:4px;
    font-size:12px;
}

</style>
<div class="app-header header top-header comb-header">
    <div class="container-fluid">
        <div class="d-flex">
            <a id="horizontal-navtoggle" class="animated-arrow hor-toggle"><span></span></a><!-- sidebar-toggle-->
            <a class="header-brand" href="{{route('index')}}"> <img src="{{asset('frontend/asset/img/logo/Specskart-logo.png')}}" alt="user-img" class="avatar-xl mb-1"> </a>
            <div class="dropdown side-nav">
                <a aria-label="Hide Sidebar" class="app-sidebar__toggle nav-link icon mt-1" data-toggle="sidebar"
                    href="#">
                    <i class="fe fe-align-left"></i>
                </a><!-- sidebar-toggle-->
            </div>
            <!--<a href="#" class="tooltip-custom">
                 <img class="avatar avatar-md brround tooltip-img" src="{{ asset('assets/images/speckart-Icons/walk_in.png')}}" alt=" image">
                 <span class="tooltip-text">Walk-In</span>
            </a>     
            @if ($usr->can('Eye-Generate-Token') || $usr->can('Eye-Pre-Test-Queue') || $usr->can('Eye-Record'))
            <a href="{{route('admin.generate-token')}}" class="tooltip-custom">
                <img class="avatar avatar-md brround tooltip-img" src="{{ asset('assets/images/speckart-Icons/Eye Test.png')}}" alt=" image">
                 <span class="tooltip-text">Eye Test</span>
            </a>    
            @endif
             @if ($usr->can('Mystry-Audit-Entry') || $usr->can('Mystry-Audit-History'))
            <a href="{{route('admin.mystry-audit-entry')}}" class="tooltip-custom">
                <img class="avatar avatar-md brround tooltip-img" src="{{ asset('assets/images/speckart-Icons/Mystry Audit_.png')}}" alt=" image">
                <span class="tooltip-text">Mystry Audit</span>
            </a>    
            @endif
            <a href="#" class="tooltip-custom">
                 <img class="avatar avatar-md brround tooltip-img" src="{{ asset('assets/images/speckart-Icons/NPS_.png')}}" alt=" image">  
                 <span class="tooltip-text">NPS</span>
            </a>   -->   
            <div class="dropdown dropdown-welcome">
                <h5><div id="clock" style="font-size: 15px;color: #fff;margin-top: 10px;"></div></h5>
            </div>
   
            <div class="d-flex order-lg-2 ml-auto">
                
                <!-- header-wallets-btn -->
                 
                <div class="dropdown header-notify">
                    <a class="nav-link icon" data-toggle="dropdown">
                        <i class="mdi mdi-bell-outline"></i>
                        <span class=" bg-success pulse-success "></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow pt-0">
                        <div class="dropdown-header border-bottom p-4 pt-0 mb-3 w-270">
                            <div class="d-flex">
                                <h5 class="dropdown-title float-left mb-1 font-weight-semibold text-drak"><p style="color: #002b49 !important;">No records found...</p></h5>
                                <a href="#" class="fe fe-align-justify text-right float-right ml-auto text-muted"></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dropdown">
                    <a class="nav-link pr-0 leading-none" href="#" data-toggle="dropdown" aria-expanded="false">
                        <img class="avatar avatar-md brround" src="{{ asset('assets/images/users/1.jpg')}}" alt=" image">
                        <div class="profile-details">
                            <span class="ml-3 font-weight-light">{{ Auth::user()->name }}</span>
                        </div>
                        <i class="fa fa-angle-down ml-1"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow w-250"> 
                      
                        <a href="{{ route('admin.change-password') }}" class="dropdown-item pt-3 pb-3">
                            <i class="dropdown-icon mdi mdi-account-outline text-primary "></i>
                             Change Password
                        </a>
                        

                        <a class="dropdown-item pt-3 pb-3" href="{{ route('logout') }}" onclick="event.preventDefault();
                        document.getElementById('admin-logout-form').submit();"><i
                                class="dropdown-icon  mdi  mdi-logout-variant text-primary"></i>Log
                            Out</a>
                        <form id="admin-logout-form" action="{{ route('logout') }}" method="POST"
                            style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>






