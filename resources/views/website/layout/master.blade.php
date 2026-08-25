<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ env('APP_NAME') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="icon" href="{{ asset('admin/assets/images/favicon1.png') }}" type="image/x-icon">
      @include('website.layout.partial.header')
      @yield('css')
      @yield('style')
     <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js">
      </script>
     <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ══════════════════════════════════════════
         Custom Toastr Design — Speckarts Brand
    ══════════════════════════════════════════ --}}
    <style>
        /* ── Container ── */
        #toast-container {
            top: 20px !important;
            right: 20px !important;
        }

        #toast-container > div {
            position: relative;
            padding: 14px 46px 14px 18px !important;
            border-radius: 14px !important;
            min-width: 280px !important;
            max-width: 340px !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18), 0 2px 8px rgba(34,161,165,0.10) !important;
            border: none !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            line-height: 1.45 !important;
            cursor: pointer;
            overflow: hidden;
            opacity: 1 !important;
            animation: toastSlideIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        }

        /* ── Slide-in animation ── */
        @keyframes toastSlideIn {
            from { transform: translateX(110%); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }

        /* ── Left accent bar ── */
        #toast-container > div::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            border-radius: 14px 0 0 14px;
        }

        /* ── Icon area ── */
        /* #toast-container > div .toast-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        } */

        /* ── Title ── */
        #toast-container > div .toast-title {
            font-weight: 700 !important;
            font-size: 13px !important;
            margin-bottom: 2px !important;
            letter-spacing: 0.2px;
        }

        /* ── Message ── */
        #toast-container > div .toast-message {
            font-size: 12.5px !important;
            opacity: 0.92;
        }

        /* ── Close button ── */
        #toast-container > div button.toast-close-button {
            position: absolute !important;
            top: 10px !important;
            right: 12px !important;
            font-size: 16px !important;
            font-weight: 400 !important;
            opacity: 0.55 !important;
            color: inherit !important;
            background: none !important;
            border: none !important;
            line-height: 1;
        }
        #toast-container > div button.toast-close-button:hover {
            opacity: 1 !important;
        }

        /* ── Progress bar ── */
        #toast-container > div .toast-progress {
            height: 3px !important;
            border-radius: 0 0 14px 14px !important;
            bottom: 0 !important;
            left: 0 !important;
        }

        /* ════════════════════════════
           SUCCESS  — Teal brand
        ════════════════════════════ */
        #toast-container .toast.toast-success {
            background-image: none !important;
        }
        #toast-container > .toast-success {
            background-color: #f0fafa !important;
            color: #0d4f52 !important;
            border-left: 4px solid #22a1a5 !important;
            border-radius: 14px !important;
        }
        #toast-container > .toast-success::before { background: #22a1a5; }
        #toast-container > .toast-success .toast-progress { background: #22a1a5 !important; }

        /* ════════════════════════════
           ERROR  — Warm red
        ════════════════════════════ */
        #toast-container > .toast-error {
            background: #ffe8e8 !important;
            color: #7a1a1a !important;
            border-left: 4px solid #e53e3e !important;
            border-radius: 14px !important;
        }
        #toast-container > .toast-error::before { background: #e53e3e; }
        #toast-container > .toast-error .toast-progress { background: #e53e3e !important; }

        /* ════════════════════════════
           WARNING  — Amber
        ════════════════════════════ */
        #toast-container > .toast-warning {
            background: #fef3c7 !important;
            color: #7a4f00 !important;
            border-left: 4px solid #f59e0b !important;
            border-radius: 14px !important;
        }
        #toast-container > .toast-warning::before { background: #f59e0b; }
        #toast-container > .toast-warning .toast-progress { background: #f59e0b !important; }

        /* ════════════════════════════
           INFO  — Navy brand
        ════════════════════════════ */
        #toast-container > .toast-info {
            background: linear-gradient(135deg, #f0f2ff 0%, #e2e6ff 100%) !important;
            color: #02045c !important;
            border-left: 4px solid #02045c !important;
            border-radius: 14px !important;
        }
        #toast-container > .toast-info::before { background: #02045c; }
        #toast-container > .toast-info .toast-progress { background: #02045c !important; }

        /* ── Mobile ── */
        @media (max-width: 480px) {
            #toast-container {
                top: 12px !important;
                right: 12px !important;
                left: 12px !important;
            }
            #toast-container > div {
                min-width: unset !important;
                width: 100% !important;
            }
        }
    </style>

</head>
<body>
 @include('website.layout.partial.sidebar')
    @yield('content')
 @include('website.layout.partial.footer')

<!-- Speckart AJAX Login Modal -->
@include('website.layout.partial.login-modal')

<!-- =================================================== -->

@include('website.layout.partial.scripts')

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
    toastr.options = {
        "closeButton":      true,
        "progressBar":      true,
        "positionClass":    "toast-top-right",
        "showDuration":     "250",
        "hideDuration":     "400",
        "timeOut":          "3500",
        "extendedTimeOut":  "1200",
        "showEasing":       "swing",
        "hideEasing":       "linear",
        "showMethod":       "fadeIn",
        "hideMethod":       "fadeOut",
        "tapToDismiss":     true,
        "newestOnTop":      true,
        "preventDuplicates": false,
    }
    @if(Session::has('success'))
        toastr.success("{{ Session::get('success') }}");
    @endif
    @if(Session::has('error'))
        toastr.error("{{ Session::get('error') }}");
    @endif
</script>
 @yield('scripts')
</body>
</html>