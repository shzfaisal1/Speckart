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
    <button class="filter-section-btn open" data-target="sec-shape">
        Shape <i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="filter-section-body open" id="sec-shape">
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
    <button class="filter-section-btn open" data-target="sec-gender">
        Gender <i class="bi bi-chevron-down chevron"></i>
    </button>
    <div class="filter-section-body open" id="sec-gender">
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
