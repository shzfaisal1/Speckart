@php
    $navCategories = \App\Models\Category::where('is_active', 1)->get();
    $navFrameTypes = \App\Models\FrameType::where('status', 1)->get();
    $navCollections = \App\Models\Collection::where('is_active', 1)->get();
    $navBrands = \App\Models\Brand::where('status', 1)->get();
@endphp
<!-- navbar -->
<section class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="top-menu">
                    <a href="{{ route('home')}}">
                    <div class="logo">
                        <img src="{{asset('assets/img/logo/Specskart-logo-png.png')}}" alt="">
                    </div>
                    </a>
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="search glasses, lenses, specs..">
                            <button class="btn btn-success" type="submit">Search</button>
                        </div>
                    </div>
                    <div class="top-menu-list">
                        <ul>
                            <li>
                                <a href="">
                                    <p>
                                        <img src="{{asset('assets/img/icon/Track-Order.png')}}" alt="">
                                    </p>
                                    <p>Track Order</p>
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    <p>
                                        <img src="{{asset('assets/img/icon/Wishlist.png')}}" alt="">
                                    </p>
                                    <p>Wishlist</p>
                                </a>
                            </li>
                            <li>
                                <a href="">
                                    <p>
                                        <img src="{{asset('assets/img/icon/My-Cart.png')}}" alt="">
                                    </p>
                                    <p>My Cart</p>
                                </a>
                            </li>
                            <li class="dropdown">
                                <a href="">
                                    <p>
                                        <img src="{{asset('assets/img/icon/More.png')}}" alt="">
                                    </p>
                                    <p>More</p>
                                </a>
                                <ul class="dropdown-nav">
					                <li><a href="">Lawn Care</a></li>
					                <li><a href="">Walling &amp; Fencing</a></li>
					                <li><a href="">Landscape design</a></li>
					                <li><a href="">Grounds Maintenance</a></li>
				                </ul>
			                </li>
		                
                          
                            <li class="dropdown">
                                <a href="{{route('profile')}}">
                                    <p>
                                        <img src="{{asset('assets/img/icon/Signup.png')}}" alt="">
                                    </p>
                                    <p>{{ Auth::user()->name ?: 'My Profile' }}</p>
                                </a>
                                <ul class="dropdown-nav">
					                <li><a href="{{route('profile')}}">Profile</a></li>
					                <li><a href="{{route('my_order')}}">My Order</a></li>
					                <li><a href="{{route('my_address')}}">My Addresses</a></li>
					                <li><a href="{{route('my_prescription')}}">My Prescription</a></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display:none;">
                                            @csrf
                                        </form>
                                        <a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit();">Logout</a>
                                    </li>
				                </ul>
                            </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <nav>
        <div class="wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- <div class="logo"><a href="#"> Mega Menu</a></div> -->
                        <input type="radio" name="slider" id="menu-btn">
                        <input type="radio" name="slider" id="close-btn">
                        @php
                            $menuItems = [
                                [
                                    'label' => 'Eyeglasses',
                                    'genders' => ['Men', 'Women', 'Kids'],
                                    'category' => null,
                                ],
                                [
                                    'label' => 'Screen Glasses',
                                    'genders' => ['Men', 'Women', 'Kids'],
                                    'category' => 'screen-glasses',
                                ],
                                [
                                    'label' => 'Sunglasses',
                                    'genders' => ['Men', 'Women'],
                                    'category' => 'sunglasses',
                                ],
                                [
                                    'label' => 'Kids Glasses',
                                    'genders' => ['Kids'],
                                    'category' => null,
                                ],
                                [
                                    'label' => 'Contact Lenses',
                                    'href' => url('/contact-lenses'),
                                    'link' => true,
                                ],
                            ];
                        @endphp
                        <ul class="nav-links">
                            <label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>
                            @foreach($menuItems as $menu)
                                @if(!empty($menu['link']))
                                    <li><a href="{{ $menu['href'] }}" class="desktop-item">{{ $menu['label'] }}</a></li>
                                @else
                                    @php
                                        $menuId = \Illuminate\Support\Str::slug($menu['label']);
                                    @endphp
                                    <li>
                                        @php
                                            $primaryGender = $menu['genders'][0];
                                            $primaryRoute = ['gender' => strtolower($primaryGender)];
                                            if (!empty($menu['category'])) {
                                                $primaryRoute['category'] = $menu['category'];
                                            }
                                        @endphp
                                        <a href="{{ route('products', $primaryRoute) }}" class="desktop-item">{{ $menu['label'] }} +</a>
                                        <input type="checkbox" id="showMega{{ $menuId }}">
                                        <label for="showMega{{ $menuId }}" class="mobile-item">{{ $menu['label'] }}</label>
                                        <div class="mega-box">
                                            <div class="content1">
                                                <div class="tabs">
                                                    <ul id="tabs-nav">
                                                        @foreach($menu['genders'] as $gender)
                                                            @php $gId = \Illuminate\Support\Str::slug($menu['label'] . '-' . $gender); @endphp
                                                            <li>
                                                                <a href="#{{ $gId }}">
                                                                    <img src="{{ asset('assets/img/icon/specs-' . strtolower($gender) . '.png') }}" alt="specs-{{ strtolower($gender) }}">
                                                                    {{ $gender }}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    <div id="tabs-content">
                                                        @foreach($menu['genders'] as $gender)
                                                            @php
                                                                $gId = \Illuminate\Support\Str::slug($menu['label'] . '-' . $gender);
                                                                $genderFilters = ['gender' => strtolower($gender)];
                                                                if (!empty($menu['category'])) {
                                                                    $genderFilters['category'] = $menu['category'];
                                                                }
                                                            @endphp
                                                            <div id="{{ $gId }}" class="tab-content">
                                                                <div class="row">
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Our Top Picks</h4>
                                                                            <li><a href="{{ route('products', array_merge($genderFilters, ['tag' => 'new-arrival'])) }}">New Arrivals</a></li>
                                                                            <li><a href="{{ route('products', array_merge($genderFilters, ['tag' => 'best-seller'])) }}">Best Seller</a></li>
                                                                            <li><a href="{{ route('products', array_merge($genderFilters, ['tag' => 'trending'])) }}">Trending</a></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Frame Type</h4>
                                                                            @foreach($navFrameTypes as $type)
                                                                                @php $typeFilters = array_merge($genderFilters, ['frame_type' => $type->slug]); @endphp
                                                                                <li><a href="{{ route('products', $typeFilters) }}">{{ $type->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Collection</h4>
                                                                            @foreach($navCollections as $collection)
                                                                                @php $collectionFilters = array_merge($genderFilters, ['collection' => $collection->slug]); @endphp
                                                                                <li><a href="{{ route('products', $collectionFilters) }}">{{ $collection->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-lg-3">
                                                                        <ul>
                                                                            <h4>Brands</h4>
                                                                            @foreach($navBrands as $brand)
                                                                                @php $brandFilters = array_merge($genderFilters, ['brand' => $brand->slug]); @endphp
                                                                                <li><a href="{{ route('products', $brandFilters) }}">{{ $brand->name }}</a></li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <label for="menu-btn" class="btn menu-btn"><i class="fas fa-bars"></i></label>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</section>
<!-- end navbar -->