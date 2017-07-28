<!DOCTYPE html>
<html>
<head>
    <title>assistant - @yield('title')</title>
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.js"></script>
    <script>
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });
    </script>
    @yield('style')
</head>
<body>
    @yield('content')
    @yield('script')
</body>
</html>
