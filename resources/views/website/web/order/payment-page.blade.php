   @extends('web.layout.master')
@section('content')
    <!-- breadcrumbs-section -->
    <section class="breadcrumbs-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul id="breadcrumbs">
                        <li><a href="#">Shipping Address</a></li>
                        <li><a href="#">Payment</a></li>
                        <li><a href="#">Summery</a></li>

                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- end breadcrumbs-section -->

    <!-- payment-page-sec -->
    <section class="payment-page-sec">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="payment-page-sec-txt">
                        <div class="payment-page-sec-txt-card">
                            <h4>Apply Discount Code</h4>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter Coupon Code">
                                <button class="btn btn-success" type="submit">APPLY</button>
                            </div>
                            <p>Available Coupons</p>
                        </div>

                        <div class="accordion-container">
                            <details open>
                                <summary>
                                    <span class="accordion-title">
                                        UPI
                                    </span>
                                    <span class="accordion-icon"> <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M6 9l6 6l6 -6" />
                                        </svg></span>
                                </summary>
                                <div class="accordion-content">
                                    <form action="">
                                        <div class="mb-3">
                                            <input type="email" class="form-control" id="email"
                                                placeholder="Your UPI ID" name="email">
                                        </div>
                                        <div class="InputGroup">
                                            <input type="radio" name="size" id="size_1" value="small" />
                                            <label for="size_1">@sbi</label>

                                            <input type="radio" name="size" id="size_2" value="small" />
                                            <label for="size_2">@icicibank</label>

                                            <input type="radio" name="size" id="size_3" value="small" />
                                            <label for="size_3">@okhdfcbank</label>

                                            <input type="radio" name="size" id="size_4" value="small" />
                                            <label for="size_4">@okaxis</label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <label class="form-check-label form-check-label1">
                                                <input class="form-check-input" type="checkbox" name="remember"> Save this
                                                UPI ID for faster checkout
                                            </label>
                                        </div>
                                    </form>
                                </div>
                            </details>
                            <details>
                                <summary>
                                    <span class="accordion-title cardimg">
                                        Cards <img src="{{asset('assets/img/bg/cardimg.png')}}" alt="">
                                    </span>
                                    <span class="accordion-icon"> <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M6 9l6 6l6 -6" />
                                        </svg>
                                    </span>
                                </summary>
                                <div class="accordion-content">
                                    <form>
                                        <div class="row">
                                            <div class="col">
                                                <input type="text" class="form-control" placeholder="Enter email"
                                                    name="email">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <input type="text" class="form-control" placeholder="Enter email"
                                                    name="email">
                                            </div>
                                            <div class="col">
                                                <input type="password" class="form-control" placeholder="Enter password"
                                                    name="pswd">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <input type="text" class="form-control" placeholder="Enter email"
                                                    name="email">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <input type="text" class="form-control" placeholder="Enter email"
                                                    name="email">
                                            </div>
                                        </div>
                                        <div class="row mb-0">
                                            <div class="col">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" name="remember">
                                                        Secure this card as per
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </details>
                            <details>
                                <summary>
                                    <span class="accordion-title">Net Banking</span>
                                    <span class="accordion-icon"> <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M6 9l6 6l6 -6" />
                                        </svg>
                                    </span>
                                </summary>
                                <div class="accordion-content">

                                    <div class="plans">
                                        <!-- <div class="title">Choose a pricing plan</div> -->
                                        <label class="plan basic-plan" for="basic">
                                            <input checked type="radio" name="plan" id="basic" />
                                            <div class="plan-content">
                                                <img loading="lazy" src="{{asset('assets/img/icon/hdfc.webp')}}" alt="" />
                                                <div class="plan-details">
                                                    <span>HDFC Bank</span>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="plan complete-plan" for="complete">
                                            <input type="radio" id="complete" name="plan" />
                                            <div class="plan-content">
                                                <img loading="lazy" src="{{asset('assets/img/icon/sbi.webp')}}" alt="" />
                                                <div class="plan-details">
                                                    <span>State Bank of India (SBI)</span>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="plan complete-plan" for="icici">
                                            <input type="radio" id="icici" name="plan" />
                                            <div class="plan-content">
                                                <img loading="lazy" src="{{asset('assets/img/icon/icici.webp')}}" alt="" />
                                                <div class="plan-details">
                                                    <span>ICICI Bank</span>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="plan complete-plan" for="Axis">
                                            <input type="radio" id="Axis" name="plan" />
                                            <div class="plan-content">
                                                <img loading="lazy" src="{{asset('assets/img/icon/axis.webp')}}" alt="" />
                                                <div class="plan-details">
                                                    <span>Axis Bank</span>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="plan complete-plan" for="Axis">
                                            <input type="radio" id="Axis" name="plan" />
                                            <div class="plan-content">
                                                <img loading="lazy" src="{{asset('assets/img/icon/axis.webp')}}" alt="" />
                                                <div class="plan-details">
                                                    <span>Axis Bank</span>
                                                </div>
                                            </div>
                                        </label>
                                        <label class="plan complete-plan" for="Axis">
                                            <input type="radio" id="Axis" name="plan" />
                                            <div class="plan-content">
                                                <img loading="lazy" src="{{asset('assets/img/icon/axis.webp')}}" alt="" />
                                                <div class="plan-details">
                                                    <span>Axis Bank</span>
                                                </div>
                                            </div>
                                        </label>

                                    </div>

                                </div>
                            </details>
                            <details>
                                <summary>
                                    <span class="accordion-title">Cash On Delivery</span>
                                    <span class="accordion-icon"> <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M6 9l6 6l6 -6" />
                                        </svg>
                                    </span>
                                </summary>
                                <div class="accordion-content">

                                    <div class="plans">

                                        <div class="form-check mb-3">
                                            <label class="form-check-label form-check-label1">
                                                <input class="form-check-input" type="checkbox" name="remember"> 
                                                Cash payment
                                            </label>
                                        </div>
                                    </div>

                                </div>
                            </details>
                            <button type="button" class="btn btn-checkout-submit">Continue</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="add-shipping-details-right">
                        <div class="add-shipping-details-right-img">
                            <img src="{{asset('assets/img/bg/Add-Shipping-details-bg.png')}}" alt="">
                        </div>
                        <div class="add-shipping-details-right-card">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td>Total Item Price</td>
                                        <td>₹3499</td>
                                    </tr>
                                    <tr>
                                        <td>Total Discount</td>
                                        <td>₹499</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Payable</b></td>
                                        <td><b>₹3000</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end payment-page-sec -->





    <!-- menu tab -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script>
        // Show the first tab and hide the rest
        $('#tabs-nav li:first-child').addClass('active');
        $('.tab-content').hide();
        $('.tab-content:first').show();

        // Click function
        $('#tabs-nav li').mouseenter(function() {
            $('#tabs-nav li').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').hide();

            var activeTab = $(this).find('a').attr('href');
            $(activeTab).fadeIn();
            return false;
        });
    </script>

    <script>
        // Show the first tab and hide the rest
        $('#tabs-navs1 li:first-child').addClass('active');
        $('.tab-content1').hide();
        $('.tab-content1:first').show();

        // Click function
        $('#tabs-nav1 li').mouseenter(function() {
            $('#tabs-nav1 li').removeClass('active');
            $(this).addClass('active');
            $('.tab-content1').hide();

            var activeTab = $(this).find('a').attr('href');
            $(activeTab).fadeIn();
            return false;
        });
    </script>

    <script>
        // Show the first tab and hide the rest
        $('#tabs-navs2 li:first-child').addClass('active');
        $('.tab-content2').hide();
        $('.tab-content2:first').show();

        // Click function
        $('#tabs-nav2 li').mouseenter(function() {
            $('#tabs-nav2 li').removeClass('active');
            $(this).addClass('active');
            $('.tab-content2').hide();

            var activeTab = $(this).find('a').attr('href');
            $(activeTab).fadeIn();
            return false;
        });
    </script>

    <script>
        // Show the first tab and hide the rest
        $('#tabs-navs3 li:first-child').addClass('active');
        $('.tab-content3').hide();
        $('.tab-content3:first').show();

        // Click function
        $('#tabs-nav3 li').mouseenter(function() {
            $('#tabs-nav3 li').removeClass('active');
            $(this).addClass('active');
            $('.tab-content3').hide();

            var activeTab = $(this).find('a').attr('href');
            $(activeTab).fadeIn();
            return false;
        });
    </script>
    <!-- end menu tab -->

    <!-- payment-page-sec -->
    <script>
        class Accordion {
            constructor(el) {
                this.el = el;
                this.summary = el.querySelector("summary");
                this.content = el.querySelector(".accordion-content");
                this.expandIcon = this.summary.querySelector(".accordion-icon");
                this.animation = null;
                this.isClosing = false;
                this.isExpanding = false;
                this.summary.addEventListener("click", (e) => this.onClick(e));
            }

            onClick(e) {
                e.preventDefault();
                this.el.style.overflow = "hidden";

                if (this.isClosing || !this.el.open) {
                    this.open();
                } else if (this.isExpanding || this.el.open) {
                    this.shrink();
                }
            }

            shrink() {
                this.isClosing = true;

                const startHeight = `${this.el.offsetHeight}px`;
                const endHeight = `${this.summary.offsetHeight}px`;

                if (this.animation) {
                    this.animation.cancel();
                }

                this.animation = this.el.animate({
                    height: [startHeight, endHeight]
                }, {
                    duration: 400,
                    easing: "ease-out"
                });
                this.animation.onfinish = () => {
                    this.expandIcon.setAttribute(
                        "src",
                        "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z'/></svg>"
                    );
                    return this.onAnimationFinish(false);
                };

                this.animation.oncancel = () => {
                    this.expandIcon.setAttribute(
                        "src",
                        "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z'/></svg>"
                    );
                    return (this.isClosing = false);
                };
            }

            open() {
                this.el.style.height = `${this.el.offsetHeight}px`;
                this.el.open = true;
                window.requestAnimationFrame(() => this.expand());
            }

            expand() {
                this.isExpanding = true;

                const startHeight = `${this.el.offsetHeight}px`;
                const endHeight = `${
      this.summary.offsetHeight + this.content.offsetHeight
    }px`;

                if (this.animation) {
                    this.animation.cancel();
                }

                this.animation = this.el.animate({
                    height: [startHeight, endHeight]
                }, {
                    duration: 350,
                    easing: "ease-out"
                });

                this.animation.onfinish = () => {
                    this.expandIcon.setAttribute(
                        "src",
                        "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z'/></svg>"
                    );
                    return this.onAnimationFinish(true);
                };
                this.animation.oncancel = () => {
                    this.expandIcon.setAttribute(
                        "src",
                        "data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'><path d='M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z'/></svg>"
                    );
                    return (this.isExpanding = false);
                };
            }

            onAnimationFinish(open) {
                this.el.open = open;
                this.animation = null;
                this.isClosing = false;
                this.isExpanding = false;
                this.el.style.height = this.el.style.overflow = "";
            }
        }

        document.querySelectorAll("details").forEach((el) => {
            new Accordion(el);
        });

        // Dynamic Checkout Post Submission
        $(document).ready(function() {
            $(document).on('click', '.btn-checkout-submit', function(e) {
                e.preventDefault();
                
                var form = $('<form>', {
                    'action': "{{ route('checkout.complete') }}",
                    'method': 'POST'
                }).append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': "{{ csrf_token() }}"
                }));
                
                $('body').append(form);
                form.submit();
            });
        });
    </script>
    <!-- end payment-page-sec -->
@endsection
