<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') - NexTrade Industries</title>
    <meta name="X-CSRF-TOKEN" content="{{ csrf_token() }}">
    {{-- jquery --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js">
    <script src="https://cdn.ckeditor.com/ckeditor5/12.0.0/classic/ckeditor.js"></script></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">

    @include('admin.partials.links')

    @yield('extra-links')

    @yield('style')

</head>

<body class="  ">
    <!-- loader -->
    <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
    </div>

    @include('admin.partials.sidebar')

    <main class="main-content">
        <div class="position-relative iq-banner">
            @include('admin.partials.header')
        </div>

        @yield('content')

        @include('admin.partials.footer')
    </main>

    @include('admin.partials.scripts')

    {{-- global script --}}
    <script>

        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.error("{{ session('error') }}");
        @endif
        @if (Session::has('success'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.success("{{ session('success') }}");
        @endif


        $('.select2').select2();

    </script>
    @yield('script')
</body>

</html>
