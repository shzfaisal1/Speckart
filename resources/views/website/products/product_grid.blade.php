<div class="row gy-3 gap-x-3" id="product-container">
    @if(isset($productsList) && $productsList->count() > 0)
        @foreach($productsList as $product)
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="product-card">
                    <div class="wishlist-btn btn-wishlist-toggle" data-product-id="{{ $product->product_id ?: $product->id }}" data-wishlist-product-id="{{ $product->product_id ?: $product->id }}">
                        <i
                            class="bi {{ in_array($product->product_id ?: $product->id, $wishlistProductIds ?? []) ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                    </div>
                    <a href="{{ $product->detail_url }}" class="product-card-link text-decoration-none">
                        <div class="product-image">
                            <img src="{{ $product->image_url }}" alt="{{ $product->product_name ?: $product->product_code }}" class="img-default">
                        </div>
                        <div class="product-info">
                        <h6 class="brand-name">{{ $product->Company ?: 'Speckart' }}</h6>
                        <p class="product-title">{{ $product->product_name ?: $product->product_code }}</p>

                        <div class="size-rating">
                            <span class="size-text">Size : <span>{{ $product->Size ?: 'Medium' }}</span></span>
                            <div class="rating">
                                <span><i class="bi bi-star-fill"></i></span> 4.5 (210)
                            </div>
                        </div>

                        <div class="price-section">
                            @if(!empty($product->discount_price) && $product->discount_price < $product->Retail_Price)
                                <span class="price">₹{{ number_format($product->discount_price, 2) }}</span>
                                <span class="text-muted text-decoration-line-through ms-2" style="font-size: 13px; font-weight: 500;">₹{{ number_format($product->Retail_Price, 2) }}</span>
                            @else
                                <span class="price">₹{{ number_format($product->Retail_Price, 2) }}</span>
                            @endif
                            <button class="try-btn">Try on you</button>
                        </div>
                    </div>
                    </a>
                </div>
            <!--</a>-->
        </div>
        @endforeach

        <div class="col-12 mt-5 d-flex justify-content-center">
            @if($productsList->hasPages())
            <nav aria-label="Page navigation">
                <ul class="pagination modern-pagination">
                    {{-- Previous Page Link --}}
                    @if ($productsList->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $productsList->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left"></i></a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($productsList->getUrlRange(max(1, $productsList->currentPage() - 2), min($productsList->lastPage(), $productsList->currentPage() + 2)) as $page => $url)
                        @if ($page == $productsList->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($productsList->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $productsList->nextPageUrl() }}" rel="next"><i class="bi bi-chevron-right"></i></a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    @else
        <div class="col-12 text-center py-5">
            <h3 class="text-muted">Not Found</h3>
            <p>We couldn't find any products matching your search.</p>
        </div>
    @endif
</div>
