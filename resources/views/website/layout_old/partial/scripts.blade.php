<!-- menu tab -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
    // Show the first tab and hide the rest
    $('#tabs-nav li:first-child').addClass('active');
    $('.tab-content').hide();
    $('.tab-content:first').show();

    // Click function
    $('#tabs-nav li').mouseenter(function(){
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
    $('#tabs-nav1 li').mouseenter(function(){
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
    $('#tabs-nav2 li').mouseenter(function(){
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
    $('#tabs-nav3 li').mouseenter(function(){
        $('#tabs-nav3 li').removeClass('active');
        $(this).addClass('active');
        $('.tab-content3').hide();
    
        var activeTab = $(this).find('a').attr('href');
        $(activeTab).fadeIn();
        return false;
    });
</script>
<!-- end menu tab -->

<!-- home-slider1 -->
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script>
    $(document).ready(function(){
      $('.my-slider').slick({
        slidesToShow: 7,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        speed: 300,
        infinite: true,
        autoplaySpeed: 5000,
        autoplay: true,
        responsive: [
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 3,
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
        }
      }
    ]
      });
    });
</script>
<!-- end home-slider1 -->

<!-- sunglasses-section-slider -->
<script>
    $(document).ready(function(){
      $('.sunglasses-slider').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        speed: 300,
        infinite: true,
        autoplaySpeed: 5000,
        autoplay: false,
        responsive: [
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 3,
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
        }
      }
    ]
      });
    });
</script>
<!-- end sunglasses-section-slider -->

<!-- new-arrivals-slider -->
<script>
    $(document).ready(function(){
      $('.new-arrivals-slider').slick({
        slidesToShow: 3.5,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        speed: 300,
        infinite: true,
        autoplaySpeed: 5000,
        autoplay: true,
        rtl: true,
        responsive: [
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 3,
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
        }
      }
    ]
      });
    });
</script>
<!-- end new-arrivals-slider -->

<!-- shop-by-brand-slider -->
<script>
    $(document).ready(function(){
      $('.shop-by-brand-slider').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        speed: 300,
        infinite: true,
        autoplaySpeed: 5000,
        autoplay: false,
        responsive: [
      {
        breakpoint: 991,
        settings: {
          slidesToShow: 3,
        }
      },
      {
        breakpoint: 767,
        settings: {
          slidesToShow: 1,
        }
      }
    ]
      });
    });
</script>
<!-- end shop-by-brand-slider -->

<!-- testmonial-section -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.js"></script>
<script>
  var swiper = new Swiper(".swiper", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    loop: true,
    // autoplay: {
    //   delay: 3000, 
    //   disableOnInteraction: false, 
    // },
    coverflowEffect: {
      rotate: 0,
      stretch: 0,
      depth: 100,
      modifier: 4,
      slideShadows: true
    },
    // Navigation arrows
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev"
    },
    keyboard: {
      enabled: true
    },
    mousewheel: {
      thresholdDelta: 70
    },
    breakpoints: {
      560: {
        slidesPerView: 2.5
      },
      768: {
        slidesPerView: 3
      },
      1024: {
        slidesPerView: 2.5
      }
    }
  });
</script>
<!-- end testmonial-section -->

<!-- own-creation -->
<script>
    var swiper = new Swiper('.swiperrr', {
  slidesPerView: 4,
  centeredSlides: false,
  loop: true,
  spaceBetween: 30,
  dots: false,
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
});

</script>
<!-- end own-creation -->

<!-- Search Auto-suggest Script -->
<script>
    $(document).ready(function() {
        var ajaxCall = null;

        function getRecentSearches() {
            var searches = localStorage.getItem('recentSearches');
            return searches ? JSON.parse(searches) : [];
        }

        function addRecentSearch(query) {
            if (!query) return;
            var searches = getRecentSearches();
            searches = searches.filter(function(s) { return s !== query; });
            searches.unshift(query);
            if (searches.length > 5) {
                searches.pop();
            }
            localStorage.setItem('recentSearches', JSON.stringify(searches));
        }

        $('form:has(.ajax-search-input)').on('submit', function() {
            var query = $(this).find('.ajax-search-input').val().trim();
            addRecentSearch(query);
        });

        function renderRecentSearches() {
            var searches = getRecentSearches();
            if (searches.length === 0) return '';
            
            var html = '<div class="suggestion-section"><div class="suggestion-header">RECENT SEARCH</div><ul class="suggestion-list">';
            searches.forEach(function(s) {
                html += '<li><a href="{{ route("products") }}?search=' + encodeURIComponent(s) + '">';
                html += '<i class="bi bi-clock-history text-secondary me-2"></i>' + s;
                html += '</a></li>';
            });
            html += '</ul></div>';
            return html;
        }

        function fetchSuggestions(inputElement) {
            var query = inputElement.val().trim();
            var searchBox = inputElement.closest('.search-box');
            var searchDropdown = searchBox.find('.ajax-search-dropdown');
            var searchContent = searchBox.find('.search-suggestions-content');

            if (ajaxCall) {
                ajaxCall.abort();
            }

            ajaxCall = $.ajax({
                url: '{{ route("ajax.search") }}',
                method: 'GET',
                data: { search: query },
                success: function(response) {
                    if (response.status === 'success') {
                        var html = '';
                        
                        if (query === '') {
                            html += renderRecentSearches();
                            html += '<div class="suggestion-section"><ul class="suggestion-list">';
                            response.data.forEach(function(item) {
                                html += '<li><a href="' + item.url + '">';
                                html += '<img src="' + item.image_url + '" alt="icon" class="suggestion-img">';
                                html += item.name;
                                html += '</a></li>';
                            });
                            html += '</ul></div>';
                        } else {
                            if (response.data.length > 0) {
                                html += '<div class="suggestion-section"><ul class="suggestion-list">';
                                response.data.forEach(function(item) {
                                    html += '<li><a href="' + item.url + '">';
                                    html += '<img src="' + item.image_url + '" alt="product" class="suggestion-img">';
                                    html += item.name;
                                    html += '</a></li>';
                                });
                                html += '</ul></div>';
                            } else {
                                html += '<div class="suggestion-section p-3 text-center text-muted">No results found for "' + query + '"</div>';
                            }
                        }

                        searchContent.html(html);
                        searchDropdown.show();
                    }
                }
            });
        }

        $('.ajax-search-input').on('keyup focus', function() {
            fetchSuggestions($(this));
        });

        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-box').length) {
                $('.ajax-search-dropdown').hide();
            }
        });
    });
</script>