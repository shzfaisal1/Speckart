
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

    <div class="bg-white p-6 rounded-xl shadow-sm border flex flex-col items-center justify-center min-h-[300px] relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
        
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6 self-start">Store Rating</h3>
        
        <div class="text-center relative z-10">
            <div class="inline-flex items-baseline gap-1">
                <span class="text-7xl font-black text-blue-600 tracking-tighter">
                    {{ number_format($overall['avg_overall_score']/10, 1) }}
                </span>
                <span class="text-2xl font-bold text-gray-300">/10</span>
            </div>
            
            <div class="flex gap-1.5 mt-4 justify-center">
                @php $rating10 = round($overall['avg_overall_score']/10); @endphp
                @for($i = 1; $i <= 10; $i++)
                    <i class="fa fa-star @if($i <= $rating10) text-amber-400 @else text-gray-100 @endif text-xl drop-shadow-sm"></i>
                @endfor
            </div>
            
            <p class="mt-6 text-xs font-bold text-gray-400 uppercase tracking-widest italic">
                Overall Customer Satisfaction
                @if(isset($selectedStore))
                    <br><span class="text-blue-500">— {{ $selectedStore->store_name }} —</span>
                @endif
            </p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border">
        <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center flex-wrap gap-2" id="drillTitle">
            <i class="fa fa-list-ul text-blue-500"></i> Detailed Question Ratings
            @if(isset($selectedStore))
                <span class="text-blue-600 font-extrabold ml-1">— {{ $selectedStore->store_name }}</span>
            @endif
        </h3>
        <div class="space-y-6">
            @foreach($questionRatings as $label => $val)
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $label }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-blue-600">{{ number_format($val * 2, 1) }}</span>
                            <div class="flex gap-0.5">
                                @for($i = 1; $i <= 10; $i++)
                                    <i class="fa fa-star @if($i <= round($val * 2)) text-amber-400 @else text-gray-200 @endif text-[8px]"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden border border-gray-50 flex">
                        @php
                            $val10 = $val * 2;
                            $perc = ($val10 / 10) * 100;
                            $color = 'bg-blue-500';
                            if($val10 >= 9.0) $color = 'bg-green-500';
                            elseif($val10 >= 7.5) $color = 'bg-blue-500';
                            elseif($val10 >= 6.0) $color = 'bg-yellow-500';
                            else $color = 'bg-red-500';
                        @endphp
                        <div class="{{ $color }} h-full rounded-full transition-all duration-700 shadow-sm" style="width: {{ $perc }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

<p class="text-xs text-gray-400 mb-4">Data Last Fetched: {{ now()->format('M d, Y H:i:s') }}</p>


<!-- Overall Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500"></div>
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Overall NPS</h2>
        <div class="flex items-baseline gap-2 mt-2">
            <p class="text-4xl font-black @if($overall['nps_score'] >= 50) text-green-600 @elseif($overall['nps_score'] >= 0) text-blue-600 @else text-red-600 @endif">
                {{ number_format($overall['nps_score'], 1) }}
            </p>
            <span class="text-xs text-gray-400 font-medium">Score Index</span>
        </div>
        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-50 text-[11px] font-bold">
            <div class="flex flex-col">
                <span class="text-gray-400">PROMOTERS</span>
                <span class="text-green-600">{{ number_format($overall['percent_promoters'], 1) }}%</span>
            </div>
            <div class="flex flex-col text-right">
                <span class="text-gray-400">DETRACTORS</span>
                <span class="text-red-500">{{ number_format($overall['percent_detractors'], 1) }}%</span>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 left-0 w-1.5 h-full bg-red-500"></div>
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">% Poor NPS (< 79)</h2>
        <div class="flex items-baseline gap-2 mt-2">
            <p class="text-4xl font-black text-red-600">{{ number_format($overall['percent_poor'], 1) }}%</p>
            <span class="text-xs text-gray-400 font-medium">{{ $overall['poor_nps_count'] }} cases</span>
        </div>
        <div class="mt-6">
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-red-500 h-1.5 rounded-full shadow-[0_0_8px_rgba(239, 68, 68, 0.4)]" style="width: {{ $overall['percent_poor'] }}%"></div>
            </div>
            <p class="text-[10px] text-gray-400 mt-2 font-medium">Negative sentiment threshold</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 left-0 w-1.5 h-full bg-green-500"></div>
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Promoter % (90+)</h2>
        <div class="flex items-baseline gap-2 mt-2">
            <p class="text-4xl font-black text-green-600">{{ number_format($overall['percent_promoter_90plus'], 1) }}%</p>
            <span class="text-xs text-gray-400 font-medium">{{ $overall['promoter_90plus_count'] }} fans</span>
        </div>
        <div class="mt-6">
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-green-500 h-1.5 rounded-full shadow-[0_0_8px_rgba(34, 197, 94, 0.4)]" style="width: {{ $overall['percent_promoter_90plus'] }}%"></div>
            </div>
            <p class="text-[10px] text-gray-400 mt-2 font-medium">Brand loyalty peak</p>
        </div>
    </div>
</div>

<!-- Category Distribution Bar Chart -->
<div class="bg-white p-6 rounded-xl shadow-sm border mb-10 w-full">
    <h2 class="text-lg font-bold text-gray-800 mb-4">NPS Feedback Distribution (%)</h2>
    <div class="relative h-72 w-full">
        <canvas id="categoryChart"></canvas>
    </div>
</div>

<!-- Stats by Store -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="text-lg font-bold text-gray-800">Performance by Store</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
    <tr class="bg-gray-100 text-gray-600 text-xs uppercase font-bold tracking-wider">
        <th class="px-6 py-3">Store</th>                    <!-- Changed -->
        <th class="px-6 py-3">Total Responses</th>
        <th class="px-6 py-3">Promoters (%)</th>
        <th class="px-6 py-3">Detractors (%)</th>
        <th class="px-6 py-3 text-right">NPS Score</th>
    </tr>
</thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stats as $row)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-800">
                <strong>{{ $row->store_name ?? 'Unknown Store' }}</strong>
                <span class="text-xs text-gray-500 block">(ID: {{ $row->store_id }})</span>
            </td>
                        <td class="px-6 py-4 text-gray-600">{{ $row->total_responses }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-bold">
                                {{ $row->promoters }} ({{ number_format($row->percent_promoters, 1) }}%)
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-bold">
                                {{ $row->detractors }} ({{ number_format($row->percent_detractors, 1) }}%)
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-lg font-bold @if($row->nps_score >= 50) text-green-600 @elseif($row->nps_score >= 0) text-blue-600 @else text-red-600 @endif">
                                {{ number_format($row->nps_score, 1) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                            No survey data available yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-10">
    <!-- Top 5 Complaints -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">Recent Complaints</h2>
        </div>
        <div class="p-6">
            @if($topComplaints->count() > 0)
                <ul class="space-y-4">
                    @foreach($topComplaints as $complaint)
                    <li class="bg-orange-50 p-4 rounded-lg border border-orange-100 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <span class="bg-orange-200 text-orange-800 text-xs font-bold px-2 py-1 rounded">Score: {{ $complaint->nps_score }}/10</span>
                            <span class="text-xs text-gray-500">{{ $complaint->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-800 text-sm">"{{ $complaint->improvement_needed }}"</p>
                        @if($complaint->detailed_category == 'Poor')
                            <span class="inline-block mt-2 text-xs font-semibold text-red-600 bg-red-100 px-2 py-0.5 rounded">Category: Poor</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-10 text-gray-500 italic">
                    No complaints logged yet. Great job!
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ===== Recent Feedback: Who is Who ===== -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden mt-10">
    <div class="flex flex-wrap gap-3 px-6 pt-4 pb-2 text-xs font-semibold">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span> Excellent (Brand Promoter)</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Good (Satisfied)</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> Average (Passive)</span>
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span> Poor (Detractor)</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-xs uppercase font-bold tracking-wider">
                    <th class="px-5 py-3">#</th>
                    <th class="px-5 py-3">Customer</th>
                    <th class="px-5 py-3">Store</th>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3 text-center">NPS Score</th>
                    <th class="px-5 py-3 text-center">Overall Score</th>
                    <th class="px-5 py-3 text-center">NPS Type</th>
                    <th class="px-5 py-3 text-center">Tier</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentResponses as $i => $r)
                    @php
                        $tier    = $r->detailed_category ?? 'N/A';
                        $npsType = $r->nps_category    ?? 'N/A';

                        $rowBg = match($tier) {
                            'Excellent' => 'bg-green-50 hover:bg-green-100',
                            'Good'      => 'bg-blue-50 hover:bg-blue-100',
                            'Average'   => 'bg-yellow-50 hover:bg-yellow-100',
                            'Poor'      => 'bg-red-50 hover:bg-red-100',
                            default     => 'hover:bg-gray-50',
                        };

                        $tierBadge = match($tier) {
                            'Excellent' => 'bg-green-100 text-green-800',
                            'Good'      => 'bg-blue-100 text-blue-800',
                            'Average'   => 'bg-yellow-100 text-yellow-800',
                            'Poor'      => 'bg-red-100 text-red-800',
                            default     => 'bg-gray-100 text-gray-600',
                        };

                        $typeBadge = match($npsType) {
                            'Promoter'  => 'bg-emerald-100 text-emerald-800',
                            'Passive'   => 'bg-indigo-100 text-indigo-800',
                            'Detractor' => 'bg-rose-100 text-rose-800',
                            default     => 'bg-gray-100 text-gray-600',
                        };

                        $typeEmoji = match($npsType) {
                            'Promoter'  => '⭐',
                            'Passive'   => '😐',
                            'Detractor' => '⚠️',
                            default     => '',
                        };

                        $customerName = $r->survey->visit->customer->customer_name ?? '—';
                        $mobile       = $r->survey->visit->customer->mobile_no ?? '';
                        $storeName    = $r->survey->visit->store->store_name ?? '—';
                        $tag          = $r->survey->visit->customer->tag ?? '—';
                        $isVip        = $r->survey->visit->customer->is_vip ?? false;
                    @endphp
                    <tr class="{{ $rowBg }} transition-colors">
                        <td class="px-5 py-3 text-gray-400 font-medium">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            <div class="font-semibold text-gray-800">
                                {{ $customerName }}
                                @if($isVip)
                                    <span class="ml-1 text-xs bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded font-bold">VIP</span>
                                @endif
                            </div>
                            @if($mobile)
                                <div class="text-xs text-gray-500">{{ $mobile }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $storeName }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">
                            {{ $r->created_at ? $r->created_at->format('d M, H:i') : '—' }}<br>
                            <span class="text-gray-400">{{ $r->created_at ? $r->created_at->diffForHumans() : '' }}</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-2xl font-extrabold
                                @if($r->nps_score >= 9) text-green-600
                                @elseif($r->nps_score >= 7) text-blue-600
                                @else text-red-600 @endif">
                                {{ $r->nps_score }}
                            </span>
                            <span class="text-gray-400">/10</span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($r->overall_score)
                                <span class="font-bold text-gray-700">{{ $r->overall_score }}</span>
                                <span class="text-gray-400 text-xs">/100</span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $typeBadge }}">
                                {{ $typeEmoji }} {{ $npsType }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $tierBadge }}">
                                {{ $tier }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 italic">
                            No survey responses yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
