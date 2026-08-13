@extends('web.layout.master')
@section('content')
    {{-- start first one --}}
    <section class="details">
        <div class="container">
            <div class="row">
                <div class="col-md-8 breadcrumbs-section">
                    <ul id="breadcrumbs">
                        <li><a href="#">Eyewear</a></li>
                        <li><a href="#">Sunglasses</a></li>
                        <li><a href="#">Brands</a></li>
                        <li>Shopping Cart</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <div class="wishshre position-relative d-flex align-items-center gap-3">
                        <div class="wishlis">
                            <i class="bi bi-heart"></i>
                        </div>

                        <div class="share position-relative">
                            <i class="bi bi-share"></i>
                            <div class="share-options">
                                @php
                                    $currentUrl = request()->url();
                                    $shareUrl = urlencode($currentUrl);
                                @endphp
                                <a href="https://api.whatsapp.com/send?text={{ $shareUrl }}" target="_blank" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" title="Facebook"><i class="bi bi-facebook"></i></a>
                                <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}" target="_blank" title="Twitter"><i class="bi bi-twitter-x"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- end first one --}}


    {{-- start 2 360 degrees --}}
 
    <section class="degree">
        <!-- Product Details Section -->
        <div class="container">
            <div class="row ">
                <!-- Left: Product Image & Thumbnails -->
                <div class="col-md-6 col-12">
                    <div class="product-box text-center">
                        <div class="position-relative main-img-area">
                            <img src="{{ asset('assets/img/bg/Sunglasses1.png') }}" class="main-image img-fluid"
                                alt="Product">
                            <button class="btn-360">360 VIEW</button>
                        </div>




                    </div>
                    <div class="thumb-section  d-flex align-items-center justify-content-center position-relative">
                        <button class="arrow-btn left"><i class="bi bi-chevron-left"></i></button>

                       <div class="thumbs-container">
                            <div class="thumbs d-flex">
                                <img src="{{ asset('assets/img/bg/Sunglasses1.png') }}" class="thumb active" alt="">
                                <img src="{{ asset('assets/img/bg/Sunglasses2.png') }}" class="thumb" alt="">
                                <img src="{{ asset('assets/img/bg/Sunglasses3.png') }}" class="thumb" alt="">
                                <img src="{{ asset('assets/img/bg/Sunglasses4.png') }}" class="thumb" alt="">
                            </div>
                        </div>

                        <button class="arrow-btn right"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>


                <!-- Right: Product Info -->
                <div class="col-md-6">
              
                    <p class="semibold mb-1">Ray-Ban Aviator Classic</p>
                    <h4 class="fbold mb-3">Timeless aviator style with premium UV protection and gold-toned metal alloy frames.</h4>

                    <!-- Price -->
                    <div class="back">
                        <span class="fastrack">₹4,999</span>
                        <br>
                        <span class="tmuted text-decoration-line-through ms-2">₹7,999</span>
                        <span class="tmuted">38% OFF</span>
                    </div>

                    <!-- Size -->
                    <div class="product-options mt-4">
                        <!-- Size Section -->
                        <div class="option-group mb-4 d-flex align-items-center flex-wrap gap-2">
                            <label class="option-label  mb-0">Size :</label>
                            <div class="size-options d-flex gap-2">
                                <button class="size-btn">Small</button>
                                <button class="size-btn active">Medium</button>
                                <button class="size-btn">Large</button>
                            </div>
                        </div>

                        <!-- Colour Section -->
                        <div class="option-group d-flex align-items-center flex-wrap gap-2">
                            <label class="option-label  mb-0">Colour :</label>
                            <div class="color-options d-flex gap-2">
                                <div class="color-box active" style="background-color: #FFD700;"></div>
                                <div class="color-box" style="background-color: #C0C0C0;"></div>
                                <div class="color-box" style="background-color: #000000;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Product Type Section ── --}}
                    <div class="product-type-section mt-4" id="product-type-section">
                        <p class="option-label mb-2">Product Type :</p>
                        <div class="product-type-tabs d-flex flex-wrap gap-2">
                            <div class="ptype-tab active" data-type-id="1" data-has-power="1" onclick="selectProductType(this)">
                                <span class="ptype-name">Eyeglasses</span>
                                <span class="ptype-sub">With lenses</span>
                            </div>
                            <div class="ptype-tab" data-type-id="2" data-has-power="0" onclick="selectProductType(this)">
                                <span class="ptype-name">Sunglasses</span>
                                <span class="ptype-sub">Zero power</span>
                            </div>
                        </div>

                        {{-- Power chips ── --}}
                        <div class="power-chips-wrap mt-3" id="powers-1">
                            <p class="option-label mb-2">Select Power :</p>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="power-chip">-0.50</button>
                                <button type="button" class="power-chip">-1.00</button>
                                <button type="button" class="power-chip">-1.50</button>
                                <button type="button" class="power-chip active">0.00</button>
                                <button type="button" class="power-chip">+1.00</button>
                                <button type="button" class="power-chip">+1.50</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row mb-2 byy">
                <div class="col-md-6">
                   
                    <div class="d-flex justify-content-center flex-wrap gap-3 mt-4">
                        <button
                            id="main-action-btn"
                            class="btn btn-outline-custom active select-lenses-mode"
                            data-bs-toggle="modal"
                            data-bs-target="#lensModal"
                            data-default-text="Select Lenses"
                        >Select Lenses</button>
                        <a href="{{url('/add-power')}}"><button class="btn btn-outline-custom">Add Power</button></a>
                        <button class="btn btn-outline-custom">Try on you</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- ============================ --}}
    {{-- end 2 360 degrees --}}
    {{-- ============================ --}}

    {{-- ============================ --}}
    {{-- start hide and show   start 3 --}}
    {{-- ============================ --}}
    <div class="tech">
        <div class="container my-4">

            <!-- Technical Information -->
            <div class="accordion" id="productAccordion">
                <div class="accordion-item  border-0 mb-3 rounded-3">
                    <h2 class="accordion-header" id="headingOne">

                        <button class="accordion-button " type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Technical information
                        </button>
                    </h2>


                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                        data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="info-row"><span class="info-label">Product id</span><span
                                            class="info-colon">:</span><span class="info-value">1001</span></div>
                                    <div class="info-row"><span class="info-label">Model No.</span><span
                                            class="info-colon">:</span><span class="info-value">RB3025</span></div>
                                    <div class="info-row"><span class="info-label">Frame Size</span><span
                                            class="info-colon">:</span><span class="info-value">Medium</span></div>
                                    <div class="info-row"><span class="info-label">Frame Width</span><span
                                            class="info-colon">:</span><span class="info-value">140 mm</span></div>
                                    <div class="info-row"><span class="info-label">Frame Dimensions</span><span
                                            class="info-colon">:</span><span class="info-value">58-14-135</span></div>
                                    <div class="info-row"><span class="info-label">Frame Colour</span><span
                                            class="info-colon">:</span><span class="info-value">Gold</span></div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="info-row"><span class="info-label">Glass Color</span><span
                                            class="info-colon">:</span><span class="info-value">Green</span></div>
                                    <div class="info-row"><span class="info-label">Weight</span><span
                                            class="info-colon">:</span><span class="info-value">28 gm</span></div>
                                    <div class="info-row"><span class="info-label">Weight Group</span><span
                                            class="info-colon">:</span><span class="info-value">Light</span></div>
                                    <div class="info-row"><span class="info-label">Material</span><span
                                            class="info-colon">:</span><span class="info-value">Metal</span></div>
                                    <div class="info-row"><span class="info-label">Frame Material</span><span
                                            class="info-colon">:</span><span class="info-value">Metal Alloy</span></div>
                                    <div class="info-row"><span class="info-label">Temple Material</span><span
                                            class="info-colon">:</span><span class="info-value">Metal Alloy</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visit Nearby Store -->
                <div class="accordion-item  border-0 mb-3 rounded-3">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Visit Nearby Store
                        </button>
                    </h2>

                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">

                                <!-- Store 1 -->
                                <div class="col-md-6 col-12">
                                    <div class="store-card p-3 rounded-4 shadow-sm">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-4">
                                                <img src="{{ asset('assets/img/productimg/grand.png') }}"
                                                    class="rounded-3 img-fluid" alt="Store Image">
                                            </div>
                                            <div class="col-8">
                                                <h6 class="fw-bold">Grand Central, Seawoods</h6>
                                                <p class="small text-muted">
                                                    P 80, Prozone Mall, Unit No G 53, Midc Api Road,<br>
                                                    Chikalthana, Near Mcdonalds, Aurangabad, Maharashtra, 431210
                                                </p>
                                                <p class="phon"><i class="bi bi-telephone text-success me-1"></i> +91
                                                    7428891313</p>
                                                <button class="btn btn-map mt-1">Open Google Map</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Store 2 -->
                                <div class="col-md-6 col-12">
                                    <div class="store-card p-3 rounded-4 shadow-sm">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-4">
                                                <img src="{{ asset('assets/img/productimg/image@2x.png') }}"
                                                    class="rounded-3 img-fluid" alt="Store Image">
                                            </div>
                                            <div class="col-8">
                                                <h6 class="fw-bold">Inorbit Mall, Vashi</h6>
                                                <p class="mb-2 small text-muted">
                                                    P 80, Prozone Mall, Unit No G 53, Midc Api Road,<br>
                                                    Chikalthana, Near Mcdonalds, Aurangabad, Maharashtra, 431210
                                                </p>
                                                <p class="phon"><i class="bi bi-telephone text-success me-1"></i> +91
                                                    7428891313</p>
                                                <button class="btn btn-map mt-1">Open Google Map</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Check Delivery Options -->
                <div class="accordion-item  border-0 mb-3 rounded-3">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Check Delivery Options
                        </button>
                    </h2>

                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <!-- Store 2 -->
                                <div class="col-md-6 col-12">

                                    <div class="pincode-box mt-3">
                                        <input type="text" placeholder="Enter pin code" />
                                        <button type="button">CHECK</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Reviews (126) -->
                <div class="accordion-item  border-0 rounded-3">
                    <h2 class="accordion-header" id="headingfour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapsefour" aria-expanded="false" aria-controls="collapsefour">
                            Reviews (126)
                        </button>
                    </h2>

                    <div id="collapsefour" class="accordion-collapse collapse" aria-labelledby="headingfour"
                        data-bs-parent="#productAccordion">
                        <div class="accordion-body">
                            <div class="testimonial-section container">
                                <div class="testimonial-slider">
                                    <div class="testimonial-track">
                                        <!-- ===== Cards (duplicated for seamless loop) ===== -->
                                        <div class="testimonial-card">
                                            <div class="stars">★★★★☆</div>
                                            <p>SpeckArts has been an absolute game changer for our business! Their design
                                                team delivered stunning visuals that perfectly captured our brand’s essence!
                                            </p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Sarah L.</span>
                                                <span>24 March 2025</span>
                                            </div>
                                        </div>

                                        <div class="testimonial-card">
                                            <div class="stars">★★★☆☆</div>
                                            <p>I recently purchased from SpeckArts.com, and the shopping experience was
                                                seamless. The website was easy to navigate, and my order arrived ahead of
                                                schedule.</p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Ashok D.</span>
                                                <span>22 March 2025</span>
                                            </div>
                                        </div>

                                        <div class="testimonial-card">
                                            <div class="stars">★★★★☆</div>
                                            <p>I hired SpeckArts for a custom digital art piece for my home, and I’m
                                                absolutely thrilled with the final product! The artist was professional and
                                                passionate.</p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Maria W.</span>
                                                <span>19 March 2025</span>
                                            </div>
                                        </div>

                                        <div class="testimonial-card">
                                            <div class="stars">★★★★★</div>
                                            <p>Excellent service! The communication was great throughout, and the final
                                                design exceeded expectations. Highly recommend SpeckArts.</p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Rahul K.</span>
                                                <span>17 March 2025</span>
                                            </div>
                                        </div>

                                        <!-- Duplicate same cards for seamless infinite loop -->
                                        <div class="testimonial-card">
                                            <div class="stars">★★★★☆</div>
                                            <p>SpeckArts has been an absolute game changer for our business! Their design
                                                team delivered stunning visuals that perfectly captured our brand’s essence!
                                            </p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Sarah L.</span>
                                                <span>24 March 2025</span>
                                            </div>
                                        </div>

                                        <div class="testimonial-card">
                                            <div class="stars">★★★☆☆</div>
                                            <p>I recently purchased from SpeckArts.com, and the shopping experience was
                                                seamless. The website was easy to navigate, and my order arrived ahead of
                                                schedule.</p>
                                            <div class="testimonial-footer">
                                                <span class="testimonial-name">Ashok D.</span>
                                                <span>22 March 2025</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- ============================ --}}
    {{-- end hide and show   --}}
    {{-- ============================ --}}

    <!-- ══════════════════════════════════════════
         SELECT LENSES MODAL
    ══════════════════════════════════════════ -->
    <div class="modal fade" id="lensModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" id="lens-modal-dialog">
            <div class="modal-content lens-popup">

                <!-- Header -->
                <div class="lens-popup-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <button id="goPrev" class="lens-prev-btn" style="visibility:hidden;">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </button>
                        <div class="lens-steps-indicator">
                            <span class="step-dot active" id="dot1"></span>
                            <span class="step-dot" id="dot2"></span>
                            <span class="step-dot" id="dot3"></span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <!-- Body -->
                <div class="lens-popup-body">

                    <!-- ── Step 1: Add Lenses? ── -->
                    <div id="step1" class="lens-step">
                        <div class="lens-step-icon">
                            <img src="{{ asset('assets/img/productimg/mask1.png') }}" alt="Lens">
                        </div>
                        <h4 class="lens-step-title">Want to add Lenses?</h4>
                        <p class="lens-step-sub">Choose lenses with the frame or buy just the frame</p>
                        <div class="lens-step1-btns">
                            <button class="lens-main-btn" id="goStep2">
                                <i class="bi bi-eye me-2"></i>Buy with Lenses
                            </button>
                            <button class="lens-frame-btn" data-bs-dismiss="modal">
                                <i class="bi bi-border-style me-2"></i>Only the Frame
                            </button>
                        </div>
                    </div>

                    <!-- ── Step 2: Choose Lens Type ── -->
                    <div id="step2" class="lens-step" style="display:none;">
                        <h4 class="lens-step-title">Choose Lens Type</h4>
                        <p class="lens-step-sub">Pick the lens type that suits your need</p>
                        <div class="lens-type-grid">
                            <div class="lens-type-card" id="singleVision" onclick="selectLensType(this, 3)">
                                <div class="lens-type-icon">
                                    <img src="{{ asset('assets/img/productimg/chooselens1.png') }}" alt="Single Vision">
                                </div>
                                <p class="lens-type-name">Single Vision</p>
                                <p class="lens-type-desc">For distance or reading</p>
                            </div>
                            <div class="lens-type-card" onclick="selectLensType(this, 3)">
                                <div class="lens-type-icon">
                                    <img src="{{ asset('assets/img/productimg/chooselens2.png') }}" alt="Computer">
                                </div>
                                <p class="lens-type-name">Computer</p>
                                <p class="lens-type-desc">Anti-blue light</p>
                            </div>
                            <div class="lens-type-card" onclick="selectLensType(this, 3)">
                                <div class="lens-type-icon">
                                    <img src="{{ asset('assets/img/productimg/chooselens3.png') }}" alt="Bifocal">
                                </div>
                                <p class="lens-type-name">Bifocal</p>
                                <p class="lens-type-desc">Near + distance both</p>
                            </div>
                            <div class="lens-type-card" onclick="selectLensType(this, 3)">
                                <div class="lens-type-icon">
                                    <img src="{{ asset('assets/img/productimg/chooselens4.png') }}" alt="Only Frame">
                                </div>
                                <p class="lens-type-name">Only Frame</p>
                                <p class="lens-type-desc">No lens included</p>
                            </div>
                        </div>
                    </div>

                    <!-- ── Step 3: Choose Package ── -->
                    <div id="step3" class="lens-step" style="display:none;">
                        <h4 class="lens-step-title">Choose Lens Package</h4>
                        <p class="lens-step-sub">Select the best lens coating for you</p>
                        <div class="lens-package-list">
                            <a href="{{ url('/shopping-cart') }}" class="text-decoration-none">
                                <div class="lens-package-card">
                                    <div class="lens-pkg-left">
                                        <div class="lens-pkg-badge">Most Popular</div>
                                        <p class="lens-pkg-name">Anti Glare Premium</p>
                                        <p class="lens-pkg-features">Anti-reflection · UV protection · Scratch resistant</p>
                                    </div>
                                    <div class="lens-pkg-right">
                                        <span class="lens-pkg-price">₹1,500</span>
                                        <button class="lens-pkg-btn"><i class="bi bi-bag-plus"></i></button>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ url('/shopping-cart') }}" class="text-decoration-none">
                                <div class="lens-package-card">
                                    <div class="lens-pkg-left">
                                        <p class="lens-pkg-name">Blue Cut + Anti Glare</p>
                                        <p class="lens-pkg-features">Blue light block · Anti-reflection · UV400</p>
                                    </div>
                                    <div class="lens-pkg-right">
                                        <span class="lens-pkg-price">₹2,000</span>
                                        <button class="lens-pkg-btn"><i class="bi bi-bag-plus"></i></button>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ url('/shopping-cart') }}" class="text-decoration-none">
                                <div class="lens-package-card">
                                    <div class="lens-pkg-left">
                                        <p class="lens-pkg-name">Photochromic (Transitions)</p>
                                        <p class="lens-pkg-features">Auto-darkens in sunlight · UV protection</p>
                                    </div>
                                    <div class="lens-pkg-right">
                                        <span class="lens-pkg-price">₹2,800</span>
                                        <button class="lens-pkg-btn"><i class="bi bi-bag-plus"></i></button>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ url('/shopping-cart') }}" class="text-decoration-none">
                                <div class="lens-package-card">
                                    <div class="lens-pkg-left">
                                        <p class="lens-pkg-name">Basic Clear Lens</p>
                                        <p class="lens-pkg-features">Standard optics · Lightweight</p>
                                    </div>
                                    <div class="lens-pkg-right">
                                        <span class="lens-pkg-price">₹900</span>
                                        <button class="lens-pkg-btn"><i class="bi bi-bag-plus"></i></button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                </div><!-- /lens-popup-body -->
            </div>
        </div>
    </div>
    <!-- ── end lensModal ── -->



    <script>
        const variantImages = {};
        const defaultImages = [];
        let index = 0;
        const visibleCount = 3;

        function initializeThumbnails() {
            const thumbs = document.querySelectorAll('.thumb');
            const mainImg = document.querySelector('.main-image');
            
            if (thumbs.length === 0) return;
            
            // Ensure first thumbnail is marked active if none is active
            if (!document.querySelector('.thumb.active')) {
                thumbs[0].classList.add('active');
            }
            
            let activeThumb = document.querySelector('.thumb.active') || thumbs[0];

            thumbs.forEach(thumb => {
                thumb.addEventListener('mouseenter', () => {
                    if (mainImg) mainImg.src = thumb.src;
                });
                thumb.addEventListener('mouseleave', () => {
                    const currentActive = document.querySelector('.thumb.active') || activeThumb;
                    if (mainImg && currentActive) mainImg.src = currentActive.src;
                });
                thumb.addEventListener('click', () => {
                    thumbs.forEach(t => t.classList.remove('active'));
                    thumb.classList.add('active');
                    activeThumb = thumb;
                    if (mainImg) mainImg.src = thumb.src;
                });
            });
        }

        function updateSlider() {
            const thumbs = document.querySelectorAll('.thumb');
            const thumbsContainer = document.querySelector('.thumbs');
            if (thumbs.length > 0 && thumbsContainer) {
                const thumbWidth = thumbs[0].offsetWidth + 12; // width + gap
                thumbsContainer.style.transform = `translateX(-${index * thumbWidth}px)`;
            }
        }

        $(document).ready(function() {
            initializeThumbnails();

            // Handle color selection click
            $(".color-box").click(function() {
                // Toggle active class
                $(".color-box").removeClass("active");
                $(this).addClass("active");
            });

            // Scroll left/right buttons for thumbnails
            const arrowRight = document.querySelector('.arrow-btn.right');
            const arrowLeft = document.querySelector('.arrow-btn.left');
            
            if (arrowRight) {
                arrowRight.addEventListener('click', () => {
                    const thumbs = document.querySelectorAll('.thumb');
                    if (index < thumbs.length - visibleCount) {
                        index++;
                        updateSlider();
                    }
                });
            }
            
            if (arrowLeft) {
                arrowLeft.addEventListener('click', () => {
                    if (index > 0) {
                        index--;
                        updateSlider();
                    }
                });
            }
        });
    </script>

    {{-- start color and size --}}
    <script>
        const sizeBtns = document.querySelectorAll('.size-btn');
        sizeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                sizeBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });
    </script>

    {{-- end color and size --}}

    {{-- three button active start --}}
    <script>
        document.querySelectorAll('.byy .btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.byy .btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });
    </script>

    {{-- three button active  end --}}
    {{-- testimonial start --}}
    <script>
        const slider = document.querySelector('.testimonial-slider');
        let isDown = false;
        let startX;
        let scrollLeft;
        let autoScroll;
        let isHovered = false;


        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            clearInterval(autoScroll);
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });

        slider.addEventListener('mouseleave', () => {
            isDown = false;
            if (!isHovered) startAutoScroll();
        });

        slider.addEventListener('mouseup', () => {
            isDown = false;
            if (!isHovered) startAutoScroll();
        });

        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 1.5;
            slider.scrollLeft = scrollLeft - walk;
        });


        slider.addEventListener('touchstart', (e) => {
            isDown = true;
            clearInterval(autoScroll);
            startX = e.touches[0].pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });
        slider.addEventListener('touchend', () => {
            isDown = false;
            if (!isHovered) startAutoScroll();
        });
        slider.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            const x = e.touches[0].pageX - slider.offsetLeft;
            const walk = (x - startX) * 1.5;
            slider.scrollLeft = scrollLeft - walk;
        });

        // 🖱️ Hover pause
        slider.addEventListener('mouseenter', () => {
            isHovered = true;
            clearInterval(autoScroll);
        });

        slider.addEventListener('mouseleave', () => {
            isHovered = false;
            startAutoScroll();
        });


        function startAutoScroll() {
            clearInterval(autoScroll);
            autoScroll = setInterval(() => {
                if (!isHovered && !isDown) {
                    slider.scrollLeft += 1; // scroll speed


                    if (slider.scrollLeft >= slider.scrollWidth - slider.clientWidth - 2) {
                        slider.scrollLeft = 0;
                    }
                }
            }, 15); // smaller = faster
        }

        // Start
        startAutoScroll();
    </script>


    {{-- testimonial end  --}}
    {{-- heart and share button start --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ❤️ Heart icon toggle
            document.querySelectorAll(".wishlis i").forEach((heart) => {
                heart.addEventListener("click", () => {
                    if (heart.classList.contains("bi-heart")) {
                        heart.classList.replace("bi-heart", "bi-heart-fill");
                        heart.classList.add("active");
                    } else {
                        heart.classList.replace("bi-heart-fill", "bi-heart");
                        heart.classList.remove("active");
                    }
                });
            });

            // 📤 Share menu toggle
            document.querySelectorAll(".share").forEach((shareDiv) => {
                const shareIcon = shareDiv.querySelector("i.bi-share");
                const shareOptions = shareDiv.querySelector(".share-options");

                shareIcon.addEventListener("click", (e) => {
                    e.stopPropagation();

                    // Hide all other share menus first
                    document.querySelectorAll(".share-options").forEach((opt) => {
                        if (opt !== shareOptions) opt.style.display = "none";
                    });

                    // Toggle current one
                    shareOptions.style.display =
                        shareOptions.style.display === "flex" ? "none" : "flex";
                });
            });

            // ✖ Close when click outside
            document.addEventListener("click", (e) => {
                if (!e.target.closest(".share")) {
                    document.querySelectorAll(".share-options").forEach((opt) => {
                        opt.style.display = "none";
                    });
                }
            });
        });
    </script>

    {{-- popup ens  --}}
    {{-- ── Product Type + Lens Modal Styles & Logic ── --}}
    <style>
        /* ── Option label ── */
        .option-label {
            font-size: 13px; font-weight: 600; color: #444; letter-spacing: 0.3px;
        }

        /* ── Product Type pill tabs ── */
        .product-type-tabs { gap: 10px !important; }
        .ptype-tab {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; min-width: 100px; padding: 10px 14px;
            border: 1.5px solid #d0d0d0; border-radius: 50px; cursor: pointer;
            background: #fff; transition: all 0.2s ease; text-align: center;
        }
        .ptype-tab:hover { border-color: #1c3a5e; background: #f5f8ff; }
        .ptype-tab.active { border-color: #1c3a5e; box-shadow: 0 0 0 1.5px #1c3a5e; }
        .ptype-name { font-size: 13px; font-weight: 600; color: #1c2b4a; line-height: 1.3; }
        .ptype-sub  { font-size: 10px; color: #888; margin-top: 2px; line-height: 1.2; }

        /* ── Power chips ── */
        .power-chip {
            padding: 6px 14px; border: 1.5px solid #c0c0c0; border-radius: 50px;
            background: #fff; font-size: 12px; font-weight: 600; color: #333;
            cursor: pointer; transition: all 0.18s ease;
        }
        .power-chip:hover  { border-color: #1c3a5e; color: #1c3a5e; background: #f0f4ff; }
        .power-chip.active { background: #1c3a5e; color: #fff; border-color: #1c3a5e; }

        /* ══════════════════════════════
           LENS MODAL
        ══════════════════════════════ */
        .lens-popup {
            border-radius: 20px; overflow: hidden; border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
        }
        .lens-popup-header {
            padding: 16px 22px; border-bottom: 1px solid #f0f0f0; background: #fff;
        }
        .lens-prev-btn {
            border: none; background: none; font-size: 13px; font-weight: 600;
            color: #1c3a5e; cursor: pointer; padding: 0; display: flex;
            align-items: center; gap: 4px; transition: opacity 0.2s;
        }
        .lens-prev-btn:hover { opacity: 0.7; }
        .lens-steps-indicator { display: flex; gap: 6px; align-items: center; }
        .step-dot {
            width: 8px; height: 8px; border-radius: 50%; background: #ddd;
            transition: all 0.25s ease;
        }
        .step-dot.active { background: #1c3a5e; width: 22px; border-radius: 4px; }

        .lens-popup-body { padding: 30px 28px; max-height: 80vh; overflow-y: auto; }

        /* Step layout */
        .lens-step { text-align: center; animation: fadein 0.25s ease; }
        @keyframes fadein { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .lens-step-icon img { width: 90px; height: 90px; object-fit: contain; margin-bottom: 16px; }
        .lens-step-title { font-size: 20px; font-weight: 700; color: #1c2b4a; margin-bottom: 6px; }
        .lens-step-sub   { font-size: 13px; color: #888; margin-bottom: 24px; }

        /* Step 1 buttons */
        .lens-step1-btns { display: flex; flex-direction: column; gap: 12px; max-width: 320px; margin: 0 auto; }
        .lens-main-btn {
            padding: 14px; border: none; border-radius: 12px;
            background: #1c3a5e; color: #fff; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: all 0.2s ease;
        }
        .lens-main-btn:hover { background: #142d4c; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(28,58,94,0.3); }
        .lens-frame-btn {
            padding: 14px; border: 1.5px solid #1c3a5e; border-radius: 12px;
            background: #fff; color: #1c3a5e; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: all 0.2s ease;
        }
        .lens-frame-btn:hover { background: #f0f5ff; }

        /* Step 2 lens type grid */
        .lens-type-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 14px; margin-top: 10px;
        }
        @media(max-width:600px) { .lens-type-grid { grid-template-columns: repeat(2,1fr); } }
        .lens-type-card {
            border: 1.5px solid #e0e0e0; border-radius: 14px; padding: 16px 10px;
            cursor: pointer; transition: all 0.2s ease; background: #fff;
        }
        .lens-type-card:hover  { border-color: #1c3a5e; background: #f5f8ff; transform: translateY(-2px); }
        .lens-type-card.active { border-color: #1c3a5e; box-shadow: 0 0 0 1.5px #1c3a5e; }
        .lens-type-icon img { width: 56px; height: 56px; object-fit: contain; }
        .lens-type-name { font-size: 13px; font-weight: 700; color: #1c2b4a; margin: 8px 0 2px; }
        .lens-type-desc { font-size: 11px; color: #999; margin: 0; }

        /* Step 3 package cards */
        .lens-package-list { display: flex; flex-direction: column; gap: 12px; margin-top: 8px; }
        .lens-package-card {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 18px; border: 1.5px solid #eee; border-radius: 14px;
            background: #fff; transition: all 0.2s ease;
        }
        .lens-package-card:hover { border-color: #1c3a5e; background: #f8faff; transform: translateX(3px); }
        .lens-pkg-left { text-align: left; }
        .lens-pkg-badge {
            display: inline-block; background: #fff3e0; color: #e65100;
            font-size: 10px; font-weight: 700; padding: 2px 8px;
            border-radius: 50px; margin-bottom: 6px; letter-spacing: 0.5px;
        }
        .lens-pkg-name     { font-size: 14px; font-weight: 700; color: #1c2b4a; margin: 0 0 3px; }
        .lens-pkg-features { font-size: 11px; color: #999; margin: 0; }
        .lens-pkg-right { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; }
        .lens-pkg-price { font-size: 18px; font-weight: 800; color: #1c3a5e; }
        .lens-pkg-btn {
            width: 36px; height: 36px; border-radius: 50%; border: none;
            background: #1c3a5e; color: #fff; font-size: 16px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s ease;
        }
        .lens-pkg-btn:hover { background: #142d4c; transform: scale(1.1); }

        /* Select Lenses button pulse */
        #main-action-btn.select-lenses-mode {
            animation: pulse-btn 2s infinite;
        }
        @keyframes pulse-btn {
            0%,100% { box-shadow: 0 0 0 0 rgba(28,58,94,0.4); }
            50%      { box-shadow: 0 0 0 8px rgba(28,58,94,0); }
        }
    </style>

    <script>
        /* ══════════════════════════════
           PRODUCT TYPE TAB SWITCHER
        ══════════════════════════════ */
        function selectProductType(el) {
            document.querySelectorAll('.ptype-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');

            const typeId   = el.dataset.typeId;
            const hasPower = el.dataset.hasPower === '1';

            // Hide all power chip panels
            document.querySelectorAll('.power-chips-wrap').forEach(p => p.classList.add('d-none'));
            if (hasPower) {
                const panel = document.getElementById('powers-' + typeId);
                if (panel) panel.classList.remove('d-none');
            }

            // Update the main action button
            const btn = document.getElementById('main-action-btn');
            if (btn) {
                if (hasPower) {
                    btn.textContent = 'Select Lenses';
                    btn.classList.add('select-lenses-mode');
                } else {
                    btn.textContent = 'BUY NOW';
                    btn.classList.remove('select-lenses-mode');
                }
            }
        }

        /* ══════════════════════════════
           LENS MODAL STEP MANAGEMENT
        ══════════════════════════════ */
        let currentLensStep = 1;

        function showLensStep(step) {
            // Hide all steps
            document.querySelectorAll('.lens-step').forEach(s => s.style.display = 'none');
            // Show target step
            const target = document.getElementById('step' + step);
            if (target) target.style.display = 'block';

            // Update dots
            document.querySelectorAll('.step-dot').forEach((d, i) => {
                d.classList.toggle('active', i + 1 === step);
            });

            // Show/hide back button
            const prevBtn = document.getElementById('goPrev');
            if (prevBtn) prevBtn.style.visibility = step > 1 ? 'visible' : 'hidden';

            currentLensStep = step;
        }

        function selectLensType(card, nextStep) {
            document.querySelectorAll('.lens-type-card').forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            setTimeout(() => showLensStep(nextStep), 220);
        }

        /* ── Lens modal open / close ── */
        document.addEventListener('DOMContentLoaded', function () {
            const lensModal = document.getElementById('lensModal');
            if (!lensModal) return;

            lensModal.addEventListener('show.bs.modal', () => showLensStep(1));
            lensModal.addEventListener('hidden.bs.modal', () => {
                document.querySelectorAll('.lens-type-card').forEach(c => c.classList.remove('active'));
            });

            // "Buy with Lenses" button → Step 2
            const goStep2 = document.getElementById('goStep2');
            if (goStep2) goStep2.addEventListener('click', () => showLensStep(2));

            // Back button
            const goPrev = document.getElementById('goPrev');
            if (goPrev) {
                goPrev.addEventListener('click', () => {
                    if (currentLensStep === 3) showLensStep(2);
                    else if (currentLensStep === 2) showLensStep(1);
                });
            }
        });

        /* ── Power chip single-select ── */
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.power-chips-wrap').forEach(wrap => {
                wrap.querySelectorAll('.power-chip').forEach(chip => {
                    chip.addEventListener('click', function () {
                        wrap.querySelectorAll('.power-chip').forEach(c => c.classList.remove('active'));
                        this.classList.add('active');
                    });
                });
            });
        });
    </script>

@endsection
