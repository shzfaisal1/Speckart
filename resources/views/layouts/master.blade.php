<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ env('APP_NAME') . ' | ' . $page_title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.partials.styles')
    <style>
    .app-content .side-app {
        padding: 0;
    }
    </style>
    @yield('styles')
</head>

<body class="app sidebar-mini">
    <!---Global-loader-->
    <div id="global-loader">
        <img src="{{ asset('assets/images/svgs/loader.svg') }}" alt=" loader">
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page comb-page">
        <div class="page-main">
            @include('layouts.partials.header')

            @include('layouts.partials.sidebar')

            <!-- main content area start -->
            <div class="app-content page-body">
                <div class="side-app pt-2 pl-0">
                    
                    <div class="page-header">
                        <div class="page-leftheader d-flex">
                            {{-- <h4 class="page-title">{{ $page_title }} | </h4> --}}
                            @if (@isset($breadcrumbs))
                            <ol class="breadcrumb pl-2 pt-3">
                                {{-- this will load breadcrumbs dynamically from controller --}}
                                @foreach ($breadcrumbs as $breadcrumb)
                                <li class="breadcrumb-item">
                                    @if (isset($breadcrumb['link']))
                                    <a
                                        href="{{ $breadcrumb['link'] == 'javascript:void(0)' ? $breadcrumb['link'] : url($breadcrumb['link']) }}">
                                        @endif
                                        {{ $breadcrumb['name'] }}
                                        @if (isset($breadcrumb['link']))
                                    </a>
                                    @endif
                                </li>
                                @endforeach
                            </ol>
                            @endisset

                        </div>
                    </div>

                    @yield('content')
                </div>
            </div>
        </div>
        <!-- main content area end -->
        @include('layouts.partials.footer')
    </div>
    <!-- page container area end -->
    <!-- Back to top -->
    <a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>
    @include('layouts.partials.scripts')
    @yield('scripts')
</body>

</html>