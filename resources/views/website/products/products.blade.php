@extends('website.layout.master')
@section('css')
<style>
/* ═══════════════════════════════════════════
   FILTER SIDEBAR — Titan/Lenskart Style
═══════════════════════════════════════════ */
.filter-sidebar {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #f0f0f0;
    position: sticky;
    top: 80px;
    max-height: calc(100vh - 100px);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #ddd transparent;
}
.filter-sidebar::-webkit-scrollbar { width: 4px; }
.filter-sidebar::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }

.filter-top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 18px 12px;
    border-bottom: 1px solid #f0f0f0;
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 10;
}
.filter-top-bar h6 {
    font-size: 15px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 7px;
}
.filter-top-bar h6 i { color: #07484A; font-size: 14px; }
.filter-reset-btn {
    font-size: 12px;
    font-weight: 600;
    color: #07484A;
    text-decoration: none;
    background: rgba(7,72,74,0.07);
    padding: 4px 12px;
    border-radius: 20px;
    transition: all 0.2s;
}
.filter-reset-btn:hover { background: #07484A; color: #fff; }

.active-chips-bar {
    padding: 8px 18px 0;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    min-height: 0;
}
.active-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(7,72,74,0.08);
    color: #07484A;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    cursor: pointer;
    border: 1px solid rgba(7,72,74,0.2);
    transition: all 0.2s;
}
.active-chip:hover { background: #07484A; color: #fff; }
.active-chip i { font-size: 9px; }

.filter-section { border-bottom: 1px solid #f0f0f0; }
.filter-section:last-child { border-bottom: none; }
.filter-section-btn {
    width: 100%;
    background: none;
    border: none;
    padding: 13px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    font-weight: 600;
    color: #1a1a1a;
    cursor: pointer;
    transition: background 0.15s;
    text-align: left;
}
.filter-section-btn:hover { background: #fafafa; }
.filter-section-btn i.chevron { font-size: 11px; color: #888; transition: transform 0.25s; }
.filter-section-btn.open i.chevron { transform: rotate(180deg); }
.filter-section-body { padding: 4px 18px 14px; display: none; }
.filter-section-body.open { display: block; }

.brand-search-wrap { position: relative; margin-bottom: 10px; }
.brand-search-wrap i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #999; font-size: 12px; }
.brand-search-input {
    width: 100%; padding: 7px 10px 7px 30px; border: 1px solid #e8e8e8;
    border-radius: 8px; font-size: 12px; color: #333; outline: none; transition: border 0.2s;
}
.brand-search-input:focus { border-color: #07484A; }

.icon-filter-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.icon-filter-item {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 8px 4px; border: 1px solid #e8e8e8; border-radius: 8px; cursor: pointer;
    transition: all 0.18s; background: #fafafa; text-align: center;
    font-size: 11px; font-weight: 500; color: #555; gap: 5px; line-height: 1.2;
}
.icon-filter-item img { width: 36px; height: 24px; object-fit: contain; }
.icon-filter-item:hover { border-color: #07484A; background: rgba(7,72,74,0.04); color: #07484A; }
.icon-filter-item.active-filter { border-color: #07484A; background: rgba(7,72,74,0.08); color: #07484A; font-weight: 600; }

.filter-checkbox-item {
    display: flex; align-items: center; gap: 8px; padding: 5px 0;
    cursor: pointer; font-size: 13px; color: #444; transition: color 0.15s;
}
.filter-checkbox-item:hover { color: #07484A; }
.filter-checkbox-item input { width: 15px; height: 15px; accent-color: #07484A; cursor: pointer; flex-shrink: 0; }
.filter-checkbox-item .color-dot { width: 14px; height: 14px; border-radius: 50%; border: 1px solid #ddd; flex-shrink: 0; }

.gender-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; }
.gender-pill {
    display: flex; align-items: center; justify-content: center; gap: 5px;
    padding: 7px 4px; border: 1px solid #e8e8e8; border-radius: 8px; cursor: pointer;
    font-size: 12px; font-weight: 500; color: #555; background: #fafafa;
    transition: all 0.18s; user-select: none;
}
.gender-pill:hover { border-color: #07484A; color: #07484A; background: rgba(7,72,74,0.04); }
.gender-pill.active-filter { border-color: #07484A; background: rgba(7,72,74,0.08); color: #07484A; font-weight: 600; }
.gender-pill i { font-size: 13px; }

.price-range-options { display: flex; flex-direction: column; gap: 2px; }

.filter-search-bar {
    display: flex; align-items: center; background: #fff;
    border: 1px solid #e8e8e8; border-radius: 10px; overflow: hidden; height: 40px; min-width: 240px;
}
.filter-search-bar input { border: none; outline: none; padding: 0 14px; font-size: 13px; color: #333; flex: 1; height: 100%; }
.filter-search-bar button {
    background: #07484A; color: #fff; border: none; padding: 0 16px;
    font-size: 13px; font-weight: 500; height: 100%; cursor: pointer; transition: background 0.2s;
}
.filter-search-bar button:hover { background: #055a5c; }

.sort-dropdown-btn {
    background: #fff; border: 1px solid #e8e8e8; border-radius: 8px; padding: 7px 14px;
    font-size: 13px; font-weight: 500; color: #333; display: flex; align-items: center; gap: 6px; cursor: pointer;
}
.sort-dropdown-btn:hover { border-color: #07484A; color: #07484A; }
</style>
@endsection

@section('content')
<section class="product py-3">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-0" style="font-size:20px; color:#1a1a1a;">
                    {{ ($activeCategory ?? null) ? $activeCategory->name : 'All Products' }}
                </h5>
                <p class="mb-0" style="font-size:13px; color:#888;">
                    {{ $productsList->total() }} products found
                </p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="filter-search-bar">
                    <input type="text" id="search-input" placeholder="Search products…" autocomplete="off">
                    <button type="button" id="search-btn"><i class="bi bi-search"></i></button>
                </div>
                <div class="dropdown">
                    <button class="sort-dropdown-btn dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-sort-down"></i> Sort By
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="min-width:180px;">
                        <li><a class="dropdown-item py-2 sort-item" data-sort="">Popularity</a></li>
                        <li><a class="dropdown-item py-2 sort-item" data-sort="newest">Newest First</a></li>
                        <li><a class="dropdown-item py-2 sort-item" data-sort="price_low">Price: Low to High</a></li>
                        <li><a class="dropdown-item py-2 sort-item" data-sort="price_high">Price: High to Low</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="prod pb-5">
    <div class="container">
        <div class="row g-4">

            {{-- FILTER SIDEBAR --}}
            <div class="col-lg-3 col-md-4">
                <div class="filter-sidebar">

                    <div class="filter-top-bar">
                        <h6><i class="bi bi-sliders"></i> Filters</h6>
                        <a href="{{ route('products', ($activeCategory ?? null) ? ['category' => $activeCategory->slug] : []) }}"
                           class="filter-reset-btn">Clear All</a>
                    </div>

                    <div class="active-chips-bar" id="active-chips-bar"></div>

                    {{-- BRAND --}}
                    @if(in_array('brand', $allowedFilters) && !empty($filterData['brands']))
                    <div class="filter-section">
                        <button class="filter-section-btn open" data-target="sec-brand">
                            Brands <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body open" id="sec-brand">
                            <div class="brand-search-wrap">
                                <i class="bi bi-search"></i>
                                <input type="text" class="brand-search-input" placeholder="Search brands…" id="brand-search">
                            </div>
                            <div id="brand-list">
                                @foreach($filterData['brands'] as $idx => $brandVal)
                                <label class="filter-checkbox-item brand-item">
                                    <input type="checkbox" class="filter-checkbox" data-filter="brand" data-value="{{ $brandVal }}">
                                    {{ $brandVal }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- FRAME TYPE --}}
                    @if(in_array('frame_type', $allowedFilters))
                    <div class="filter-section">
                        <button class="filter-section-btn open" data-target="sec-frametype">
                            Frame Type <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body open" id="sec-frametype">
                            <div class="icon-filter-grid">
                                <div class="icon-filter-item" data-filter="frame_type" data-value="Full Rim">
                                    <img src="{{ asset('assets/img/icon/full-rim.png') }}" alt="Full Rim" onerror="this.style.display='none'">
                                    Full Rim
                                </div>
                                <div class="icon-filter-item" data-filter="frame_type" data-value="Half Rim">
                                    <img src="{{ asset('assets/img/icon/half-rim.png') }}" alt="Half Rim" onerror="this.style.display='none'">
                                    Half Rim
                                </div>
                                <div class="icon-filter-item" data-filter="frame_type" data-value="Rimless">
                                    <img src="{{ asset('assets/img/icon/rimless.png') }}" alt="Rimless" onerror="this.style.display='none'">
                                    Rimless
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- FRAME SHAPE --}}
                    @if(in_array('shape', $allowedFilters))
                    @php
                        $allShapeIcons = ['Round'=>'round.png','Rectangle'=>'rectangle.png','Aviator'=>'aviator.png','Cat Eye'=>'cateye.png','Square'=>'square.png','Wayfarer'=>'wayfarer.png','Oval'=>'oval.png','Hexagonal'=>'hexagonal.png'];
                        $shapesToShow = !empty($filterData['shapes']) ? $filterData['shapes'] : array_keys($allShapeIcons);
                    @endphp
                    <div class="filter-section">
                        <button class="filter-section-btn" data-target="sec-shape">
                            Shape <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body" id="sec-shape">
                            <div class="icon-filter-grid">
                                @foreach($shapesToShow as $shapeVal)
                                @php $icon = $allShapeIcons[$shapeVal] ?? 'round.png'; @endphp
                                <div class="icon-filter-item" data-filter="shape" data-value="{{ $shapeVal }}">
                                    <img src="{{ asset('assets/img/icon/' . $icon) }}" alt="{{ $shapeVal }}" onerror="this.style.display='none'">
                                    {{ $shapeVal }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- GENDER --}}
                    @if(in_array('gender', $allowedFilters))
                    <div class="filter-section">
                        <button class="filter-section-btn" data-target="sec-gender">
                            Gender <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body" id="sec-gender">
                            <div class="gender-grid">
                                @foreach($filterData['genders'] as $genderVal)
                                @php $gIcon = ['Men'=>'bi-person','Women'=>'bi-person-dress','Unisex'=>'bi-people','Kids'=>'bi-person-hearts'][$genderVal] ?? 'bi-person'; @endphp
                                <div class="gender-pill" data-filter="gender" data-value="{{ $genderVal }}">
                                    <i class="bi {{ $gIcon }}"></i> {{ $genderVal }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- OCCASION --}}
                    @if(in_array('occasion', $allowedFilters))
                    <div class="filter-section">
                        <button class="filter-section-btn" data-target="sec-occasion">
                            Occasion <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body" id="sec-occasion">
                            @foreach($filterData['occasions'] as $occVal)
                            <label class="filter-checkbox-item">
                                <input type="checkbox" class="filter-checkbox" data-filter="occasion" data-value="{{ $occVal }}">
                                {{ $occVal }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- AGE GROUP --}}
                    @if(in_array('age', $allowedFilters))
                    <div class="filter-section">
                        <button class="filter-section-btn" data-target="sec-age">
                            Age Group <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body" id="sec-age">
                            @foreach($filterData['ages'] as $ageVal)
                            <label class="filter-checkbox-item">
                                <input type="checkbox" class="filter-checkbox" data-filter="age" data-value="{{ $ageVal }}">
                                {{ $ageVal }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- FRAME COLOR --}}
                    @if(in_array('color', $allowedFilters))
                    @php
                        $colorsList = !empty($filterData['colors']) ? $filterData['colors'] : ['Black','Brown','Blue','Gold','Silver','Grey'];
                        $colorHexMap = ['Black'=>'#1a1a1a','Brown'=>'#8B4513','Blue'=>'#2563EB','Gold'=>'#F59E0B','Silver'=>'#9CA3AF','Red'=>'#EF4444','Green'=>'#16A34A','Yellow'=>'#EAB308','Pink'=>'#EC4899','Grey'=>'#6B7280','White'=>'#FFFFFF','Purple'=>'#7C3AED','Orange'=>'#F97316'];
                    @endphp
                    <div class="filter-section">
                        <button class="filter-section-btn" data-target="sec-color">
                            Frame Color <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body" id="sec-color">
                            @foreach($colorsList as $idx => $colorVal)
                            @php $hex = $colorHexMap[$colorVal] ?? '#CCCCCC'; @endphp
                            <label class="filter-checkbox-item">
                                <input type="checkbox" class="filter-checkbox" data-filter="color" data-value="{{ $colorVal }}">
                                <span class="color-dot" style="background:{{ $hex }};"></span>
                                {{ $colorVal }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- MATERIAL --}}
                    @if(in_array('material', $allowedFilters))
                    @php $materialsList = !empty($filterData['materials']) ? $filterData['materials'] : ['Metal','Acetate','TR90','Titanium','Plastic']; @endphp
                    <div class="filter-section">
                        <button class="filter-section-btn" data-target="sec-material">
                            Material <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body" id="sec-material">
                            @foreach($materialsList as $idx => $matVal)
                            <label class="filter-checkbox-item">
                                <input type="checkbox" class="filter-checkbox" data-filter="material" data-value="{{ $matVal }}">
                                {{ $matVal }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- FRAME SIZE --}}
                    @if(in_array('size', $allowedFilters))
                    @php $sizesList = !empty($filterData['sizes']) ? $filterData['sizes'] : ['Extra Small','Small','Medium','Large','Extra Large']; @endphp
                    <div class="filter-section">
                        <button class="filter-section-btn" data-target="sec-size">
                            Frame Size <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body" id="sec-size">
                            @foreach($sizesList as $idx => $sizeVal)
                            <label class="filter-checkbox-item">
                                <input type="checkbox" class="filter-checkbox" data-filter="size" data-value="{{ $sizeVal }}">
                                {{ $sizeVal }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- MODALITY (Contact Lenses) --}}
                    @if(in_array('modality', $allowedFilters))
                    @php $modalitiesList = !empty($filterData['modalities']) ? $filterData['modalities'] : ['Daily','Fortnightly','Monthly','Quarterly','Yearly']; @endphp
                    <div class="filter-section">
                        <button class="filter-section-btn open" data-target="sec-modality">
                            Disposability <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body open" id="sec-modality">
                            @foreach($modalitiesList as $idx => $modVal)
                            <label class="filter-checkbox-item">
                                <input type="checkbox" class="filter-checkbox" data-filter="modality" data-value="{{ $modVal }}">
                                {{ $modVal }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- PRICE RANGE --}}
                    @if(in_array('price_range', $allowedFilters))
                    <div class="filter-section">
                        <button class="filter-section-btn" data-target="sec-price">
                            Price Range <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body" id="sec-price">
                            <div class="price-range-options">
                                <label class="filter-checkbox-item"><input type="radio" class="filter-radio" name="price_range" data-filter="price_range" data-value="under_500"> Under &#8377;500</label>
                                <label class="filter-checkbox-item"><input type="radio" class="filter-radio" name="price_range" data-filter="price_range" data-value="under_1000"> Under &#8377;1,000</label>
                                <label class="filter-checkbox-item"><input type="radio" class="filter-radio" name="price_range" data-filter="price_range" data-value="under_2000"> Under &#8377;2,000</label>
                                <label class="filter-checkbox-item"><input type="radio" class="filter-radio" name="price_range" data-filter="price_range" data-value="under_5000"> Under &#8377;5,000</label>
                                <label class="filter-checkbox-item"><input type="radio" class="filter-radio" name="price_range" data-filter="price_range" data-value="above_5000"> Above &#8377;5,000</label>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- COLLECTIONS --}}
                    @if(in_array('collections', $allowedFilters))
                    <div class="filter-section">
                        <button class="filter-section-btn" data-target="sec-collections">
                            Collections <i class="bi bi-chevron-down chevron"></i>
                        </button>
                        <div class="filter-section-body" id="sec-collections">
                            @foreach(['Summer Collection','Winter Collection','Classic Collection','Sports Collection','Premium'] as $idx => $collVal)
                            <label class="filter-checkbox-item">
                                <input type="checkbox" class="filter-checkbox" data-filter="collection" data-value="{{ $collVal }}">
                                {{ $collVal }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            {{-- PRODUCT GRID --}}
            <div class="col-lg-9 col-md-8 filtercardscroll" id="product-grid-container">
                @include('website.products.product_grid')
            </div>

        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);

    // Accordion toggle
    document.querySelectorAll('.filter-section-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const target = document.getElementById(this.getAttribute('data-target'));
            const isOpen = this.classList.contains('open');
            this.classList.toggle('open', !isOpen);
            if (target) target.classList.toggle('open', !isOpen);
        });
    });

    // Brand search
    const brandSearch = document.getElementById('brand-search');
    if (brandSearch) {
        brandSearch.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.brand-item').forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(q) ? 'flex' : 'none';
            });
        });
    }

    // Apply all filters
    function applyFilters() {
        const params = new URLSearchParams(window.location.search);
        // Icon filters (frame_type, shape)
        ['frame_type', 'shape'].forEach(key => {
            const vals = [];
            document.querySelectorAll(`.icon-filter-item[data-filter="${key}"].active-filter`).forEach(el => vals.push(el.getAttribute('data-value')));
            vals.length ? params.set(key, vals.join(',')) : params.delete(key);
        });
        // Gender pills
        const gVals = [];
        document.querySelectorAll('.gender-pill.active-filter').forEach(el => gVals.push(el.getAttribute('data-value')));
        gVals.length ? params.set('gender', gVals.join(',')) : params.delete('gender');
        // Checkboxes
        ['color','brand','size','modality','material','collection','occasion','age'].forEach(key => {
            const vals = [];
            document.querySelectorAll(`.filter-checkbox[data-filter="${key}"]:checked`).forEach(el => vals.push(el.getAttribute('data-value')));
            vals.length ? params.set(key, vals.join(',')) : params.delete(key);
        });
        // Price radio
        const pr = document.querySelector('.filter-radio[name="price_range"]:checked');
        pr ? params.set('price_range', pr.getAttribute('data-value')) : params.delete('price_range');
        window.location.search = params.toString();
    }

    // Sync UI from URL
    ['frame_type','shape'].forEach(key => {
        if (urlParams.has(key)) {
            const vals = urlParams.get(key).split(',');
            document.querySelectorAll(`.icon-filter-item[data-filter="${key}"]`).forEach(el => {
                if (vals.includes(el.getAttribute('data-value'))) el.classList.add('active-filter');
            });
        }
    });
    if (urlParams.has('gender')) {
        const vals = urlParams.get('gender').split(',');
        document.querySelectorAll('.gender-pill').forEach(el => {
            if (vals.includes(el.getAttribute('data-value'))) el.classList.add('active-filter');
        });
    }
    ['color','brand','size','modality','material','collection','occasion','age'].forEach(key => {
        if (urlParams.has(key)) {
            const vals = urlParams.get(key).split(',');
            document.querySelectorAll(`.filter-checkbox[data-filter="${key}"]`).forEach(el => {
                if (vals.includes(el.getAttribute('data-value'))) el.checked = true;
            });
        }
    });
    if (urlParams.has('price_range')) {
        const val = urlParams.get('price_range');
        const radio = document.querySelector(`.filter-radio[data-value="${val}"]`);
        if (radio) radio.checked = true;
    }

    // Active chips
    function updateChips() {
        const bar = document.getElementById('active-chips-bar');
        if (!bar) return;
        bar.innerHTML = '';
        const labels = {frame_type:'Type',shape:'Shape',gender:'Gender',color:'Color',brand:'Brand',size:'Size',modality:'Disposability',material:'Material',collection:'Collection',occasion:'Occasion',age:'Age',price_range:'Price',search:'Search'};
        urlParams.forEach((val, key) => {
            if (key === 'category') return;
            const label = labels[key] || key;
            val.split(',').forEach(v => {
                const chip = document.createElement('span');
                chip.className = 'active-chip';
                chip.innerHTML = label + ': ' + v + ' <i class="bi bi-x"></i>';
                chip.addEventListener('click', () => {
                    const existing = urlParams.get(key);
                    if (!existing) return;
                    const arr = existing.split(',').filter(x => x !== v);
                    arr.length ? urlParams.set(key, arr.join(',')) : urlParams.delete(key);
                    window.location.search = urlParams.toString();
                });
                bar.appendChild(chip);
            });
        });
    }
    updateChips();

    // Bind events
    document.querySelectorAll('.icon-filter-item').forEach(el => {
        el.addEventListener('click', function() { this.classList.toggle('active-filter'); applyFilters(); });
    });
    document.querySelectorAll('.gender-pill').forEach(el => {
        el.addEventListener('click', function() { this.classList.toggle('active-filter'); applyFilters(); });
    });
    document.querySelectorAll('.filter-checkbox, .filter-radio').forEach(el => {
        el.addEventListener('change', applyFilters);
    });

    // Search
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    if (searchInput) {
        if (urlParams.has('search')) searchInput.value = urlParams.get('search');
        const doSearch = () => {
            searchInput.value.trim() ? urlParams.set('search', searchInput.value.trim()) : urlParams.delete('search');
            window.location.search = urlParams.toString();
        };
        searchInput.addEventListener('keypress', e => { if (e.key === 'Enter') doSearch(); });
        if (searchBtn) searchBtn.addEventListener('click', doSearch);
    }

    // Sort
    document.querySelectorAll('.sort-item').forEach(el => {
        el.addEventListener('click', function() {
            const val = this.getAttribute('data-sort');
            val ? urlParams.set('sort', val) : urlParams.delete('sort');
            window.location.search = urlParams.toString();
        });
    });

    // Wishlist
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('active');
            const heart = this.querySelector('i');
            if (heart) { heart.classList.toggle('bi-heart'); heart.classList.toggle('bi-heart-fill'); }
        });
    });
});
</script>
@endsection
