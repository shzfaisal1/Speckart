<div class="row g-3" id="product-container">
    @forelse($productsList as $p)
    <div class="col-sm-6 col-md-4">
        <a href="{{ $p->detail_url }}" class="text-decoration-none">
            <div class="product-card">
                <div class="wishlist-btn">
                    <i class="bi bi-heart"></i>
                </div>

                <div class="product-image">
                    <img src="{{ $p->image_url }}" alt="{{ $p->product_name }}" class="img-default">
                    <img src="{{ $p->image_url }}" alt="{{ $p->product_name }} Hover" class="img-hover">
                </div>

                <div class="product-info">
                    <h6 class="brand-name">{{ $p->Company ?: 'Speckarts' }}</h6>
                    <p class="product-title">{{ $p->product_name }}</p>

                    <div class="size-rating">
                        <span class="size-text">Size : <span>{{ $p->Size ?: 'Medium' }}</span></span>
                        <div class="rating">
                            <span><i class="bi bi-star-fill"></i></span> 4.5 (10)
                        </div>
                    </div>

                    <div class="price-section">
                        <span class="price">₹{{ number_format($p->Retail_Price, 0) }}</span>
                        <button class="try-btn">Try on you</button>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <h5 class="text-muted">No products found matching the criteria.</h5>
    </div>
    @endforelse
</div>

@if($productsList->hasPages())
<div class="d-flex justify-content-center mt-4">
    {!! $productsList->appends(request()->query())->links('pagination::bootstrap-5') !!}
</div>
@endif
