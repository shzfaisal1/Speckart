<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ env('APP_NAME') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('admin/assets/images/favicon1.png') }}" type="image/x-icon">
      @include('web.layout.partial.header')
     <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js">
      </script>
</head>
<body>
 @include('web.layout.partial.sidebar')
    @yield('content')
 @include('web.layout.partial.footer')
<!-- =================================================== -->

@include('web.layout.partial.scripts')

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
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