@extends('website.layout.master')
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .active-filter {
            border-color: #07484A !important;
            background-color: rgba(7, 72, 74, 0.05) !important;
            color: #07484A !important;
        }
        .form-check-input:checked {
            background-color: #07484A !important;
            border-color: #07484A !important;
        }
        .frame-type:hover {
            border-color: #07484A;
        }
    </style>
@endsection

@section('content')
    {{-- ================================= --}}
    {{-- start product search 1 --}}
    {{-- ================================= --}}
    <section class="product breadcrumbs-section bg-white border-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-4">
                    <ul id="breadcrumbs"
                        class="m-0 p-0 list-unstyled d-flex align-items-center gap-2 flex-wrap">

                        <li>
                            <a href="{{ route('home') }}"
                            class="text-muted text-decoration-none">
                                Home
                            </a>
                        </li>

                        <li>
                            <i class="bi bi-chevron-right text-muted"
                            style="font-size:10px;"></i>
                        </li>

                        <li>
                            <a href="{{ route('products') }}"
                            class="fw-medium text-decoration-none">

                                @if(request('tag'))
                                    {{ ucwords(str_replace('-', ' ', request('tag'))) }}
                                @elseif(request('category'))
                                    {{ \App\Models\Category::where('slug', request('category'))->first()->name ?? 'Eye Glasses' }}
                                @else
                                    {{ $page_title ?? 'Eye Glasses' }}
                                @endif

                            </a>
                        </li>

                    </ul>
                </div>
            
                <!-- Left Title -->
                

                <!-- Right Side Section -->
                <div class="col-lg-8 col-md-8 mt-2 mt-md-0">
                    <div class="row align-items-center justify-content-end">
                        <div class="col-lg-8 col-md-7 col-6">
                            <div class="search-box">
                                <form action="{{ route('products') }}" method="GET" class="d-flex w-100" id="page-search-form">
                                    @if(request()->has('category'))
                                        <input type="hidden" name="category" value="{{ request('category') }}">
                                    @endif
                                    <input type="text" name="search" id="page-search-input" class="ajax-search-input" placeholder="Search eyeglasses, sunglasses" value="{{ request('search') }}" autocomplete="off">
                                    <!--<button type="submit">Search</button>-->
                                </form>
                                
                            </div>
                            
                        </div>
                        <div class="col-lg-2 col-md-5 col-6">
                            <div class="d-flex gap-2 justify-content-md-end">
                                <div class="dropdown">
                            <button class="dropdown-btn dropdown-toggle" id="sort-dropdown-btn" data-bs-toggle="dropdown" aria-expanded="false">
                                Sort By
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><a class="dropdown-item py-2 sort-option" href="javascript:void(0)" data-sort="most_viewed">Most Viewed</a></li>
                                <li><a class="dropdown-item py-2 sort-option" href="javascript:void(0)" data-sort="newest">Newest</a></li>
                                <li><a class="dropdown-item py-2 sort-option" href="javascript:void(0)" data-sort="price_low">Price: Low to High</a></li>
                                <li><a class="dropdown-item py-2 sort-option" href="javascript:void(0)" data-sort="price_high">Price: High to Low</a></li>
                            </ul>
                        </div>
                                <button class="dropdown-btn d-lg-none"
                                        data-bs-toggle="modal"
                                        data-bs-target="#filterModal">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- ================================= --}}
    {{-- end product search 1 --}}
    {{-- ================================= --}}
    {{-- ================================= --}}
    {{-- start product 2 --}}
    {{-- ================================= --}}
    <section class="prod">
        <div class="container pt-3 pb-4">
            <div class="row">
                
                <!-- Sidebar Filters -->
                <div class="col-lg-3 col-12 d-none d-lg-block">
                    <div class="filter-sidebar p-1 rounded-4 shadow-sm">

                        <!-- Sticky Header -->
                        <div class="filter-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">Filters</h5>
                                </div>

                                <a href="javascript:void(0)" id="btn-reset-filters"
                                class="btn btn-sm btn-outline-secondary rounded-pill">
                                    Reset
                                </a>
                            </div>
                        </div>
                        <div class="filter-scroll p-0">
                            <div class="accordion filter-accordion" id="filterAccordion">

                                <!-- Frame Type -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header" id="headingType">
                                        <button class="accordion-button bg-transparent shadow-none " type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseType">
                                            Frame Type
                                        </button>
                                    </h2>
                                    <div id="collapseType" class="accordion-collapse collapse show"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="frame_type" data-value="Full Rim">
                                                    <img src="{{ asset('assets/img/icon/full-rim.png') }}" class="framsize" >
                                                    <div class="fontsize" >Full Rim</div>
                                                </div>
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="frame_type" data-value="Half Rim">
                                                    <img src="{{ asset('assets/img/icon/half-rim.png') }}" class="framsize" >
                                                    <div class="fontsize" >Half Rim</div>
                                                </div>
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="frame_type" data-value="Rimless">
                                                    <img src="{{ asset('assets/img/icon/rimless.png') }}" class="framsize" >
                                                    <div class="fontsize" >Rimless</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Frame Shape -->
                                 <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseShape">
                                            Frame Shape
                                        </button>
                                    </h2>
                                    <div id="collapseShape" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="shape" data-value="Round">
                                                    <img src="{{ asset('assets/img/icon/round.png') }}" class="framsize" >
                                                    <div class="fontsize" style="font-size: 11px; padding: 5px 0;">Round</div>
                                                </div>
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="shape" data-value="Rectangle">
                                                    <img src="{{ asset('assets/img/icon/rectangle.png') }}" class="framsize" >
                                                    <div class="fontsize" style="font-size: 11px; padding: 5px 0;">Rectangle</div>
                                                </div>
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="shape" data-value="Aviator">
                                                    <img src="{{ asset('assets/img/icon/aviator.png') }}" class="framsize" >
                                                    <div class="fontsize" style="font-size: 11px; padding: 5px 0;">Aviator</div>
                                                </div>
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="shape" data-value="Cat Eye">
                                                    <img src="{{ asset('assets/img/icon/cateye.png') }}" class="framsize" >
                                                    <div class="fontsize" style="font-size: 11px; padding: 5px 0;">Cat Eye</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Frame Color -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseColor">
                                            Frame Color
                                        </button>
                                    </h2>
                                    <div id="collapseColor" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="color_0" data-filter="color" data-value="Black">
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="color_0">
                                                        <span class="color-dot border" style="background:#000000; width: 16px; height: 16px; border-radius: 50%; display: inline-block;"></span> 
                                                        Black
                                                    </label>
                                                </div>
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="color_1" data-filter="color" data-value="Brown">
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="color_1">
                                                        <span class="color-dot border" style="background:#8B4513; width: 16px; height: 16px; border-radius: 50%; display: inline-block;"></span> 
                                                        Brown
                                                    </label>
                                                </div>
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="color_2" data-filter="color" data-value="Blue">
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="color_2">
                                                        <span class="color-dot border" style="background:#0000FF; width: 16px; height: 16px; border-radius: 50%; display: inline-block;"></span> 
                                                        Blue
                                                    </label>
                                                </div>
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="color_3" data-filter="color" data-value="Gold">
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="color_3">
                                                        <span class="color-dot border" style="background:#FFD700; width: 16px; height: 16px; border-radius: 50%; display: inline-block;"></span> 
                                                        Gold
                                                    </label>
                                                </div>
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="color_4" data-filter="color" data-value="Silver">
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="color_4">
                                                        <span class="color-dot border" style="background:#C0C0C0; width: 16px; height: 16px; border-radius: 50%; display: inline-block;"></span> 
                                                        Silver
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Brands -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseBrand">
                                            Brands
                                        </button>
                                    </h2>
                                    <div id="collapseBrand" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox" type="checkbox" id="brand_1" data-filter="brand" data-value="Ray-Ban">
                                                <label class="form-check-label" for="brand_1">Ray-Ban</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox" type="checkbox" id="brand_2" data-filter="brand" data-value="Oakley">
                                                <label class="form-check-label" for="brand_2">Oakley</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox" type="checkbox" id="brand_3" data-filter="brand" data-value="Vincent Chase">
                                                <label class="form-check-label" for="brand_3">Vincent Chase</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox" type="checkbox" id="brand_4" data-filter="brand" data-value="John Jacobs">
                                                <label class="form-check-label" for="brand_4">John Jacobs</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox" type="checkbox" id="brand_5" data-filter="brand" data-value="Lenskart Air">
                                                <label class="form-check-label" for="brand_5">Lenskart Air</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Frame Size -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseSize">
                                            Frame Size
                                        </button>
                                    </h2>
                                    <div id="collapseSize" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="size_0" data-filter="size" data-value="Small">
                                                    <label class="form-check-label" for="size_0">Small</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="size_1" data-filter="size" data-value="Medium">
                                                    <label class="form-check-label" for="size_1">Medium</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="size_2" data-filter="size" data-value="Large">
                                                    <label class="form-check-label" for="size_2">Large</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Material -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseMaterial">
                                            Material
                                        </button>
                                    </h2>
                                    <div id="collapseMaterial" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="mat_0" data-filter="material" data-value="Metal">
                                                    <label class="form-check-label" for="mat_0">Metal</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="mat_1" data-filter="material" data-value="Acetate">
                                                    <label class="form-check-label" for="mat_1">Acetate</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="mat_2" data-filter="material" data-value="TR90">
                                                    <label class="form-check-label" for="mat_2">TR90</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
 <!-- Age Group -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAge">
                                            Age Group
                                        </button>
                                    </h2>
                                    <div id="collapseAge" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="age_0" data-filter="age" data-value="2-5yrs"><label class="form-check-label" for="age_0">2-5yrs</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="age_1" data-filter="age" data-value="5-8yrs"><label class="form-check-label" for="age_1">5-8yrs</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="age_2" data-filter="age" data-value="8-12yrs"><label class="form-check-label" for="age_2">8-12yrs</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="age_3" data-filter="age" data-value="Adult"><label class="form-check-label" for="age_3">Adult</label></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Occasion -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOccasion">
                                            Occasion
                                        </button>
                                    </h2>
                                    <div id="collapseOccasion" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="occ_0" data-filter="occasion" data-value="Casual"><label class="form-check-label" for="occ_0">Casual</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="occ_1" data-filter="occasion" data-value="Office"><label class="form-check-label" for="occ_1">Office</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="occ_2" data-filter="occasion" data-value="Party"><label class="form-check-label" for="occ_2">Party</label></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Face Shape -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaceShape">
                                            Face Shape
                                        </button>
                                    </h2>
                                    <div id="collapseFaceShape" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="fs_0" data-filter="face_shape" data-value="Round"><label class="form-check-label" for="fs_0">Round</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="fs_1" data-filter="face_shape" data-value="Oval"><label class="form-check-label" for="fs_1">Oval</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="fs_2" data-filter="face_shape" data-value="Square"><label class="form-check-label" for="fs_2">Square</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="fs_3" data-filter="face_shape" data-value="Heart"><label class="form-check-label" for="fs_3">Heart</label></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Sunglass Colour -->
                                <div class="accordion-item border-0 border-bottom" id="sunglassColourFilterSection">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSunglassColour">
                                            Sunglass Colour
                                        </button>
                                    </h2>
                                    <div id="collapseSunglassColour" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="sgc_0" data-filter="sunglass_colour" data-value="Black"><label class="form-check-label" for="sgc_0">Black</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="sgc_1" data-filter="sunglass_colour" data-value="Blue"><label class="form-check-label" for="sgc_1">Blue</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="sgc_2" data-filter="sunglass_colour" data-value="Red"><label class="form-check-label" for="sgc_2">Red</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="sgc_3" data-filter="sunglass_colour" data-value="Brown"><label class="form-check-label" for="sgc_3">Brown</label></div>
                                                <div class="form-check"><input class="form-check-input filter-checkbox" type="checkbox" id="sgc_4" data-filter="sunglass_colour" data-value="Green"><label class="form-check-label" for="sgc_4">Green</label></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrice">
                                            Price Range
                                        </button>
                                    </h2>
                                    <div id="collapsePrice" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body px-0">
                                            <div class="form-check">
                                                <input class="form-check-input filter-radio" type="radio" name="price_range" id="price1" data-filter="price_range" data-value="under_1000">
                                                <label class="form-check-label" for="price1">Under ₹1000</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-radio" type="radio" name="price_range" id="price2" data-filter="price_range" data-value="under_2000">
                                                <label class="form-check-label" for="price2">Under ₹2000</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-radio" type="radio" name="price_range" id="price3" data-filter="price_range" data-value="under_5000">
                                                <label class="form-check-label" for="price3">Under ₹5000</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Collections -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseColl">
                                            Collections
                                        </button>
                                    </h2>
                                    <div id="collapseColl" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="coll_1">
                                                <label class="form-check-label" for="coll_1">Summer Collection</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="coll_2">
                                                <label class="form-check-label" for="coll_2">Winter Collection</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="coll_3">
                                                <label class="form-check-label" for="coll_3">Classic Collection</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>
                    </div>

                </div>

                <!-- Product Grid -->
                <div class="col-lg-9 col-12 filtercardscroll" id="product-grid-container">
                    @include('website.products.product_grid')
                </div>
            </div>
        </div>
    </section>
    
    
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-scrollable px-3 py-4">
            <div class="modal-content">
    
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Filters</h5>
    
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>
                </div>
    
                <div class="modal-body p-0">
                    <div class="accordion" id="mobileFilterAccordion">

                                <!-- Frame Type -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header" id="headingType">
                                        <button class="accordion-button bg-transparent shadow-none " type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseType">
                                            Frame Type
                                        </button>
                                    </h2>
                                    <div id="collapseType" class="accordion-collapse collapse show"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="frame_type" data-value="Full Rim">
                                                    <img src="{{ asset('assets/img/icon/full-rim.png') }}" class="framsize" >
                                                    <div class="fontsize" >Full Rim</div>
                                                </div>
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="frame_type" data-value="Half Rim">
                                                    <img src="{{ asset('assets/img/icon/half-rim.png') }}" class="framsize" >
                                                    <div class="fontsize" >Half Rim</div>
                                                </div>
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="frame_type" data-value="Rimless">
                                                    <img src="{{ asset('assets/img/icon/rimless.png') }}" class="framsize" >
                                                    <div class="fontsize" >Rimless</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Frame Shape -->
                                 <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseShape">
                                            Frame Shape
                                        </button>
                                    </h2>
                                    <div id="collapseShape" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="d-flex flex-wrap gap-2">
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="shape" data-value="Round">
                                                    <img src="{{ asset('assets/img/icon/round.png') }}" class="framsize" >
                                                    <div class="fontsize" style="font-size: 11px; padding: 5px 0;">Round</div>
                                                </div>
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="shape" data-value="Rectangle">
                                                    <img src="{{ asset('assets/img/icon/rectangle.png') }}" class="framsize" >
                                                    <div class="fontsize" style="font-size: 11px; padding: 5px 0;">Rectangle</div>
                                                </div>
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="shape" data-value="Aviator">
                                                    <img src="{{ asset('assets/img/icon/aviator.png') }}" class="framsize" >
                                                    <div class="fontsize" style="font-size: 11px; padding: 5px 0;">Aviator</div>
                                                </div>
                                                <div class="frame-type text-center border rounded-3 flex-fill" style="cursor:pointer;" data-filter="shape" data-value="Cat Eye">
                                                    <img src="{{ asset('assets/img/icon/cateye.png') }}" class="framsize" >
                                                    <div class="fontsize" style="font-size: 11px; padding: 5px 0;">Cat Eye</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Frame Color -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseColor">
                                            Frame Color
                                        </button>
                                    </h2>
                                    <div id="collapseColor" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="color_0" data-filter="color" data-value="Black">
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="color_0">
                                                        <span class="color-dot border" style="background:#000000; width: 16px; height: 16px; border-radius: 50%; display: inline-block;"></span> 
                                                        Black
                                                    </label>
                                                </div>
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="color_1" data-filter="color" data-value="Brown">
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="color_1">
                                                        <span class="color-dot border" style="background:#8B4513; width: 16px; height: 16px; border-radius: 50%; display: inline-block;"></span> 
                                                        Brown
                                                    </label>
                                                </div>
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="color_2" data-filter="color" data-value="Blue">
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="color_2">
                                                        <span class="color-dot border" style="background:#0000FF; width: 16px; height: 16px; border-radius: 50%; display: inline-block;"></span> 
                                                        Blue
                                                    </label>
                                                </div>
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="color_3" data-filter="color" data-value="Gold">
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="color_3">
                                                        <span class="color-dot border" style="background:#FFD700; width: 16px; height: 16px; border-radius: 50%; display: inline-block;"></span> 
                                                        Gold
                                                    </label>
                                                </div>
                                                <div class="form-check d-flex align-items-center gap-2">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="color_4" data-filter="color" data-value="Silver">
                                                    <label class="form-check-label d-flex align-items-center gap-2" for="color_4">
                                                        <span class="color-dot border" style="background:#C0C0C0; width: 16px; height: 16px; border-radius: 50%; display: inline-block;"></span> 
                                                        Silver
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Brands -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseBrand">
                                            Brands
                                        </button>
                                    </h2>
                                    <div id="collapseBrand" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox" type="checkbox" id="brand_1" data-filter="brand" data-value="Ray-Ban">
                                                <label class="form-check-label" for="brand_1">Ray-Ban</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox" type="checkbox" id="brand_2" data-filter="brand" data-value="Oakley">
                                                <label class="form-check-label" for="brand_2">Oakley</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox" type="checkbox" id="brand_3" data-filter="brand" data-value="Vincent Chase">
                                                <label class="form-check-label" for="brand_3">Vincent Chase</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox" type="checkbox" id="brand_4" data-filter="brand" data-value="John Jacobs">
                                                <label class="form-check-label" for="brand_4">John Jacobs</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox" type="checkbox" id="brand_5" data-filter="brand" data-value="Lenskart Air">
                                                <label class="form-check-label" for="brand_5">Lenskart Air</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Frame Size -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseSize">
                                            Frame Size
                                        </button>
                                    </h2>
                                    <div id="collapseSize" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="size_0" data-filter="size" data-value="Small">
                                                    <label class="form-check-label" for="size_0">Small</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="size_1" data-filter="size" data-value="Medium">
                                                    <label class="form-check-label" for="size_1">Medium</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="size_2" data-filter="size" data-value="Large">
                                                    <label class="form-check-label" for="size_2">Large</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Material -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseMaterial">
                                            Material
                                        </button>
                                    </h2>
                                    <div id="collapseMaterial" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="d-flex flex-column gap-2">
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="mat_0" data-filter="material" data-value="Metal">
                                                    <label class="form-check-label" for="mat_0">Metal</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="mat_1" data-filter="material" data-value="Acetate">
                                                    <label class="form-check-label" for="mat_1">Acetate</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input filter-checkbox" type="checkbox" id="mat_2" data-filter="material" data-value="TR90">
                                                    <label class="form-check-label" for="mat_2">TR90</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrice">
                                            Price Range
                                        </button>
                                    </h2>
                                    <div id="collapsePrice" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body px-0">
                                            <div class="form-check">
                                                <input class="form-check-input filter-radio" type="radio" name="price_range" id="price1" data-filter="price_range" data-value="under_1000">
                                                <label class="form-check-label" for="price1">Under ₹1000</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-radio" type="radio" name="price_range" id="price2" data-filter="price_range" data-value="under_2000">
                                                <label class="form-check-label" for="price2">Under ₹2000</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input filter-radio" type="radio" name="price_range" id="price3" data-filter="price_range" data-value="under_5000">
                                                <label class="form-check-label" for="price3">Under ₹5000</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Collections -->
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent shadow-none "
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseColl">
                                            Collections
                                        </button>
                                    </h2>
                                    <div id="collapseColl" class="accordion-collapse collapse"
                                        data-bs-parent="#filterAccordion">
                                        <div class="accordion-body pt-0 pb-2 ">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="coll_1">
                                                <label class="form-check-label" for="coll_1">Summer Collection</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="coll_2">
                                                <label class="form-check-label" for="coll_2">Winter Collection</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="coll_3">
                                                <label class="form-check-label" for="coll_3">Classic Collection</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                </div>
    
                <div class="modal-footer">

                    <a href="{{ route('products') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
    
                    {{-- <button class="btn btn-dark">
                        Apply Filters
                    </button> --}}
    
                </div>
    
            </div>
        </div>
    </div>
    
 
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Heart toggle logic
            const wishlistIcons = document.querySelectorAll('.wishlist-btn');
            wishlistIcons.forEach(icon => {
                icon.addEventListener('click', function(e) {
                    e.preventDefault();
                    icon.classList.toggle('active');
                    const heart = icon.querySelector('i');
                    if (heart) {
                        heart.classList.toggle('bi-heart');
                        heart.classList.toggle('bi-heart-fill');
                    }
                });
            });

            // AJAX Filter Logic
            let currentSort = '';

            function applyFilters(page = 1) {
                const params = new URLSearchParams();
                
                // 1. Frame Type
                const selectedFrameTypes = [];
                document.querySelectorAll('.frame-type[data-filter="frame_type"].active-filter').forEach(el => {
                    selectedFrameTypes.push(el.getAttribute('data-value'));
                });
                if (selectedFrameTypes.length > 0) {
                    params.append('frame_type', selectedFrameTypes[0]);
                }
                
                // 2. Shape
                const selectedShapes = [];
                document.querySelectorAll('.frame-type[data-filter="shape"].active-filter').forEach(el => {
                    selectedShapes.push(el.getAttribute('data-value'));
                });
                if (selectedShapes.length > 0) {
                    params.append('shape', selectedShapes[0]);
                }
                
                // 3. Color
                const checkedColors = [];
                document.querySelectorAll('.filter-checkbox[data-filter="color"]:checked').forEach(el => {
                    checkedColors.push(el.getAttribute('data-value'));
                });
                if (checkedColors.length > 0) {
                    params.append('color', checkedColors[0]);
                }
                
                // 4. Brand
                const checkedBrands = [];
                document.querySelectorAll('.filter-checkbox[data-filter="brand"]:checked').forEach(el => {
                    checkedBrands.push(el.getAttribute('data-value'));
                });
                if (checkedBrands.length > 0) {
                    params.append('brand', checkedBrands[0]);
                }
                
                // 5. Size
                const checkedSizes = [];
                document.querySelectorAll('.filter-checkbox[data-filter="size"]:checked').forEach(el => {
                    checkedSizes.push(el.getAttribute('data-value'));
                });
                if (checkedSizes.length > 0) {
                    params.append('size', checkedSizes[0]);
                }
                
                // 6. Material
                const checkedMaterials = [];
                document.querySelectorAll('.filter-checkbox[data-filter="material"]:checked').forEach(el => {
                    checkedMaterials.push(el.getAttribute('data-value'));
                });
                if (checkedMaterials.length > 0) {
                    params.append('material', checkedMaterials[0]);
                }
                  // 7. Age Group
                const checkedAges = [];
                document.querySelectorAll('.filter-checkbox[data-filter="age"]:checked').forEach(el => {
                    checkedAges.push(el.getAttribute('data-value'));
                });
                if (checkedAges.length > 0) {
                    params.append('age', checkedAges[0]);
                }
                
                // 8. Occasion
                const checkedOccasions = [];
                document.querySelectorAll('.filter-checkbox[data-filter="occasion"]:checked').forEach(el => {
                    checkedOccasions.push(el.getAttribute('data-value'));
                });
                if (checkedOccasions.length > 0) {
                    params.append('occasion', checkedOccasions[0]);
                }
                
                // 9. Face Shape
                const checkedFaceShapes = [];
                document.querySelectorAll('.filter-checkbox[data-filter="face_shape"]:checked').forEach(el => {
                    checkedFaceShapes.push(el.getAttribute('data-value'));
                });
                if (checkedFaceShapes.length > 0) {
                    params.append('face_shape', checkedFaceShapes[0]);
                }
                // 10. Sunglass Colour
                const checkedSunglassColours = [];
                document.querySelectorAll('.filter-checkbox[data-filter="sunglass_colour"]:checked').forEach(el => {
                    checkedSunglassColours.push(el.getAttribute('data-value'));
                });
                if (checkedSunglassColours.length > 0) {
                    params.append('sunglass_colour', checkedSunglassColours[0]);
                }
                // 7. Price Range
                const checkedPrice = document.querySelector('.filter-radio[data-filter="price_range"]:checked');
                if (checkedPrice) {
                    params.append('price_range', checkedPrice.getAttribute('data-value'));
                }
                
                // 8. Search Input
                const searchInput = document.getElementById('page-search-input');
                if (searchInput && searchInput.value) {
                    params.append('search', searchInput.value);
                }
                
                // 9. Category from url
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('category')) {
                    params.append('category', urlParams.get('category'));
                }
                
                // 10. Sort
                if (currentSort) {
                    params.append('sort', currentSort);
                }
                
                // Page
                if (page > 1) {
                    params.append('page', page);
                }
                
                const fetchUrl = "{{ route('products') }}?" + params.toString();
                
                fetch(fetchUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.text())
                .then(html => {
                    document.getElementById('product-grid-container').innerHTML = html;
                    window.history.pushState({}, '', fetchUrl);
                    bindPaginationClickEvents();
                })
                .catch(err => console.error(err));
            }

            function bindPaginationClickEvents() {
                document.querySelectorAll('.modern-pagination a.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const href = this.getAttribute('href');
                        if (href && href !== '#') {
                            const url = new URL(href);
                            const page = url.searchParams.get('page') || 1;
                            applyFilters(page);
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    });
                });
            }

            // Sync Checkboxes/Radios and Trigger Apply
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('filter-checkbox') || e.target.classList.contains('filter-radio')) {
                    const filter = e.target.getAttribute('data-filter');
                    const value = e.target.getAttribute('data-value');
                    const checked = e.target.checked;
                    
                    if (e.target.classList.contains('filter-checkbox')) {
                        document.querySelectorAll(`.filter-checkbox[data-filter="${filter}"][data-value="${value}"]`).forEach(el => {
                            el.checked = checked;
                        });
                    } else {
                        document.querySelectorAll(`.filter-radio[data-filter="${filter}"][data-value="${value}"]`).forEach(el => {
                            el.checked = checked;
                        });
                    }
                    applyFilters();
                }
            });

            // Frame Type / Shape div selection
            document.addEventListener('click', function(e) {
                const frameTypeDiv = e.target.closest('.frame-type[data-filter]');
                if (frameTypeDiv) {
                    const filter = frameTypeDiv.getAttribute('data-filter');
                    const value = frameTypeDiv.getAttribute('data-value');
                    
                    // Toggle active-filter class on the clicked one
                    frameTypeDiv.classList.toggle('active-filter');
                    const isActive = frameTypeDiv.classList.contains('active-filter');
                    
                    // Sync active state to other panel (mobile/desktop)
                    document.querySelectorAll(`.frame-type[data-filter="${filter}"][data-value="${value}"]`).forEach(el => {
                        if (isActive) {
                            el.classList.add('active-filter');
                        } else {
                            el.classList.remove('active-filter');
                        }
                    });
                    
                    applyFilters();
                }
            });

            // Search Bar Input Debounce
            const searchInput = document.getElementById('page-search-input');
            if (searchInput) {
                let debounceTimer;
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        applyFilters();
                    }, 400);
                });
            }
            const searchForm = document.getElementById('page-search-form');
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    applyFilters();
                });
            }

            // Sort logic
            document.querySelectorAll('.sort-option').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentSort = this.getAttribute('data-sort');
                    document.getElementById('sort-dropdown-btn').textContent = this.textContent;
                    applyFilters();
                });
            });

            // Reset filters logic
            function resetAllFilters() {
                document.querySelectorAll('.frame-type.active-filter').forEach(el => {
                    el.classList.remove('active-filter');
                });
                document.querySelectorAll('.filter-checkbox:checked').forEach(el => {
                    el.checked = false;
                });
                document.querySelectorAll('.filter-radio:checked').forEach(el => {
                    el.checked = false;
                });
                if (searchInput) {
                    searchInput.value = '';
                }
                currentSort = '';
                document.getElementById('sort-dropdown-btn').textContent = 'Sort By';
                applyFilters();
            }

            const resetBtn = document.getElementById('btn-reset-filters');
            if (resetBtn) resetBtn.addEventListener('click', resetAllFilters);

            const resetBtnMobile = document.getElementById('btn-reset-filters-mobile');
            if (resetBtnMobile) resetBtnMobile.addEventListener('click', resetAllFilters);

            // Initial pagination bindings
            bindPaginationClickEvents();
        });
    </script>
@endsection
